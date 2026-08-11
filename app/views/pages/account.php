<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>ELLCY | My Account</title>
  <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE) ?>/css/style.css"/>
  <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE) ?>/css/cart.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
  <style>
    body{background:#f8f6fb;font-family:'Segoe UI',system-ui,sans-serif;margin:0;color:#1a1a2e}
    .acct-topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#fff;border-bottom:1px solid #eee;position:sticky;top:0;z-index:10}
    .acct-logo{font-size:1.3rem;font-weight:900;color:#6a1b9a;letter-spacing:-.04em;text-decoration:none}
    .acct-back{font-size:.85rem;color:#555;text-decoration:none;font-weight:600}
    .acct-wrap{max-width:900px;margin:0 auto;padding:28px 20px 60px}
    .acct-hello{font-size:1.4rem;font-weight:800;margin-bottom:24px}
    .acct-card{background:#fff;border:1px solid #f0ecf8;border-radius:14px;padding:24px;margin-bottom:22px;box-shadow:0 4px 18px rgba(107,33,168,.06)}
    .acct-card h2{font-size:1.05rem;font-weight:800;margin:0 0 18px;display:flex;align-items:center;gap:8px}
    .acct-card h2 i{color:#6a1b9a}
    .acct-field{margin-bottom:16px}
    .acct-label{display:block;font-weight:700;font-size:.78rem;color:#1a1a2e;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    .acct-input{width:100%;padding:11px 14px;border:1.5px solid #e0d5f0;border-radius:9px;background:#fafafa;font-size:.92rem;font-family:inherit;outline:none;box-sizing:border-box}
    .acct-input:focus{border-color:#6a1b9a;background:#fff}
    .acct-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    @media (max-width:560px){.acct-row{grid-template-columns:1fr}}
    .acct-btn{background:#6a1b9a;color:#fff;border:none;padding:11px 22px;border-radius:9px;font-weight:800;font-size:.9rem;cursor:pointer;font-family:inherit}
    .acct-btn:hover{background:#5c1690}
    .acct-msg{border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:16px}
    .acct-msg.ok{background:#e8f8ee;border:1px solid #86e0a8;color:#1e6b3a}
    .acct-msg.err{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}
    .order-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f0ecf8;font-size:.88rem;gap:10px;flex-wrap:wrap}
    .order-row:last-child{border-bottom:none}
    .order-ref{font-weight:800;color:#6a1b9a}
    .order-status{font-size:.74rem;font-weight:700;padding:3px 10px;border-radius:999px;background:#f4e9ff;color:#6a1b9a;text-transform:capitalize}
    .acct-empty{color:#999;font-size:.88rem;padding:10px 0}
    .acct-logout-link{color:#c0392b;font-weight:700;text-decoration:none;font-size:.85rem}
  </style>
</head>
<body>
<div class="acct-topbar">
  <a class="acct-logo" href="<?= htmlspecialchars(APP_BASE) ?>/">ELLCY</a>
  <a class="acct-back" href="<?= htmlspecialchars(APP_BASE) ?>/"><i class="fa-solid fa-arrow-left"></i> Back to browsing</a>
</div>

<div class="acct-wrap">
  <div class="acct-hello">Hello, <?= htmlspecialchars($user['name'] ?? 'there') ?> 👋</div>

  <?php if (!empty($saved)): ?>
    <div class="acct-msg ok"><i class="fa-solid fa-circle-check"></i> Your changes were saved.</div>
  <?php elseif (!empty($error)): ?>
    <div class="acct-msg err"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- My Profile -->
  <div class="acct-card" id="profile">
    <h2><i class="fa-solid fa-id-card"></i> My Profile</h2>
    <form method="POST" action="<?= htmlspecialchars(APP_BASE) ?>/account">
      <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
      <input type="hidden" name="action" value="profile">
      <div class="acct-row">
        <div class="acct-field">
          <label class="acct-label" for="name">Full Name</label>
          <input type="text" id="name" name="name" class="acct-input" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required/>
        </div>
        <div class="acct-field">
          <label class="acct-label" for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" class="acct-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required/>
        </div>
      </div>
      <div class="acct-field">
        <label class="acct-label">Email Address</label>
        <input type="email" class="acct-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled/>
      </div>
      <div class="acct-field">
        <label class="acct-label" for="address">Address <span style="text-transform:none;font-weight:400">(optional)</span></label>
        <input type="text" id="address" name="address" class="acct-input" value="<?= htmlspecialchars($user['address'] ?? '') ?>"/>
      </div>
      <button type="submit" class="acct-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
    </form>
  </div>

  <!-- Account Settings: password -->
  <div class="acct-card" id="settings">
    <h2><i class="fa-solid fa-gear"></i> Account Settings — Change Password</h2>
    <form method="POST" action="<?= htmlspecialchars(APP_BASE) ?>/account#settings">
      <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
      <input type="hidden" name="action" value="password">
      <div class="acct-row">
        <div class="acct-field">
          <label class="acct-label" for="current_password">Current Password</label>
          <input type="password" id="current_password" name="current_password" class="acct-input" required autocomplete="current-password"/>
        </div>
        <div class="acct-field">
          <label class="acct-label" for="new_password">New Password</label>
          <input type="password" id="new_password" name="new_password" class="acct-input" required minlength="8" autocomplete="new-password"/>
        </div>
      </div>
      <button type="submit" class="acct-btn"><i class="fa-solid fa-key"></i> Update Password</button>
    </form>
  </div>

  <!-- My Bookings / My Orders -->
  <div class="acct-card" id="orders">
    <h2><i class="fa-solid fa-calendar-check"></i> My Bookings &amp; Orders</h2>
    <?php if (empty($orders)): ?>
      <p class="acct-empty">You haven't placed any bookings yet. <a href="<?= htmlspecialchars(APP_BASE) ?>/category?type=wedding" style="color:#6a1b9a;font-weight:700;">Browse services</a> to get started.</p>
    <?php else: ?>
      <?php foreach ($orders as $o): ?>
        <?php $items = json_decode($o['items_json'] ?? '[]', true) ?: []; ?>
        <div class="order-row" style="flex-direction:column;align-items:stretch;gap:8px;">
          <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <div>
              <span class="order-ref">#<?= htmlspecialchars($o['order_ref']) ?></span>
              — <?= htmlspecialchars($o['event_type'] ?: 'Event') ?>
              <?php if (!empty($o['event_date'])): ?> · <?= htmlspecialchars(date('d M Y', strtotime($o['event_date']))) ?><?php endif; ?>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <span>₹<?= number_format((float)$o['total'], 0) ?></span>
              <span class="order-status"><?= htmlspecialchars(str_replace('_',' ', $o['status'])) ?></span>
            </div>
          </div>
          <?php if ($items): ?>
            <ul style="margin:0;padding:0 0 0 4px;list-style:none;font-size:.86rem;color:#555;">
              <?php foreach ($items as $it): ?>
                <li style="display:flex;justify-content:space-between;padding:4px 0;border-top:1px solid #f1e9f8;">
                  <span><?= htmlspecialchars($it['title'] ?? 'Service') ?><?php if (!empty($o['event_date'])): ?> · <?= htmlspecialchars(date('d M Y', strtotime($o['event_date']))) ?><?php endif; ?></span>
                  <span>₹<?= number_format((float)($it['price'] ?? 0), 0) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div style="text-align:center;margin-top:10px;">
    <a class="acct-logout-link" href="<?= htmlspecialchars(APP_BASE) ?>/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>
</div>
</body>
</html>
