<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>ELLCY | Create Account</title>
  <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE) ?>/css/style.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
  <style>
    body{background:#f4e9ff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;font-family:'Segoe UI',system-ui,sans-serif;margin:0}
    .auth-card{background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(106,27,154,.15);padding:44px 36px;width:100%;max-width:420px}
    .auth-logo{font-size:1.9rem;font-weight:900;color:#6a1b9a;letter-spacing:-.04em;text-align:center;margin-bottom:4px;text-decoration:none;display:block}
    .auth-sub{text-align:center;color:#888;font-size:.85rem;margin-bottom:32px}
    .auth-field{margin-bottom:16px}
    .auth-label{display:block;font-weight:700;font-size:.8rem;color:#1a1a2e;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    .auth-input{width:100%;padding:12px 14px;border:1.5px solid #e0d5f0;border-radius:10px;background:#fafafa;font-size:.94rem;font-family:inherit;outline:none;transition:border-color .18s,box-shadow .18s}
    .auth-input:focus{border-color:#6a1b9a;box-shadow:0 0 0 3px rgba(106,27,154,.1);background:#fff}
    .auth-btn{width:100%;padding:13px;background:#6a1b9a;color:#fff;border:none;border-radius:10px;font-family:inherit;font-size:1rem;font-weight:800;cursor:pointer;margin-top:6px;box-shadow:0 4px 16px rgba(106,27,154,.3)}
    .auth-btn:hover{background:#5c1690}
    .auth-error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:18px}
    .auth-footer{text-align:center;margin-top:22px;font-size:.85rem;color:#555}
    .auth-footer a{color:#6a1b9a;font-weight:700;text-decoration:none}
    .auth-back{display:block;text-align:center;margin-top:14px;font-size:.8rem;color:#999;text-decoration:none}
    .auth-hint{font-size:.74rem;color:#999;margin-top:4px}
  </style>
</head>
<body>
<div class="auth-card">
  <a class="auth-logo" href="<?= htmlspecialchars(APP_BASE) ?>/">ELLCY</a>
  <div class="auth-sub">Create an account to complete your booking</div>

  <?php if (!empty($error)): ?>
  <div class="auth-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
    <input type="hidden" name="return_to" value="<?= htmlspecialchars($return_to) ?>">
    <div class="auth-field">
      <label class="auth-label" for="name">Full Name</label>
      <input type="text" id="name" name="name" class="auth-input" required autocomplete="name"/>
    </div>
    <div class="auth-field">
      <label class="auth-label" for="email">Email Address</label>
      <input type="email" id="email" name="email" class="auth-input" required autocomplete="email"/>
    </div>
    <div class="auth-field">
      <label class="auth-label" for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone" class="auth-input" placeholder="10-digit mobile number" required autocomplete="tel"/>
    </div>
    <div class="auth-field">
      <label class="auth-label" for="password">Password</label>
      <input type="password" id="password" name="password" class="auth-input" required minlength="8" autocomplete="new-password"/>
      <div class="auth-hint">At least 8 characters.</div>
    </div>
    <button type="submit" class="auth-btn"><i class="fa-solid fa-user-plus"></i> Create Account</button>
  </form>

  <div class="auth-footer">
    Already have an account?
    <a href="<?= htmlspecialchars(APP_BASE) ?>/login?return_to=<?= urlencode($return_to) ?>">Log in</a>
  </div>
  <a class="auth-back" href="<?= htmlspecialchars(APP_BASE) ?>/"><i class="fa-solid fa-arrow-left"></i> Back to browsing</a>
</div>
</body>
</html>
