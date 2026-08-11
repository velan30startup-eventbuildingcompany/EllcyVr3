<?php
$page_title  = 'Call Requests';
$active_page = 'requests';
require VIEWS_PATH . '/admin/layout_start.php';
?>

<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title">
      <i class="fa-solid fa-phone-volume" style="color:#6a1b9a;margin-right:8px"></i>Request for Call
    </div>
    <div style="display:flex;gap:8px">
      <span class="badge badge-amber"><?= (int)($status_counts['new']??0) ?> New</span>
      <span class="badge badge-blue"><?= (int)($status_counts['called']??0) ?> Called</span>
      <span class="badge badge-green"><?= (int)($status_counts['completed']??0) ?> Completed</span>
    </div>
  </div>

  <div class="filter-bar">
    <input class="filter-search" type="text" id="rfcSearch"
           placeholder="Search by phone or service…"
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"/>
    <select class="form-select" style="width:auto;padding:8px 13px" id="rfcStatus" onchange="applyFilters()">
      <option value="">All Status</option>
      <option value="new"       <?= ($_GET['status']??'')==='new'      ?'selected':'' ?>>New</option>
      <option value="called"    <?= ($_GET['status']??'')==='called'   ?'selected':'' ?>>Called</option>
      <option value="completed" <?= ($_GET['status']??'')==='completed'?'selected':'' ?>>Completed</option>
      <option value="spam"      <?= ($_GET['status']??'')==='spam'     ?'selected':'' ?>>Spam</option>
    </select>
    <button class="btn btn-outline btn-sm" onclick="applyFilters()">Filter</button>
  </div>

  <div style="overflow-x:auto">
  <table class="data-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Phone</th>
        <th>Service</th>
        <th>Best Time</th>
        <th>Note</th>
        <th>Reference</th>
        <th>Status</th>
        <th>Received</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $r): ?>
      <tr>
        <td style="color:#aaa;font-size:.8rem"><?= (int)$r['id'] ?></td>
        <td>
          <a href="tel:<?= htmlspecialchars($r['phone']) ?>"
             style="font-weight:700;color:#6a1b9a;text-decoration:none">
            <?= htmlspecialchars($r['phone']) ?>
          </a>
        </td>
        <td style="font-size:.82rem">
          <?= htmlspecialchars(mb_strimwidth($r['service'] ?? '—', 0, 28, '…')) ?>
        </td>
        <td style="font-size:.82rem"><?= htmlspecialchars($r['best_time'] ?: '—') ?></td>
        <td style="font-size:.78rem;color:#888;max-width:160px">
          <?= htmlspecialchars(mb_strimwidth($r['note'] ?? '', 0, 40, '…')) ?>
        </td>
        <td>
          <?php foreach (($r['reference_uploads'] ?? []) as $upload): ?>
          <a href="<?= htmlspecialchars(APP_URL . $upload['path']) ?>" target="_blank" rel="noopener">
            <img src="<?= htmlspecialchars(APP_URL . $upload['path']) ?>" alt="Jewellery reference" style="width:48px;height:48px;object-fit:cover;border-radius:7px;border:1px solid #e0d5f0"/>
          </a>
          <?php endforeach; ?>
          <?php if (empty($r['reference_uploads'])): ?><span style="color:#aaa">—</span><?php endif; ?>
        </td>
        <td>
          <span class="badge badge-<?= match($r['status']){
            'called'=>'blue','completed'=>'green','spam'=>'red',default=>'amber'
          } ?>">
            <?= ucfirst(htmlspecialchars($r['status'])) ?>
          </span>
        </td>
        <td style="font-size:.78rem;color:#888;white-space:nowrap">
          <?= date('d M y, H:i', strtotime($r['created_at'])) ?>
        </td>
        <td>
          <div style="display:flex;gap:5px">
            <button class="btn btn-sm btn-success"
                    onclick="updateStatus(<?= (int)$r['id'] ?>,'called')"
                    title="Mark Called">
              <i class="fa-solid fa-phone"></i>
            </button>
            <button class="btn btn-sm btn-outline"
                    onclick="updateStatus(<?= (int)$r['id'] ?>,'completed')"
                    title="Mark Completed">
              <i class="fa-solid fa-check"></i>
            </button>
            <button class="btn btn-sm btn-danger"
                    onclick="updateStatus(<?= (int)$r['id'] ?>,'spam')"
                    title="Mark Spam">
              <i class="fa-solid fa-ban"></i>
            </button>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($requests)): ?>
      <tr><td colspan="9" style="text-align:center;color:#aaa;padding:32px">No call requests found.</td></tr>
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
  var q = document.getElementById('rfcSearch').value;
  var s = document.getElementById('rfcStatus').value;
  window.location.href = window.location.pathname + '?q=' + encodeURIComponent(q) + '&status=' + encodeURIComponent(s);
}
document.getElementById('rfcSearch').addEventListener('keydown',function(e){if(e.key==='Enter')applyFilters();});

function updateStatus(id, status){
  fetch(window.ELLCY_BASE + '/admin/requests/update/'+id,{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'status='+encodeURIComponent(status)+'&csrf_token={$csrf}'
  })
  .then(function(r){return r.json();})
  .then(function(d){
    if(d.success){showNotif('Status updated to '+status,'success');setTimeout(function(){location.reload();},900);}
    else showNotif(d.message||'Error','error');
  });
}
JS;
require VIEWS_PATH . '/admin/layout_end.php';
?>
