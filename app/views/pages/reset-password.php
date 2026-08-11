<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>ELLCY | Reset Password</title>
  <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE) ?>/css/style.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
  <style>
    body{background:#f4e9ff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;font-family:'Segoe UI',system-ui,sans-serif;margin:0}
    .auth-card{background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(106,27,154,.15);padding:44px 36px;width:100%;max-width:400px}
    .auth-logo{font-size:1.9rem;font-weight:900;color:#6a1b9a;letter-spacing:-.04em;text-align:center;margin-bottom:4px;text-decoration:none;display:block}
    .auth-sub{text-align:center;color:#888;font-size:.85rem;margin-bottom:32px}
    .auth-field{margin-bottom:16px}
    .auth-label{display:block;font-weight:700;font-size:.8rem;color:#1a1a2e;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    .auth-input{width:100%;padding:12px 14px;border:1.5px solid #e0d5f0;border-radius:10px;background:#fafafa;font-size:.94rem;font-family:inherit;outline:none;transition:border-color .18s,box-shadow .18s}
    .auth-input:focus{border-color:#6a1b9a;box-shadow:0 0 0 3px rgba(106,27,154,.1);background:#fff}
    .auth-btn{width:100%;padding:13px;background:#6a1b9a;color:#fff;border:none;border-radius:10px;font-family:inherit;font-size:1rem;font-weight:800;cursor:pointer;margin-top:6px;box-shadow:0 4px 16px rgba(106,27,154,.3)}
    .auth-btn:hover{background:#5c1690}
    .auth-error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:18px}
    .auth-success{background:#e8f8ee;border:1px solid #86e0a8;color:#1e6b3a;border-radius:8px;padding:14px;font-size:.85rem;margin-bottom:18px;line-height:1.5}
    .auth-footer{text-align:center;margin-top:22px;font-size:.85rem;color:#555}
    .auth-footer a{color:#6a1b9a;font-weight:700;text-decoration:none}
    .auth-hint{font-size:.74rem;color:#999;margin-top:4px}
  </style>
</head>
<body>
<div class="auth-card">
  <a class="auth-logo" href="<?= htmlspecialchars(APP_BASE) ?>/">ELLCY</a>
  <div class="auth-sub">Choose a new password</div>

  <?php if (!empty($error)): ?>
  <div class="auth-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($done)): ?>
  <div class="auth-success">
    <i class="fa-solid fa-circle-check"></i>
    Your password has been reset. You can now log in with your new password.
  </div>
  <div class="auth-footer">
    <a href="<?= htmlspecialchars(APP_BASE) ?>/login">Go to Log In</a>
  </div>
  <?php else: ?>
  <div class="auth-sub" style="margin-top:-24px;margin-bottom:24px">
    We emailed a 6-digit code to <strong><?= htmlspecialchars($email ?: 'your address') ?></strong>. Enter it below along with your new password.
  </div>
  <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    <div class="auth-field">
      <label class="auth-label" for="code">6-Digit Code</label>
      <input type="text" id="code" name="code" class="auth-input" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="123456" style="letter-spacing:6px;text-align:center;font-size:1.3rem;font-weight:700"/>
      <div class="auth-hint">Expires 10 minutes after it's sent.</div>
    </div>
    <div class="auth-field">
      <label class="auth-label" for="password">New Password</label>
      <input type="password" id="password" name="password" class="auth-input" required minlength="8" autocomplete="new-password"/>
      <div class="auth-hint">At least 8 characters.</div>
    </div>
    <div class="auth-field">
      <label class="auth-label" for="password_confirm">Confirm New Password</label>
      <input type="password" id="password_confirm" name="password_confirm" class="auth-input" required minlength="8" autocomplete="new-password"/>
    </div>
    <button type="submit" class="auth-btn"><i class="fa-solid fa-key"></i> Verify &amp; Reset Password</button>
  </form>
  <div class="auth-footer">
    Didn't get a code?
    <a href="<?= htmlspecialchars(APP_BASE) ?>/forgot-password<?= $email ? '?email=' . urlencode($email) : '' ?>">Send a new one</a>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
