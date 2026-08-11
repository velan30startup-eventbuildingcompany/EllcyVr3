<?php require VIEWS_PATH . '/admin/layout_start.php'; ?>

<div style="max-width:620px">
<form method="POST" action="">
  <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

  <div class="data-card" style="padding:28px;margin-bottom:24px">
    <div style="font-size:.95rem;font-weight:700;margin-bottom:20px;color:#1a1a2e">
      <i class="fa-solid fa-globe" style="color:#6a1b9a;margin-right:8px"></i>Site Information
    </div>
    <div class="form-group">
      <label class="form-label">Site Name</label>
      <input type="text" name="site_name" class="form-input"
             value="<?= htmlspecialchars($settings['site_name'] ?? 'ELLCY') ?>"/>
    </div>
    <div class="form-group">
      <label class="form-label">Site Tagline</label>
      <input type="text" name="site_tagline" class="form-input"
             value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>"/>
    </div>
    <div class="form-group">
      <label class="form-label">Maintenance Mode</label>
      <select name="maintenance" class="form-select">
        <option value="0" <?= ($settings['maintenance']??'0')==='0'?'selected':'' ?>>Off (Site is live)</option>
        <option value="1" <?= ($settings['maintenance']??'0')==='1'?'selected':'' ?>>On (Maintenance page shown)</option>
      </select>
    </div>
  </div>

  <div class="data-card" style="padding:28px;margin-bottom:24px">
    <div style="font-size:.95rem;font-weight:700;margin-bottom:20px">
      <i class="fa-solid fa-address-card" style="color:#6a1b9a;margin-right:8px"></i>Contact Details
    </div>
    <div class="form-group">
      <label class="form-label">Phone Number</label>
      <input type="text" name="contact_phone" class="form-input"
             value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>"/>
    </div>
    <div class="form-group">
      <label class="form-label">Email Address</label>
      <input type="email" name="contact_email" class="form-input"
             value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"/>
    </div>
    <div class="form-group">
      <label class="form-label">Address</label>
      <input type="text" name="contact_address" class="form-input"
             value="<?= htmlspecialchars($settings['contact_address'] ?? '') ?>"/>
    </div>
  </div>

  <div style="display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">
      <i class="fa-solid fa-floppy-disk"></i> Save Settings
    </button>
  </div>
</form>
</div>

<?php require VIEWS_PATH . '/admin/layout_end.php'; ?>
