<?php
/**
 * ELLCY — First-Run Setup Wizard
 * Access: http://localhost/ellcy/setup.php
 * DELETE this file after first use!
 */

// Security: only allow from localhost
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
    http_response_code(403);
    die('Access denied. This setup script is only accessible from localhost.');
}

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/Security.php';

Security::startSession();

$step    = (int)($_GET['step'] ?? 1);
$error   = '';
$success = '';

// Safety: if an active admin already exists, the wizard has already been
// run once. Block further use so this file can't be used to silently
// reset admin credentials if someone forgets to delete it.
try {
    $existingAdmin = Database::fetchOne(
        "SELECT id FROM users WHERE role IN ('admin','superadmin') AND status='active' LIMIT 1"
    );
} catch (Exception $e) {
    $existingAdmin = null; // tables not created yet — first run, allow through
}
if ($existingAdmin && $step >= 3) {
    http_response_code(403);
    die('Setup has already been completed. For security, please delete setup.php from your server. '
      . 'If you need to reset the admin password, do it directly in the database.');
}

// STEP 2: Test DB connection
if ($step === 2) {
    try {
        $db = Database::getInstance();
        $success = 'Database connection successful!';
    } catch (Exception $e) {
        $error = 'Connection failed: ' . $e->getMessage();
        $step  = 1;
    }
}

// STEP 3: Import schema
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrf()) {
        http_response_code(403);
        $error = 'Your setup session expired. Please reload the page and try again.';
    } else {
    try {
        $db = Database::getInstance();
        foreach ([
            '/sql/ellcy_schema.sql',
            '/sql/ellcy_seed_services.sql',
            '/sql/enquiries_decoration.sql',
            '/sql/media_gallery_migration.sql',
            '/sql/new_services_plates_rangoli.sql',
            '/sql/production_update_v2_migration.sql',
            '/sql/production_update_v3_auth_header_cart.sql',
            '/sql/production_update_v4_remove_services.sql',
            '/sql/production_update_v5_event_location.sql',
            '/sql/production_update_v6_otp_reset.sql',
            '/sql/production_update_v7_phone_otp_login.sql',
            '/sql/production_update_v8_requested_changes.sql',
            '/sql/production_update_v9_vendor_vr2.sql',
            '/sql/production_update_v10_catering_admin.sql',
        ] as $file) {
            $path = __DIR__ . $file;
            if (!is_file($path)) continue;
            $sql = file_get_contents($path);
            // Split on semicolons (crude but works for this schema)
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $stmt) {
                if ($stmt) $db->exec($stmt);
            }
        }
        $success = 'Database schema and service catalog imported successfully!';
        $step    = 4;
    } catch (Exception $e) {
        $error = 'Schema import failed: ' . $e->getMessage();
    }
    }
}

