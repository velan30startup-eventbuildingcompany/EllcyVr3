<?php
$page_title  = 'Dashboard';
$active_page = 'dashboard';
require VIEWS_PATH . '/admin/layout_start.php';
?>

<!-- STAT CARDS -->
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon purple"><i class="fa-solid fa-layer-group"></i></div>
    <div>
      <div class="stat-label">Total Services</div>
      <div class="stat-value"><?= number_format($stats['services'] ?? 0) ?></div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green"><i class="fa-solid fa-calendar-check"></i></div>
    <div>
      <div class="stat-label">Total Bookings</div>
      <div class="stat-value"><?= number_format($stats['orders'] ?? 0) ?></div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber"><i class="fa-solid fa-phone-volume"></i></div>
    <div>
      <div class="stat-label">Call Requests</div>
      <div class="stat-value"><?= number_format($stats['requests'] ?? 0) ?></div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple"><i class="fa-solid fa-indian-rupee-sign"></i></div>
    <div>
      <div class="stat-label">Total Revenue</div>
      <div class="stat-value">₹<?= number_format($stats['revenue'] ?? 0) ?></div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

<!-- RECENT BOOKINGS -->
<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title"><i class="fa-solid fa-calendar-check" style="color:#6a1b9a;margin-right:8px"></i>Recent Bookings</div>
    <a class="btn btn-sm btn-outline" href="<?= APP_URL ?>/admin/bookings">View All</a>
  </div>
  <table class="data-table">
    <thead>
      <tr><th>Ref</th><th>Name</th><th>Event</th><th>Status</th></tr>
    </thead>
    <tbody>
      <?php foreach ($recent_orders as $o): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/admin/bookings/<?= (int)$o['id'] ?>" style="color:#6a1b9a;font-weight:700"><?= htmlspecialchars($o['order_ref']) ?></a></td>
        <td><?= htmlspecialchars($o['name']) ?></td>
        <td><?= htmlspecialchars($o['event_type'] ?: '—') ?></td>
        <td>
          <span class="badge badge-<?= match($o['status']){
            'confirmed'=>'green','completed'=>'blue','cancelled'=>'red',default=>'amber'
          } ?>">
            <?= ucfirst(htmlspecialchars($o['status'])) ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($recent_orders)): ?>
      <tr><td colspan="4" style="text-align:center;color:#aaa;padding:24px">No bookings yet</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- RECENT CALL REQUESTS -->
<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title"><i class="fa-solid fa-phone-volume" style="color:#6a1b9a;margin-right:8px"></i>Call Requests</div>
    <a class="btn btn-sm btn-outline" href="<?= APP_URL ?>/admin/requests">View All</a>
  </div>
  <table class="data-table">
    <thead>
      <tr><th>Phone</th><th>Service</th><th>Status</th><th>Date</th></tr>
    </thead>
    <tbody>
      <?php foreach ($recent_requests as $r): ?>
      <tr>
        <td style="font-weight:700"><?= htmlspecialchars($r['phone']) ?></td>
        <td style="font-size:.8rem"><?= htmlspecialchars(mb_strimwidth($r['service'] ?? '—', 0, 24, '…')) ?></td>
        <td>
          <span class="badge badge-<?= $r['status']==='new'?'amber':($r['status']==='completed'?'green':'blue') ?>">
            <?= ucfirst(htmlspecialchars($r['status'])) ?>
          </span>
        </td>
        <td style="color:#888;font-size:.78rem"><?= date('d M', strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($recent_requests)): ?>
      <tr><td colspan="4" style="text-align:center;color:#aaa;padding:24px">No requests yet</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</div>

<?php require VIEWS_PATH . '/admin/layout_end.php'; ?>
