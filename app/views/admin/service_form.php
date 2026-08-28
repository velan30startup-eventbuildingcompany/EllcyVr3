<?php
$is_edit     = !empty($service);
$page_title  = $is_edit ? 'Edit Service' : 'Add New Service';
$active_page = 'services';
require VIEWS_PATH . '/admin/layout_start.php';
$s = $service ?? [];
?>

<div class="service-form-shell">
<form id="svcForm" method="POST" enctype="multipart/form-data" action="">
  <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

  <div class="service-form-grid">

    <!-- LEFT COLUMN -->
    <div>
      <div class="data-card" style="padding:24px;margin-bottom:24px">
        <div style="font-size:.9rem;font-weight:700;margin-bottom:18px;color:#1a1a2e">Basic Information</div>
        <div class="form-group">
          <label class="form-label">Service Title <span style="color:#dc2626">*</span></label>
          <input type="text" name="title" class="form-input" required
                 value="<?= htmlspecialchars($s['title'] ?? '') ?>"
                 placeholder="e.g. DJ – Premium Package"/>
        </div>
        <div class="form-group">
          <label class="form-label">Slug (URL-friendly)</label>
          <input type="text" name="slug" class="form-input" id="slugField"
                 value="<?= htmlspecialchars($s['slug'] ?? '') ?>"
                 placeholder="auto-generated from title"/>
          <div style="font-size:.75rem;color:#888;margin-top:4px">Leave blank to auto-generate</div>
        </div>
        <div class="form-group">
          <label class="form-label">Short Description</label>
          <input type="text" name="short_description" class="form-input" maxlength="500"
                 value="<?= htmlspecialchars($s['short_description'] ?? '') ?>"
                 placeholder="One-line service summary shown on listing cards"/>
        </div>
        <div class="form-group">
          <label class="form-label">Full Description</label>
          <textarea name="description" class="form-textarea" style="min-height:150px"
                    placeholder="Detailed service description…"><?= htmlspecialchars($s['description'] ?? '') ?></textarea>
        </div>
        <div class="admin-form-pair">
          <div class="form-group">
            <label class="form-label">Tags</label>
            <input type="text" name="tags" class="form-input"
                   value="<?= htmlspecialchars($s['tags'] ?? '') ?>"
                   placeholder="DJ | Music | Bollywood"/>
          </div>
          <div class="form-group">
            <label class="form-label">Availability</label>
            <input type="text" name="availability" class="form-input"
                   value="<?= htmlspecialchars($s['availability'] ?? 'Available All Year') ?>"/>
          </div>
        </div>
      </div>

      <div class="data-card" style="padding:24px;margin-bottom:24px">
        <div style="font-size:.9rem;font-weight:700;margin-bottom:18px">SEO</div>
        <div class="form-group">
          <label class="form-label">Meta Title</label>
          <input type="text" name="meta_title" class="form-input" maxlength="200"
                 value="<?= htmlspecialchars($s['meta_title'] ?? '') ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Meta Description</label>
          <textarea name="meta_description" class="form-textarea" style="min-height:80px" maxlength="500"
                    placeholder="SEO description for search engines…"><?= htmlspecialchars($s['meta_description'] ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div>
      <div class="data-card" style="padding:24px;margin-bottom:24px">
        <div style="font-size:.9rem;font-weight:700;margin-bottom:18px">Settings</div>
        <div class="form-group">
          <label class="form-label">Category <span style="color:#dc2626">*</span></label>
          <select name="category_id" class="form-select" required>
            <option value="">Select…</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>"
                    <?= ($s['category_id']??'')==$cat['id']?'selected':'' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Page Template</label>
          <select name="page_template" class="form-select">
            <?php foreach (['sd'=>'SD (Standard)','cm'=>'CM (Chenda/Dancer)','snk'=>'SNK (Snacks)','bnc'=>'BNC (Bouncer/Catering)','custom'=>'Custom'] as $val=>$lbl): ?>
            <option value="<?= $val ?>" <?= ($s['page_template']??'sd')===$val?'selected':'' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Base Price (₹)</label>
          <input type="number" name="price" class="form-input" min="0" step="0.01"
                 value="<?= htmlspecialchars($s['price'] ?? 0) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Price Unit</label>
          <input type="text" name="price_unit" class="form-input"
                 value="<?= htmlspecialchars($s['price_unit'] ?? '') ?>"
                 placeholder="/ person, / day…"/>
        </div>
        <div class="form-group">
          <label class="form-label">Rating (0–5)</label>
          <input type="number" name="rating" class="form-input" min="0" max="5" step="0.1"
                 value="<?= htmlspecialchars($s['rating'] ?? 4.5) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-input" min="0"
                 value="<?= htmlspecialchars($s['sort_order'] ?? 0) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="active"   <?= ($s['status']??'active')==='active'?'selected':'' ?>>Active</option>
            <option value="inactive" <?= ($s['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
            <option value="draft"    <?= ($s['status']??'')==='draft'?'selected':'' ?>>Draft</option>
          </select>
        </div>
        <div class="form-group">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.85rem;font-weight:600">
            <input type="checkbox" name="featured" value="1" <?= !empty($s['featured'])?'checked':'' ?>
                   style="width:16px;height:16px;accent-color:#6a1b9a"/>
            Featured on homepage
          </label>
        </div>
      </div>

      <div class="data-card" style="padding:24px;margin-bottom:24px">
        <div style="font-size:.9rem;font-weight:700;margin-bottom:18px">Service Image</div>
        <?php if (!empty($s['image'])): ?>
        <?php $serviceImagePreview = preg_match('#^https?://#i', (string)$s['image']) ? $s['image'] : APP_URL . '/' . ltrim((string)$s['image'], '/'); ?>
        <img src="<?= htmlspecialchars($serviceImagePreview) ?>" alt="Current service image" style="width:100%;border-radius:10px;margin-bottom:14px;object-fit:cover;height:140px;border:1px solid #e0d5f0"/>
        <?php endif; ?>
        <input type="file" name="image" class="form-input" accept="image/*" style="padding:8px"/>
        <div style="font-size:.75rem;color:#888;margin-top:4px">JPG, PNG, WebP · Max 5 MB. Used as the public fallback and listing image.</div>
      </div>

      <?php if ($is_edit && !empty($packages)): ?>
      <div class="data-card" style="padding:24px;margin-bottom:24px">
        <div style="font-size:.9rem;font-weight:700;margin-bottom:6px">Packages and Pricing</div>
        <div style="font-size:.78rem;color:#888;margin-bottom:16px">Package IDs are permanent. Names, unique routes, prices, descriptions and inclusions are stored centrally here.</div>
        <?php foreach ($packages as $package): ?>
        <details style="border:1px solid #e7dff0;border-radius:10px;padding:12px;margin-bottom:10px" <?= !empty($package['is_default']) ? 'open' : '' ?>>
          <summary style="cursor:pointer;font-weight:700"><?= htmlspecialchars($package['label']) ?> · ₹<?= number_format((float)$package['price']) ?></summary>
          <div class="admin-form-pair admin-package-grid">
            <div class="form-group"><label class="form-label">Package ID</label><input class="form-input" value="<?= (int)$package['id'] ?>" disabled/></div>
            <div class="form-group"><label class="form-label">Price</label><input type="number" min="0" step="0.01" class="form-input" name="packages[<?= (int)$package['id'] ?>][price]" value="<?= htmlspecialchars($package['price']) ?>"/></div>
            <div class="form-group"><label class="form-label">Name</label><input class="form-input" name="packages[<?= (int)$package['id'] ?>][label]" value="<?= htmlspecialchars($package['label']) ?>"/></div>
            <div class="form-group"><label class="form-label">Unique route slug</label><input class="form-input" name="packages[<?= (int)$package['id'] ?>][slug]" value="<?= htmlspecialchars($package['slug'] ?? '') ?>"/></div>
            <div class="form-group" style="grid-column:1/-1"><label class="form-label">Description</label><textarea class="form-textarea" name="packages[<?= (int)$package['id'] ?>][description]" rows="2"><?= htmlspecialchars($package['description'] ?? '') ?></textarea></div>
            <div class="form-group" style="grid-column:1/-1"><label class="form-label">Inclusions (one per line)</label><textarea class="form-textarea" name="packages[<?= (int)$package['id'] ?>][inclusions]" rows="4"><?php $inc=json_decode($package['inclusions_json'] ?? '[]',true); echo htmlspecialchars(implode("\n",is_array($inc)?$inc:[])); ?></textarea></div>
            <div class="form-group"><label class="form-label">Status</label><select class="form-select" name="packages[<?= (int)$package['id'] ?>][status]"><option value="active" <?= ($package['status']??'active')==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= ($package['status']??'')==='inactive'?'selected':'' ?>>Inactive</option></select></div>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($is_edit): ?>
      <div class="data-card" style="padding:24px;margin-bottom:24px">
        <div style="font-size:.9rem;font-weight:700;margin-bottom:6px">Primary Media</div>
        <div style="font-size:.78rem;color:#888;margin-bottom:16px">
          Every service description page shows up to 2 images and 1 video. Manage them here —
          no need to touch the database directly. "Replace" swaps the file in place; "Delete" clears the slot.
        </div>

        <?php
          // Derive Image 1 / Image 2 / Video slots from the same $gallery
          // data used by the classic gallery grid below (ordered by
          // sort_order, id — see AdminController::serviceEdit()).
          $imgSlots = array_values(array_filter($gallery, fn($g) => $g['media_type'] !== 'video'));
          $vidSlot  = null;
          foreach ($gallery as $g) { if ($g['media_type'] === 'video') { $vidSlot = $g; break; } }
          $slot1 = $imgSlots[0] ?? null;
          $slot2 = $imgSlots[1] ?? null;
          $isDecoration = in_array($s['category_slug'] ?? '', ['stage-decoration','light-decoration'], true);
          $slotDefinitions = [];
          $imageSlotCount = $isDecoration ? 5 : 2;
          for ($slotIndex = 0; $slotIndex < $imageSlotCount; $slotIndex++) {
              $slotDefinitions[] = [
                  'label' => 'Image ' . ($slotIndex + 1),
                  'key' => 'slot' . ($slotIndex + 1),
                  'item' => $imgSlots[$slotIndex] ?? null,
                  'video' => false,
              ];
          }
          $slotDefinitions[] = ['label'=>'Service Video','key'=>'slotVideo','item'=>$vidSlot,'video'=>true];
        ?>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:8px">
          <?php foreach ($slotDefinitions as $slot): ?>
          <div class="media-slot" data-slot="<?= $slot['key'] ?>" data-img-id="<?= $slot['item'] ? (int)$slot['item']['id'] : '' ?>"
               style="border:1.5px solid #eee;border-radius:12px;padding:12px;text-align:center">
            <div style="font-size:.78rem;font-weight:700;color:#1a1a2e;margin-bottom:8px"><?= $slot['label'] ?></div>
            <div class="media-slot-preview" style="width:100%;aspect-ratio:1;border-radius:8px;overflow:hidden;background:#f4e9ff;
                 display:flex;align-items:center;justify-content:center;margin-bottom:10px;position:relative">
              <?php if ($slot['item']): ?>
                <?php if ($slot['video']): ?>
                  <?php if (!empty($slot['item']['video_provider'])): ?>
                    <div style="color:#6a1b9a;display:flex;flex-direction:column;align-items:center;gap:4px">
                      <i class="fa-solid fa-circle-play" style="font-size:1.8rem"></i>
                      <span style="font-size:.65rem;font-weight:700"><?= ucfirst($slot['item']['video_provider']) ?></span>
                    </div>
                  <?php else: ?>
                    <video src="<?= htmlspecialchars(APP_URL . $slot['item']['path']) ?>" style="width:100%;height:100%;object-fit:cover" controls></video>
                  <?php endif; ?>
                <?php else: ?>
                  <img src="<?= htmlspecialchars(APP_URL . $slot['item']['path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover"/>
                <?php endif; ?>
              <?php else: ?>
                <div style="color:#c9b8e0;display:flex;flex-direction:column;align-items:center;gap:4px">
                  <i class="fa-solid fa-<?= $slot['video']?'video':'image' ?>" style="font-size:1.6rem"></i>
                  <span style="font-size:.68rem;font-weight:600">Empty</span>
                </div>
              <?php endif; ?>
            </div>
            <input type="file" class="media-slot-file" data-slot="<?= $slot['key'] ?>" style="display:none"
                   accept="<?= $slot['video'] ? 'video/mp4,video/webm,video/quicktime' : 'image/jpeg,image/png,image/webp,image/gif' ?>"/>
            <div style="display:flex;gap:6px;justify-content:center">
              <button type="button" class="btn btn-sm btn-outline media-slot-replace" data-slot="<?= $slot['key'] ?>" style="flex:1">
                <i class="fa-solid fa-rotate"></i> Replace
              </button>
              <?php if ($slot['item']): ?>
              <button type="button" class="btn btn-sm btn-outline media-slot-delete" data-slot="<?= $slot['key'] ?>"
                      data-img-id="<?= (int)$slot['item']['id'] ?>" style="color:#c0392b;border-color:#f5c6c6">
                <i class="fa-solid fa-trash"></i>
              </button>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div id="mediaSlotMsg" style="margin-top:6px;font-size:.78rem;display:none"></div>
      </div>

      <div class="data-card" style="padding:24px;margin-bottom:24px">
        <div style="font-size:.9rem;font-weight:700;margin-bottom:6px">Additional Gallery Items</div>
        <div style="font-size:.78rem;color:#888;margin-bottom:16px">
          Optional extra photos/videos beyond the primary Image 1 / Image 2 / Video above — useful for
          service categories (like Stage Decoration) that pool media across several sub-services.
        </div>

        <p style="font-size:.78rem;color:#888;margin:-8px 0 12px">
          The public site now shows <strong>only one</strong> image or video per category —
          whichever item below is marked <i class="fa-solid fa-star" style="color:#f5b400"></i> Primary.
          Click the star on any item to make it the one shown.
        </p>
        <div id="galleryGrid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:16px">
          <?php foreach ($gallery as $g): ?>
          <div class="gallery-item" data-img-id="<?= (int)$g['id'] ?>" style="position:relative;border-radius:8px;overflow:hidden;aspect-ratio:1;background:#f4e9ff;<?= !empty($g['is_primary']) ? 'outline:2.5px solid #f5b400' : '' ?>">
            <?php if ($g['media_type'] === 'video'): ?>
              <?php if (!empty($g['video_provider'])): ?>
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#6a1b9a;flex-direction:column;gap:4px">
                <i class="fa-solid fa-play-circle" style="font-size:1.6rem"></i>
                <span style="font-size:.65rem;font-weight:700"><?= ucfirst($g['video_provider']) ?></span>
              </div>
              <?php else: ?>
              <video src="<?= htmlspecialchars(APP_URL . $g['path']) ?>" style="width:100%;height:100%;object-fit:cover" muted></video>
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.2)"><i class="fa-solid fa-play" style="color:#fff"></i></div>
              <?php endif; ?>
            <?php else: ?>
              <img src="<?= htmlspecialchars(APP_URL . $g['path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover"/>
            <?php endif; ?>
            <button type="button" class="gallery-primary-btn" data-img-id="<?= (int)$g['id'] ?>"
                    title="<?= !empty($g['is_primary']) ? 'Currently primary' : 'Set as primary' ?>"
                    style="position:absolute;top:4px;left:4px;width:22px;height:22px;border-radius:50%;background:<?= !empty($g['is_primary']) ? '#f5b400' : 'rgba(0,0,0,.45)' ?>;color:#fff;border:none;cursor:pointer;font-size:.7rem">
              <i class="fa-solid fa-star"></i>
            </button>
            <button type="button" class="gallery-del-btn" data-img-id="<?= (int)$g['id'] ?>"
                    style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(220,38,38,.9);color:#fff;border:none;cursor:pointer;font-size:.7rem">
              <i class="fa-solid fa-xmark"></i>
            </button>
            <div style="position:absolute;left:4px;bottom:4px;display:flex;gap:3px">
              <button type="button" class="gallery-order-btn" data-direction="up" data-img-id="<?= (int)$g['id'] ?>" title="Move earlier" style="width:24px;height:24px;border:0;border-radius:5px;background:rgba(0,0,0,.58);color:#fff;cursor:pointer"><i class="fa-solid fa-arrow-left"></i></button>
              <button type="button" class="gallery-order-btn" data-direction="down" data-img-id="<?= (int)$g['id'] ?>" title="Move later" style="width:24px;height:24px;border:0;border-radius:5px;background:rgba(0,0,0,.58);color:#fff;cursor:pointer"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($gallery)): ?>
          <div style="grid-column:1/-1;text-align:center;color:#bbb;padding:16px;font-size:.8rem">No gallery items yet.</div>
          <?php endif; ?>
        </div>

        <div class="gallery-mode-row">
          <button type="button" class="btn btn-sm btn-outline gallery-mode-btn active" data-mode="image">Add Photo</button>
          <button type="button" class="btn btn-sm btn-outline gallery-mode-btn" data-mode="video_url">Add Video (YouTube/Vimeo)</button>
          <button type="button" class="btn btn-sm btn-outline gallery-mode-btn" data-mode="video_upload">Upload Video</button>
        </div>

        <div id="galleryModeImage" class="gallery-mode-panel">
          <input type="file" id="galleryFileImage" class="form-input" accept="image/jpeg,image/png,image/webp,image/gif" style="padding:8px;margin-bottom:8px"/>
        </div>
        <div id="galleryModeVideo_url" class="gallery-mode-panel" style="display:none">
          <input type="url" id="galleryVideoUrl" class="form-input" placeholder="https://youtube.com/watch?v=… or vimeo.com/…" style="margin-bottom:8px"/>
        </div>
        <div id="galleryModeVideo_upload" class="gallery-mode-panel" style="display:none">
          <input type="file" id="galleryFileVideo" class="form-input" accept="video/mp4,video/webm,video/quicktime" style="padding:8px;margin-bottom:8px"/>
          <div style="font-size:.72rem;color:#888;margin-bottom:8px">MP4, WebM or MOV · Max 40 MB</div>
        </div>
        <div id="galleryThumbnailWrap" style="display:none;margin:8px 0">
          <label class="form-label" for="galleryThumbnail">Video thumbnail (optional)</label>
          <input type="file" id="galleryThumbnail" class="form-input" accept="image/jpeg,image/png,image/webp" style="padding:8px"/>
        </div>

        <button type="button" class="btn btn-primary btn-sm" id="galleryAddBtn" style="width:100%">
          <i class="fa-solid fa-plus"></i> Add to Gallery
        </button>
        <div id="galleryMsg" style="margin-top:8px;font-size:.78rem;display:none"></div>
      </div>
      <?php endif; ?>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" style="flex:1">
          <i class="fa-solid fa-<?= $is_edit?'floppy-disk':'plus' ?>"></i>
          <?= $is_edit ? 'Save Changes' : 'Create Service' ?>
        </button>
        <a href="<?= APP_URL ?>/admin/services" class="btn btn-outline">Cancel</a>
      </div>
    </div>
  </div>
</form>
</div>

<?php
$csrf = Security::csrfToken();
$serviceIdJs = $is_edit ? (int)$s['id'] : 'null';
$extra_admin_js = <<<JS
// Auto-generate slug from title
document.querySelector('[name=title]').addEventListener('input', function(){
  var slug = document.getElementById('slugField');
  if (!slug.dataset.manual) {
    slug.value = this.value.toLowerCase()
      .replace(/[^a-z0-9\s-]/g,'')
      .replace(/[\s]+/g,'-')
      .replace(/-+/g,'-')
      .replace(/^-|-\$/g,'');
  }
});
document.getElementById('slugField').addEventListener('input', function(){
  this.dataset.manual = '1';
});

// ── Description Page Gallery (only present on the edit screen) ──
(function(){
  var addBtn = document.getElementById('galleryAddBtn');
  if (!addBtn) return; // create-new-service screen has no gallery yet

  var serviceId = {$serviceIdJs};
  var csrf = '{$csrf}';
  var currentMode = 'image';

  document.querySelectorAll('.gallery-mode-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.gallery-mode-btn').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      currentMode = btn.dataset.mode;
      document.querySelectorAll('.gallery-mode-panel').forEach(function(p){ p.style.display = 'none'; });
      document.getElementById('galleryMode' + currentMode.charAt(0).toUpperCase() + currentMode.slice(1)).style.display = 'block';
      document.getElementById('galleryThumbnailWrap').style.display = currentMode === 'image' ? 'none' : 'block';
    });
  });

  addBtn.addEventListener('click', function(){
    var fd = new FormData();
    fd.append('csrf_token', csrf);
    fd.append('media_mode', currentMode);

    if (currentMode === 'image') {
      var f = document.getElementById('galleryFileImage').files[0];
      if (!f) return showGalleryMsg('Choose a photo first.', false);
      fd.append('media', f);
    } else if (currentMode === 'video_url') {
      var url = document.getElementById('galleryVideoUrl').value.trim();
      if (!url) return showGalleryMsg('Paste a YouTube or Vimeo link first.', false);
      fd.append('video_url', url);
    } else if (currentMode === 'video_upload') {
      var vf = document.getElementById('galleryFileVideo').files[0];
      if (!vf) return showGalleryMsg('Choose a video file first.', false);
      fd.append('media', vf);
    }
    var thumbnail = document.getElementById('galleryThumbnail').files[0];
    if (currentMode !== 'image' && thumbnail) fd.append('thumbnail', thumbnail);

    addBtn.disabled = true;
    fetch(window.ELLCY_BASE + '/admin/services/gallery/add/' + serviceId, { method:'POST', body: fd })
      .then(function(r){ return r.json(); })
      .then(function(d){
        if (d.success) { showGalleryMsg('Added!', true); setTimeout(function(){ location.reload(); }, 600); }
        else { showGalleryMsg(d.message || 'Failed to add.', false); addBtn.disabled = false; }
      })
      .catch(function(){ showGalleryMsg('Network error.', false); addBtn.disabled = false; });
  });

  document.querySelectorAll('.gallery-del-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      if (!confirm('Remove this gallery item?')) return;
      var id = btn.dataset.imgId;
      fetch(window.ELLCY_BASE + '/admin/services/gallery/delete/' + id, {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'csrf_token=' + encodeURIComponent(csrf)
      })
      .then(function(r){ return r.json(); })
      .then(function(d){ if (d.success) location.reload(); });
    });
  });

  document.querySelectorAll('.gallery-primary-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      var id = btn.dataset.imgId;
      fetch(window.ELLCY_BASE + '/admin/services/gallery/primary/' + id, {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'csrf_token=' + encodeURIComponent(csrf)
      })
      .then(function(r){ return r.json(); })
      .then(function(d){ if (d.success) location.reload(); else showGalleryMsg(d.message||'Error', false); });
    });
  });

  document.querySelectorAll('.gallery-order-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      fetch(window.ELLCY_BASE + '/admin/services/gallery/reorder/' + btn.dataset.imgId, {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'direction=' + encodeURIComponent(btn.dataset.direction) + '&csrf_token=' + encodeURIComponent(csrf)
      }).then(function(r){ return r.json(); })
        .then(function(d){ if(d.success) location.reload(); else showGalleryMsg(d.message||'Reorder failed.', false); });
    });
  });

  function showGalleryMsg(text, ok){
    var el = document.getElementById('galleryMsg');
    el.style.display = 'block';
    el.style.color = ok ? '#1a7a3d' : '#b02020';
    el.textContent = text;
  }
})();

