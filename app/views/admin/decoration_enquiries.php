<?php
$page_title  = 'Decoration Enquiries';
$active_page = 'decoration_enquiries';
require VIEWS_PATH . '/admin/layout_start.php';
?>

<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title">
      <i class="fa-solid fa-lightbulb" style="color:#6a1b9a;margin-right:8px"></i>Decoration Enquiries
    </div>
    <div style="display:flex;gap:8px">
      <span class="badge badge-amber"><?= (int)($status_counts['new']??0) ?> New</span>
      <span class="badge badge-blue"><?= (int)($status_counts['contacted']??0) ?> Contacted</span>
      <span class="badge badge-green"><?= (int)($status_counts['converted']??0) ?> Converted</span>
    </div>
  </div>

  <div class="filter-bar" style="gap:8px">
    <a href="?tab=stage" class="btn btn-sm <?= $current_tab==='stage' ? 'btn-primary' : 'btn-outline' ?>">
      <i class="fa-solid fa-water"></i> Stage Decoration
    </a>
    <a href="?tab=light" class="btn btn-sm <?= $current_tab==='light' ? 'btn-primary' : 'btn-outline' ?>">
      <i class="fa-solid fa-lightbulb"></i> Light Decoration
    </a>
    <select class="form-select" style="width:auto;padding:8px 13px;margin-left:auto" id="decStatus" onchange="applyDecFilters()">
      <option value="">All Status</option>
      <option value="new"       <?= ($_GET['status']??'')==='new'      ?'selected':'' ?>>New</option>
      <option value="contacted" <?= ($_GET['status']??'')==='contacted'?'selected':'' ?>>Contacted</option>
      <option value="converted" <?= ($_GET['status']??'')==='converted'?'selected':'' ?>>Converted</option>
      <option value="closed"    <?= ($_GET['status']??'')==='closed'   ?'selected':'' ?>>Closed</option>
    </select>
  </div>

  <?php if (empty($enquiries) && !Database::fetchOne("SHOW TABLES LIKE '" . ($current_tab==='stage'?'stage_decoration_enquiries':'light_decoration_enquiries') . "'")): ?>
  <div style="padding:24px;background:#fff8e6;border:1px solid #f5d78a;border-radius:10px;margin:16px;color:#7a5c00;font-size:.85rem">
    <i class="fa-solid fa-triangle-exclamation"></i>
    The enquiry tables don't exist yet. Import <code>sql/enquiries_decoration.sql</code> via phpMyAdmin to enable this page.
  </div>
  <?php endif; ?>

  <div style="overflow-x:auto">
  <table class="data-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>Event Date</th>
        <th>Budget</th>
        <th>Location</th>
        <th><?= $current_tab==='stage' ? 'Preference' : 'Arch' ?></th>
        <th>Venue Photo</th>
        <th>Status</th>
        <th>Received</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($enquiries as $e): ?>
      <tr>
        <td style="color:#aaa;font-size:.8rem"><?= (int)$e['enquiry_id'] ?></td>
        <td style="font-weight:700;font-size:.85rem"><?= htmlspecialchars($e['customer_name']) ?></td>
        <td style="font-size:.8rem">
          <a href="tel:<?= htmlspecialchars($e['phone_number']) ?>" style="color:#6a1b9a;text-decoration:none;font-weight:600"><?= htmlspecialchars($e['phone_number']) ?></a>
          <?php if (!empty($e['email'])): ?><br/><span style="color:#888"><?= htmlspecialchars($e['email']) ?></span><?php endif; ?>
        </td>
        <td style="font-size:.8rem"><?= $e['event_date'] ? date('d M Y', strtotime($e['event_date'])) : '—' ?></td>
        <td style="font-size:.78rem"><?= htmlspecialchars($e['budget_range'] ?: '—') ?></td>
        <td style="font-size:.78rem;max-width:160px"><?= htmlspecialchars(mb_strimwidth($e['location'] ?? '', 0, 40, '…')) ?></td>
        <td style="font-size:.78rem">
          <?php if ($current_tab==='stage'): ?>
            <?= $e['flower_type'] ? ($e['flower_type']==='real' ? '🌸 Real' : '🌼 Artificial') : '—' ?>
          <?php else: ?>
            <?= $e['arch_required'] ? ($e['arch_required']==='yes' ? '✅ Yes' : '❌ No') : '—' ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($e['venue_image'])): ?>
            <a href="<?= APP_URL . htmlspecialchars($e['venue_image']) ?>" target="_blank" rel="noopener">
              <img src="<?= APP_URL . htmlspecialchars($e['venue_image']) ?>" alt="Venue" style="width:44px;height:44px;object-fit:cover;border-radius:8px"/>
            </a>
          <?php else: ?>
            <span style="color:#ccc">—</span>
          <?php endif; ?>
        </td>
        <td>
          <span class="badge badge-<?= match($e['enquiry_status']){
            'contacted'=>'blue','converted'=>'green','closed'=>'red',default=>'amber'
          } ?>">
            <?= ucfirst(htmlspecialchars($e['enquiry_status'])) ?>
          </span>
        </td>
        <td style="font-size:.78rem;color:#888;white-space:nowrap"><?= date('d M y, H:i', strtotime($e['created_at'])) ?></td>
        <td>
          <div style="display:flex;gap:5px">
            <button class="btn btn-sm btn-success" onclick="updateDecStatus('<?= $current_tab ?>',<?= (int)$e['enquiry_id'] ?>,'contacted')" title="Mark Contacted">
              <i class="fa-solid fa-phone"></i>
            </button>
            <button class="btn btn-sm btn-outline" onclick="updateDecStatus('<?= $current_tab ?>',<?= (int)$e['enquiry_id'] ?>,'converted')" title="Mark Converted">
              <i class="fa-solid fa-check"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="updateDecStatus('<?= $current_tab ?>',<?= (int)$e['enquiry_id'] ?>,'closed')" title="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($enquiries)): ?>
      <tr><td colspan="11" style="text-align:center;color:#aaa;padding:32px">No enquiries found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<?php
$csrf = Security::csrfToken();
$extra_admin_js = <<<JS
function applyDecFilters(){
  var s = document.getElementById('decStatus').value;
  var tab = '{$current_tab}';
  window.location.href = window.location.pathname + '?tab=' + tab + '&status=' + encodeURIComponent(s);
}
function updateDecStatus(type, id, status){
  fetch(window.ELLCY_BASE + '/admin/decoration-enquiries/update/' + type + '/' + id, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'status=' + encodeURIComponent(status) + '&csrf_token={$csrf}'
  })
  .then(function(r){return r.json();})
  .then(function(d){
    if (d.success) { showNotif('Status updated to ' + status, 'success'); setTimeout(function(){ location.reload(); }, 900); }
    else showNotif(d.message || 'Error', 'error');
  });
}
JS;
require VIEWS_PATH . '/admin/layout_end.php';
