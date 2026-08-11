<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>ELLCY | Page Not Found</title>
  <style>
    body{font-family:'Segoe UI',system-ui,sans-serif;background:#f4e9ff;min-height:100vh;display:flex;align-items:center;justify-content:center;margin:0;padding:20px}
    .wrap{text-align:center;max-width:460px}
    .num{font-size:5rem;font-weight:900;color:#6a1b9a;line-height:1;margin-bottom:12px}
    h1{font-size:1.4rem;font-weight:700;color:#1a1a2e;margin-bottom:8px}
    p{color:#666;margin-bottom:28px}
    a{display:inline-flex;align-items:center;gap:8px;background:#6a1b9a;color:#fff;padding:12px 28px;border-radius:10px;text-decoration:none;font-weight:700;transition:background .18s}
    a:hover{background:#5c1690}
  </style>
</head>
<body>
<div class="wrap">
  <div class="num">404</div>
  <h1>Page Not Found</h1>
  <p>The page you're looking for doesn't exist or may have been moved.</p>
  <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>/">← Back to Home</a>
</div>
</body>
</html>