// ── Primary Media slots (Image 1 / Image 2 / Video) ─────────────
// "Replace" = upload the new file first (via the existing gallery-add
// endpoint), then delete the slot's previous item — giving a clean
// swap using the same secure upload/delete backend as the classic
// gallery below, with no separate endpoint required.
(function(){
  var slots = document.querySelectorAll('.media-slot');
  if (!slots.length) return;

  var serviceId = {$serviceIdJs};
  var csrf = '{$csrf}';

  function showSlotMsg(text, ok){
    var el = document.getElementById('mediaSlotMsg');
    if (!el) return;
    el.style.display = 'block';
    el.style.color = ok ? '#1a7a3d' : '#b02020';
    el.textContent = text;
  }

  document.querySelectorAll('.media-slot-replace').forEach(function(btn){
    btn.addEventListener('click', function(){
      var slotKey = btn.dataset.slot;
      var input = document.querySelector('.media-slot-file[data-slot="' + slotKey + '"]');
      if (input) input.click();
    });
  });

  document.querySelectorAll('.media-slot-file').forEach(function(input){
    input.addEventListener('change', function(){
      var file = input.files[0];
      if (!file) return;
      var slotKey = input.dataset.slot;
      var slotEl = document.querySelector('.media-slot[data-slot="' + slotKey + '"]');
      var oldImgId = slotEl ? slotEl.dataset.imgId : '';
      var isVideo = slotKey === 'slotVideo';

      var fd = new FormData();
      fd.append('csrf_token', csrf);
      fd.append('media_mode', isVideo ? 'video_upload' : 'image');
      fd.append('media', file);
      if (oldImgId) fd.append('replace_id', oldImgId);

      showSlotMsg('Uploading…', true);
      fetch(window.ELLCY_BASE + '/admin/services/gallery/add/' + serviceId, { method:'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(d){
          if (!d.success) { showSlotMsg(d.message || 'Upload failed.', false); return; }
          // New file uploaded — now remove the old one at this slot (true "replace").
          if (oldImgId) {
            fetch(window.ELLCY_BASE + '/admin/services/gallery/delete/' + oldImgId, {
              method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
              body:'csrf_token=' + encodeURIComponent(csrf)
            }).finally(function(){ location.reload(); });
          } else {
            location.reload();
          }
        })
        .catch(function(){ showSlotMsg('Network error.', false); });
    });
  });

  document.querySelectorAll('.media-slot-delete').forEach(function(btn){
    btn.addEventListener('click', function(){
      if (!confirm('Delete this media item?')) return;
      var id = btn.dataset.imgId;
      fetch(window.ELLCY_BASE + '/admin/services/gallery/delete/' + id, {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'csrf_token=' + encodeURIComponent(csrf)
      })
      .then(function(r){ return r.json(); })
      .then(function(d){ if (d.success) location.reload(); else showSlotMsg(d.message || 'Delete failed.', false); });
    });
  });
})();
JS;
require VIEWS_PATH . '/admin/layout_end.php';
?>
