<?php
$page_title  = 'Categories';
$active_page = 'categories';
require VIEWS_PATH . '/admin/layout_start.php';
?>

<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title"><i class="fa-solid fa-sitemap" style="color:#6a1b9a;margin-right:8px"></i>Categories</div>
    <a class="btn btn-primary" href="<?= APP_URL ?>/admin/categories/create">
      <i class="fa-solid fa-plus"></i> Add Category
    </a>
  </div>
  <div style="overflow-x:auto">
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:52px">Image</th>
        <th>Name</th>
        <th>Parent</th>
        <th>Services</th>
        <th>Sort</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
      <tr>
        <td>
          <img src="<?= htmlspecialchars($cat['image'] ?: '/public/uploads/services/stage.webp') ?>"
               alt="" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid #e0d5f0"/>
        </td>
        <td>
          <div style="font-weight:700;font-size:.88rem"><?= htmlspecialchars($cat['name']) ?></div>
          <div style="font-size:.75rem;color:#888"><?= htmlspecialchars($cat['slug']) ?></div>
        </td>
        <td style="font-size:.83rem"><?= htmlspecialchars($cat['parent_name'] ?? '—') ?></td>
        <td style="font-size:.83rem"><?= (int)$cat['service_count'] ?></td>
        <td style="font-size:.83rem"><?= (int)$cat['sort_order'] ?></td>
        <td>
          <span class="badge badge-<?= $cat['status']==='active'?'green':'red' ?>">
            <?= ucfirst(htmlspecialchars($cat['status'])) ?>
          </span>
          <?php if ($cat['hidden']): ?><span class="badge badge-amber" title="Hidden from the public category grid">Hidden</span><?php endif; ?>
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <a class="btn btn-sm btn-outline" href="<?= APP_URL ?>/admin/categories/edit/<?= (int)$cat['id'] ?>">
              <i class="fa-solid fa-pen"></i>
            </a>
            <button class="btn btn-sm btn-danger" onclick="deleteCategory(<?= (int)$cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>')">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($categories)): ?>
      <tr><td colspan="7" style="text-align:center;color:#aaa;padding:32px">No categories found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<?php
$extra_admin_js = <<<'JS'
function deleteCategory(id, name){
  if(!confirm('Delete "' + name + '"?\n\nThis will mark the category as inactive — services in it are not deleted.')) return;
  fetch(window.ELLCY_BASE + '/admin/categories/delete/' + id, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'csrf_token=' + encodeURIComponent(document.querySelector('meta[name=csrf]')?.content || '')
  })
  .then(r=>r.json())
  .then(d=>{ if(d.success){ showNotif('Category deleted.','success'); setTimeout(()=>location.reload(),1000); } else showNotif(d.message,'error'); })
  .catch(()=>showNotif('Error deleting category.','error'));
}
JS;
require VIEWS_PATH . '/admin/layout_end.php';
?>
