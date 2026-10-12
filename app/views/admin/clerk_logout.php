<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex,nofollow"/>
  <title>Signing out — ELLCY Admin</title>
</head>
<body>
  <p>Signing out securely…</p>
  <script
    async
    crossorigin="anonymous"
    data-clerk-publishable-key="<?= Security::e($clerkPublishableKey) ?>"
    src="<?= Security::e($clerkScriptUrl) ?>"
    onload="window.Clerk.load().then(function(){return window.Clerk.signOut()}).finally(function(){location.replace(<?= json_encode(APP_URL . '/admin/login') ?>)})">
  </script>
  <noscript><a href="<?= Security::e(APP_URL . '/admin/login') ?>">Return to admin login</a></noscript>
</body>
</html>