// STEP 4: Create admin user
if ($step === 4 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!Security::verifyCsrf()) {
        $error = 'Your setup session expired. Please reload the page and try again.';
    } elseif (!$name || !$email || !$password) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $hash = Security::hashPassword($password);
            $db   = Database::getInstance();
            // Update the placeholder admin or insert new
            $existing = Database::fetchOne('SELECT id FROM users WHERE email = ?', [$email]);
            if ($existing) {
                Database::query(
                    'UPDATE users SET name=?, password_hash=?, role="superadmin", status="active" WHERE email=?',
                    [$name, $hash, $email]
                );
            } else {
                Database::query(
                    'INSERT INTO users (name,email,password_hash,role,status) VALUES (?,?,?,"superadmin","active")',
                    [$name, $email, $hash]
                );
            }
            $success = 'Admin account created! You can now login.';
            $step    = 5;
        } catch (Exception $e) {
            $error = 'Failed to create admin: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>ELLCY — Setup Wizard</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',system-ui,sans-serif;background:#f4e9ff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
    .card{background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(106,27,154,.15);padding:48px 40px;width:100%;max-width:480px}
    .logo{font-size:1.8rem;font-weight:900;color:#6a1b9a;text-align:center;margin-bottom:4px}
    .sub{text-align:center;color:#888;font-size:.85rem;margin-bottom:28px}
    h2{font-size:1.1rem;font-weight:700;color:#1a1a2e;margin-bottom:20px}
    .field{margin-bottom:16px}
    label{display:block;font-size:.82rem;font-weight:700;color:#1a1a2e;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    input{width:100%;padding:11px 13px;border:1.5px solid #e0d5f0;border-radius:9px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .18s}
    input:focus{border-color:#6a1b9a;box-shadow:0 0 0 3px rgba(106,27,154,.1)}
    .btn{width:100%;padding:13px;background:#6a1b9a;color:#fff;border:none;border-radius:10px;font-size:.95rem;font-weight:800;cursor:pointer;margin-top:8px;transition:background .18s}
    .btn:hover{background:#5c1690}
    .btn-outline{background:transparent;color:#6a1b9a;border:1.5px solid #6a1b9a}
    .error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:16px}
    .success{background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:16px}
    .steps{display:flex;justify-content:center;gap:8px;margin-bottom:28px}
    .step-dot{width:10px;height:10px;border-radius:50%;background:#e0d5f0}
    .step-dot.done{background:#6a1b9a}
    .warn{background:#fef3c7;border:1px solid #fcd34d;color:#92400e;border-radius:8px;padding:12px 14px;font-size:.83rem;margin-top:16px}
    a.link{color:#6a1b9a;font-weight:700}
  </style>
</head>
<body>
<div class="card">
  <div class="logo">ELLCY</div>
  <div class="sub">Setup Wizard</div>
  <div class="steps">
    <?php for($i=1;$i<=5;$i++): ?>
    <div class="step-dot <?= $i<=$step?'done':'' ?>"></div>
    <?php endfor; ?>
  </div>

  <?php if ($error): ?><div class="error">⚠ <?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success && $step < 5): ?><div class="success">✓ <?= htmlspecialchars($success) ?></div><?php endif; ?>

  <?php if ($step === 1): ?>
  <h2>Step 1 — Welcome</h2>
  <p style="color:#555;font-size:.88rem;margin-bottom:20px">
    This wizard will set up the ELLCY database and create your admin account.<br><br>
    Before continuing, make sure:<br>
    ✓ XAMPP is running (Apache + MySQL)<br>
    ✓ You've configured <code>config/database.php</code> with your DB details<br>
    ✓ MySQL is accessible
  </p>
  <a href="?step=2"><button class="btn">Test Database Connection →</button></a>

  <?php elseif ($step === 2): ?>
  <h2>Step 2 — Database Connected</h2>
  <div class="success" style="margin-bottom:20px">✓ Successfully connected to <strong><?= DB_NAME ?></strong></div>
  <p style="color:#555;font-size:.88rem;margin-bottom:20px">
    Ready to import the database schema. This will create all required tables and seed initial data.
  </p>
  <form method="POST" action="?step=3">
    <?= Security::csrfField() ?>
    <button type="submit" class="btn">Import Schema & Seed Data →</button>
  </form>

  <?php elseif ($step === 3): ?>
  <h2>Step 3 — Create Admin Account</h2>
  <form method="POST" action="?step=4">
    <?= Security::csrfField() ?>
    <div class="field">
      <label>Full Name</label>
      <input type="text" name="name" required placeholder="Your Name" value="<?= htmlspecialchars($_POST['name']??'') ?>"/>
    </div>
    <div class="field">
      <label>Email Address</label>
      <input type="email" name="email" required placeholder="admin@ellcy.in" value="<?= htmlspecialchars($_POST['email']??'') ?>"/>
    </div>
    <div class="field">
      <label>Password (min 8 characters)</label>
      <input type="password" name="password" required minlength="8"/>
    </div>
    <div class="field">
      <label>Confirm Password</label>
      <input type="password" name="confirm" required/>
    </div>
    <button type="submit" class="btn">Create Admin Account →</button>
  </form>

  <?php elseif ($step === 4): ?>
  <h2>Step 3 — Create Admin Account</h2>
  <?php // step=4 POST re-renders here with error or redirects to step 5 ?>
  <form method="POST" action="?step=4">
    <?= Security::csrfField() ?>
    <div class="field"><label>Full Name</label><input type="text" name="name" required value="<?= htmlspecialchars($_POST['name']??'') ?>"/></div>
    <div class="field"><label>Email Address</label><input type="email" name="email" required value="<?= htmlspecialchars($_POST['email']??'') ?>"/></div>
    <div class="field"><label>Password</label><input type="password" name="password" required minlength="8"/></div>
    <div class="field"><label>Confirm Password</label><input type="password" name="confirm" required/></div>
    <button type="submit" class="btn">Create Admin Account →</button>
  </form>

  <?php elseif ($step === 5): ?>
  <h2>🎉 Setup Complete!</h2>
  <div class="success">✓ ELLCY is ready to use!</div>
  <p style="color:#555;font-size:.88rem;margin:16px 0">Your admin account has been created. You can now log in to the admin panel.</p>
  <a href="admin/login"><button class="btn">Go to Admin Login →</button></a>
  <div class="warn">
    ⚠ <strong>Security:</strong> Please delete <code>setup.php</code> from your server immediately after completing setup.
  </div>
  <?php endif; ?>
</div>
</body>
</html>
