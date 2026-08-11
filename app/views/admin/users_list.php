<?php
$page_title  = 'Users';
$active_page = 'users';
require VIEWS_PATH . '/admin/layout_start.php';
?>

<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title">
      <i class="fa-solid fa-users" style="color:#6a1b9a;margin-right:8px"></i>Users
    </div>
    <div style="display:flex;gap:8px">
      <span class="badge badge-green"><?= (int)($user_stats['active']??0) ?> Active</span>
      <span class="badge badge-amber"><?= (int)($user_stats['inactive']??0) ?> Inactive</span>
      <span class="badge badge-red"><?= (int)($user_stats['banned']??0) ?> Banned</span>
    </div>
  </div>

  <div class="filter-bar">
    <input class="filter-search" type="text" id="userSearch"
           placeholder="Search by name, email or phone…"
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"/>
    <select class="form-select" style="width:auto;padding:8px 13px" id="userStatus" onchange="applyFilters()">
      <option value="">All Status</option>
      <option value="active"   <?= ($_GET['status']??'')==='active'   ?'selected':'' ?>>Active</option>
      <option value="inactive" <?= ($_GET['status']??'')==='inactive' ?'selected':'' ?>>Inactive</option>
      <option value="banned"   <?= ($_GET['status']??'')==='banned'   ?'selected':'' ?>>Banned</option>
    </select>
    <button class="btn btn-outline btn-sm" onclick="applyFilters()">Filter</button>
  </div>

  <div style="overflow-x:auto">
  <table class="data-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Role</th>
        <th>Last Login</th>
        <th>Joined</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td style="color:#aaa;font-size:.8rem"><?= (int)$u['id'] ?></td>
        <td style="font-weight:700;font-size:.85rem"><?= htmlspecialchars($u['name']) ?></td>
        <td style="font-size:.82rem"><?= htmlspecialchars($u['email']) ?></td>
        <td style="font-size:.82rem"><?= htmlspecialchars($u['phone'] ?: '—') ?></td>
        <td style="font-size:.82rem"><?= ucfirst(htmlspecialchars($u['role'])) ?></td>
        <td style="font-size:.78rem;color:#888;white-space:nowrap">
          <?= $u['last_login'] ? date('d M y, H:i', strtotime($u['last_login'])) : 'Never' ?>
        </td>
        <td style="font-size:.78rem;color:#888;white-space:nowrap">
          <?= date('d M y', strtotime($u['created_at'])) ?>
        </td>
        <td>
          <span class="badge badge-<?= match($u['status']){
            'active'=>'green','banned'=>'red',default=>'amber'
          } ?>">
            <?= ucfirst(htmlspecialchars($u['status'])) ?>
          </span>
        </td>
        <td>
          <div style="display:flex;gap:5px">
            <?php if ($u['status'] !== 'active'): ?>
            <button class="btn btn-sm btn-success" onclick="setStatus(<?= (int)$u['id'] ?>,'active')" title="Activate">
              <i class="fa-solid fa-check"></i>
            </button>
            <?php endif; ?>
            <?php if ($u['status'] !== 'inactive'): ?>
            <button class="btn btn-sm btn-outline" onclick="setStatus(<?= (int)$u['id'] ?>,'inactive')" title="Deactivate">
              <i class="fa-solid fa-pause"></i>
            </button>
            <?php endif; ?>
            <?php if ($u['status'] !== 'banned'): ?>
            <button class="btn btn-sm btn-danger" onclick="setStatus(<?= (int)$u['id'] ?>,'banned')" title="Ban">
              <i class="fa-solid fa-ban"></i>
            </button>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($users)): ?>
      <tr><td colspan="9" style="text-align:center;color:#aaa;padding:32px">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

  <?php if ($total_pages > 1): ?>
  <div class="pagination">
    <?php for ($p=1;$p<=$total_pages;$p++): ?>
    <a href="?page=<?=$p?>&q=<?=urlencode($_GET['q']??'')?>&status=<?=urlencode($_GET['status']??'')?>"
       class="page-btn <?=$p===$current_page?'active':''?>"><?=$p?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php
$csrf = Security::csrfToken();
$extra_admin_js = <<<JS
function applyFilters(){
  var q = document.getElementById('userSearch').value;
  var s = document.getElementById('userStatus').value;
  window.location.href = window.location.pathname + '?q=' + encodeURIComponent(q) + '&status=' + encodeURIComponent(s);
}
document.getElementById('userSearch').addEventListener('keydown',function(e){if(e.key==='Enter')applyFilters();});

function setStatus(id, status){
  if(status === 'banned' && !confirm('Ban this user? They will not be able to log in.')) return;
  fetch(window.ELLCY_BASE + '/admin/users/status/'+id,{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'status='+encodeURIComponent(status)+'&csrf_token={$csrf}'
  })
  .then(function(r){return r.json();})
  .then(function(d){
    if(d.success){showNotif('User status updated to '+status,'success');setTimeout(function(){location.reload();},900);}
    else showNotif(d.message||'Error','error');
  })
  .catch(function(){showNotif('Error updating status.','error');});
}
JS;
require VIEWS_PATH . '/admin/layout_end.php';
?>
