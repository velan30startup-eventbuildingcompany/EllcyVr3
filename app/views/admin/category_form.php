<?php
$page_title  = $category ? 'Edit Category' : 'Add Category';
$active_page = 'categories';
require VIEWS_PATH . '/admin/layout_start.php';
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
?>

<div class="data-card" style="max-width:640px">
  <div class="data-card-hdr">
    <div class="data-card-title">
      <i class="fa-solid fa-sitemap" style="color:#6a1b9a;margin-right:8px"></i>
      <?= $category ? 'Edit Category' : 'Add Category' ?>
    </div>
    <a class="btn btn-outline" href="<?= APP_URL ?>/admin/categories"><i class="fa-solid fa-arrow-left"></i> Back</a>
  </div>

  <?php if ($flash): ?>
  <div class="badge badge-<?= $flash['type']==='success'?'green':'red' ?>" style="display:block;padding:10px 14px;margin-bottom:18px;font-size:.85rem">
    <?= htmlspecialchars($flash['msg']) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

    <div class="form-group">
      <label class="form-label">Name *</label>
      <input type="text" name="name" class="form-input" required value="<?= htmlspecialchars($category['name'] ?? '') ?>" placeholder="e.g. Event Location"/>
    </div>

    <div class="form-group">
      <label class="form-label">Slug</label>
      <input type="text" name="slug" class="form-input" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" placeholder="auto-generated from name if left blank"/>
      <div style="font-size:.78rem;color:#888;margin-top:4px">Used in URLs (e.g. <code>services.html?type=your-slug</code>). Leave blank to auto-generate.</div>
    </div>

    <div class="form-group">
      <label class="form-label">Parent Category</label>
      <select name="parent_id" class="form-select">
        <option value="">— None (top-level) —</option>
        <?php foreach (($parents ?? []) as $p): ?>
        <option value="<?= (int)$p['id'] ?>" <?= (isset($category) && (int)($category['parent_id'] ?? 0) === (int)$p['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($p['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
      <div style="font-size:.78rem;color:#888;margin-top:4px">Only needed for a sub-category (e.g. Breakfast under Food Services).</div>
    </div>

    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-textarea" style="min-height:80px" placeholder="Optional — shown on the category listing page"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Category Image</label>
      <?php if (!empty($category['image'])): ?>
      <img src="<?= htmlspecialchars($category['image']) ?>" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:10px;border:1px solid #e0d5f0;display:block;margin-bottom:10px"/>
      <?php endif; ?>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="form-input"/>
      <div style="font-size:.78rem;color:#888;margin-top:4px">JPG, PNG, WebP or GIF. Leave blank to keep the current image.</div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-input" value="<?= (int)($category['sort_order'] ?? 0) ?>"/>
        <div style="font-size:.78rem;color:#888;margin-top:4px">Lower numbers show first.</div>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active"   <?= ($category['status'] ?? 'active')==='active'   ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= ($category['status'] ?? '')==='inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label style="display:flex;align-items:center;gap:8px;font-size:.85rem;font-weight:600;cursor:pointer">
        <input type="checkbox" name="hidden" value="1" <?= !empty($category['hidden']) ? 'checked' : '' ?>/>
        Hidden from the public category grid (kept for internal grouping only)
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">
      <i class="fa-solid fa-check"></i> <?= $category ? 'Save Changes' : 'Create Category' ?>
    </button>
  </form>
</div>

<?php require VIEWS_PATH . '/admin/layout_end.php'; ?>
