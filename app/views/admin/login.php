<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>ELLCY Admin — Login</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',system-ui,sans-serif;background:#f4e9ff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
    .login-card{background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(106,27,154,.15);padding:48px 40px;width:100%;max-width:420px}
    .login-logo{font-size:2rem;font-weight:900;color:#6a1b9a;letter-spacing:-.04em;text-align:center;margin-bottom:4px}
    .login-sub{text-align:center;color:#888;font-size:.85rem;margin-bottom:36px}
    .login-field{margin-bottom:18px}
    .login-label{display:block;font-weight:700;font-size:.83rem;color:#1a1a2e;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    .login-input{width:100%;padding:12px 14px;border:1.5px solid #e0d5f0;border-radius:10px;background:#fafafa;font-size:.94rem;font-family:inherit;outline:none;transition:border-color .18s,box-shadow .18s}
    .login-input:focus{border-color:#6a1b9a;box-shadow:0 0 0 3px rgba(106,27,154,.1);background:#fff}
    .login-btn{width:100%;padding:14px;background:#6a1b9a;color:#fff;border:none;border-radius:10px;font-family:inherit;font-size:1rem;font-weight:800;cursor:pointer;margin-top:8px;transition:background .18s,transform .14s;box-shadow:0 4px 16px rgba(106,27,154,.3)}
    .login-btn:hover{background:#5c1690;transform:translateY(-1px)}
    .login-btn:disabled{background:#bbb;box-shadow:none;cursor:not-allowed;transform:none}
    .login-error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:18px;display:none}
    .login-error.show{display:block}
    .login-footer{text-align:center;margin-top:24px;font-size:.8rem;color:#aaa}
    .setup-note{background:#fff8dc;border:1px solid #efd774;color:#765b00;border-radius:10px;padding:12px 14px;font-size:.84rem;line-height:1.45;margin-bottom:18px}
    .setup-note a{display:inline-block;margin-top:7px;color:#6a1b9a;font-weight:800;text-decoration:none}
  </style>
</head>
<body>
<div class="login-card">
  <div class="login-logo">ELLCY</div>
  <div class="login-sub">Admin Dashboard</div>

  <?php if (!empty($error)): ?>
  <div class="login-error show"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($needsSetup)): ?>
  <div class="setup-note">
    No admin account exists yet. Create the first administrator locally, then return here to manage prices, images and videos.
    <br><a href="<?= APP_URL ?>/setup.php?step=4">Create admin account</a>
  </div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
    <div class="login-field">
      <label class="login-label" for="email">Email Address</label>
      <input type="email" id="email" name="email" class="login-input"
             placeholder="admin@ellcy.in" required autocomplete="email"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
    </div>
    <div class="login-field">
      <label class="login-label" for="password">Password</label>
      <input type="password" id="password" name="password" class="login-input"
             placeholder="••••••••" required autocomplete="current-password"/>
    </div>
    <button type="submit" class="login-btn">Sign In</button>
  </form>
  <div class="login-footer">ELLCY Admin Panel &copy; <?= date('Y') ?></div>
</div>
</body>
</html>
