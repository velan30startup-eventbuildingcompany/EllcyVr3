<?php
$page_title  = 'All Services';
$active_page = 'services';
require VIEWS_PATH . '/admin/layout_start.php';
?>

<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title"><i class="fa-solid fa-layer-group" style="color:#6a1b9a;margin-right:8px"></i>Services</div>
    <a class="btn btn-primary" href="<?= APP_URL ?>/admin/services/create">
      <i class="fa-solid fa-plus"></i> Add Service
    </a>
  </div>
  <div class="filter-bar">
    <input class="filter-search" type="text" id="svcSearch" placeholder="Search by title, category…" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"/>
    <select class="form-select" style="width:auto;padding:8px 13px" id="svcStatus" onchange="applyFilters()">
      <option value="">All Status</option>
      <option value="active"   <?= ($_GET['status']??'')==='active'?'selected':'' ?>>Active</option>
      <option value="inactive" <?= ($_GET['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
      <option value="draft"    <?= ($_GET['status']??'')==='draft'?'selected':'' ?>>Draft</option>
    </select>
    <button class="btn btn-outline btn-sm" onclick="applyFilters()">Filter</button>
  </div>
  <div style="overflow-x:auto">
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:52px">Image</th>
        <th>Title</th>
        <th>Category</th>
        <th>Price</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($services as $svc): ?>
      <tr>
        <td>
          <img src="<?= htmlspecialchars($svc['image'] ?: '/public/uploads/services/stage.png') ?>"
               alt="" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid #e0d5f0"/>
        </td>
        <td>
          <div style="font-weight:700;font-size:.88rem"><?= htmlspecialchars($svc['title']) ?></div>
          <div style="font-size:.75rem;color:#888"><?= htmlspecialchars($svc['slug']) ?></div>
        </td>
        <td style="font-size:.83rem"><?= htmlspecialchars($svc['category_name'] ?? '—') ?></td>
        <td style="font-weight:700;color:#6a1b9a">
          <?= $svc['price'] > 0 ? '₹' . number_format($svc['price']) : '—' ?>
        </td>
        <td>
          <span class="badge badge-<?= $svc['status']==='active'?'green':($svc['status']==='draft'?'amber':'red') ?>">
            <?= ucfirst(htmlspecialchars($svc['status'])) ?>
          </span>
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <a class="btn btn-sm btn-outline" href="<?= APP_URL ?>/admin/services/edit/<?= (int)$svc['id'] ?>">
              <i class="fa-solid fa-pen"></i>
            </a>
            <button class="btn btn-sm btn-danger" onclick="deleteService(<?= (int)$svc['id'] ?>, '<?= htmlspecialchars(addslashes($svc['title'])) ?>')">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($services)): ?>
      <tr><td colspan="6" style="text-align:center;color:#aaa;padding:32px">No services found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

  <!-- PAGINATION -->
  <?php if ($total_pages > 1): ?>
  <div class="pagination">
    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
    <a href="?page=<?= $p ?>&q=<?= urlencode($_GET['q']??'') ?>&status=<?= urlencode($_GET['status']??'') ?>"
       class="page-btn <?= $p === $current_page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php
$extra_admin_js = <<<'JS'
function applyFilters(){
  var q = document.getElementById('svcSearch').value;
  var s = document.getElementById('svcStatus').value;
  window.location.href = window.location.pathname + '?q=' + encodeURIComponent(q) + '&status=' + encodeURIComponent(s);
}
document.getElementById('svcSearch').addEventListener('keydown', function(e){ if(e.key==='Enter') applyFilters(); });

function deleteService(id, title){
  if(!confirm('Delete "' + title + '"?\n\nThis will mark the service as inactive.')) return;
  fetch(window.ELLCY_BASE + '/admin/services/delete/' + id, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'csrf_token=' + encodeURIComponent(document.querySelector('meta[name=csrf]')?.content || '')
  })
  .then(r=>r.json())
  .then(d=>{ if(d.success){ showNotif('Service deleted.','success'); setTimeout(()=>location.reload(),1000); } else showNotif(d.message,'error'); })
  .catch(()=>showNotif('Error deleting service.','error'));
}
JS;
require VIEWS_PATH . '/admin/layout_end.php';
?>
