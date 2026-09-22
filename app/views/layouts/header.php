<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="description" content="<?= Security::e($meta_description ?? 'Book event services in Chennai with ELLCY.') ?>"/>
  <?php
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $canonicalPath = APP_BASE !== '' && str_starts_with($requestPath, APP_BASE) ? substr($requestPath, strlen(APP_BASE)) : $requestPath;
    $canonicalUrl = rtrim(APP_URL, '/') . '/' . ltrim($canonicalPath, '/');
    $isPrivatePage = (bool)preg_match('#^/(admin|account|login|register|forgot-password|reset-password|cart|booking)(/|$)#', $canonicalPath);
    $robotsValue = $robots ?? ($isPrivatePage ? 'noindex, nofollow' : 'index, follow');
    $socialImage = $og_image ?? (rtrim(APP_URL, '/') . '/uploads/services/stage.webp');
  ?>
  <meta name="robots" content="<?= Security::e($robotsValue) ?>"/>
  <link rel="canonical" href="<?= Security::e($canonicalUrl) ?>"/>
  <meta name="referrer" content="strict-origin-when-cross-origin"/>
  <meta http-equiv="X-Content-Type-Options" content="nosniff"/>
  <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()"/>
  <?php if (!empty($meta_title)): ?>
  <meta property="og:title" content="<?= Security::e($meta_title) ?>"/>
  <meta property="og:description" content="<?= Security::e($meta_description ?? '') ?>"/>
  <meta property="og:type" content="website"/>
  <meta property="og:url" content="<?= Security::e($canonicalUrl) ?>"/>
  <meta property="og:image" content="<?= Security::e($socialImage) ?>"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <?php endif; ?>
  <title><?= Security::e($page_title ?? 'ELLCY | Event Services') ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= PUBLIC_URL ?>/uploads/branding/favicon.svg?v=20260922.1"/>
  <link rel="alternate icon" type="image/png" sizes="32x32" href="<?= PUBLIC_URL ?>/uploads/branding/favicon-32.png"/>
  <link rel="apple-touch-icon" sizes="180x180" href="<?= PUBLIC_URL ?>/uploads/branding/apple-touch-icon.png"/>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/style.css"/>
  <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/cart.css?v=20260903.2"/>
  <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/brand.css?v=20260908.5"/>
  <?php if (!empty($extra_css)): ?>
    <?php foreach ((array)$extra_css as $css): ?>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/<?= Security::e($css) ?>"/>
    <?php endforeach; ?>
  <?php endif; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
  <script type="application/ld+json"><?= json_encode([
    '@context'=>'https://schema.org','@type'=>'LocalBusiness','name'=>'ELLCY',
    'description'=>'Tamil wedding and event services marketplace for customers in Chennai.',
    'url'=>APP_URL,'logo'=>rtrim(PUBLIC_URL, '/').'/uploads/branding/ellcy-logo-violet.png',
    'image'=>rtrim(APP_URL, '/').'/uploads/services/stage.webp','telephone'=>'+919361011717',
    'address'=>['@type'=>'PostalAddress','addressLocality'=>'Chennai','addressRegion'=>'Tamil Nadu','addressCountry'=>'IN'],
    'areaServed'=>['@type'=>'City','name'=>'Chennai'],
    'knowsAbout'=>['Tamil weddings','Wedding decoration','Wedding catering','Wedding photography','Bridal make over','Traditional wedding music']
  ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></script>
  <script type="application/ld+json"><?= json_encode([
    '@context'=>'https://schema.org','@type'=>'WebSite','name'=>'ELLCY','url'=>APP_URL,
    'description'=>'Discover and book event services for Tamil weddings and celebrations in Chennai.'
  ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body class="<?= Security::e($body_class ?? '') ?>">
