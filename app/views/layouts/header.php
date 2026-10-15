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
    $socialImage = $og_image ?? (rtrim(PUBLIC_URL, '/') . '/uploads/branding/ellcy-og-2026.jpg');
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
  <meta property="og:site_name" content="ELLCY"/>
  <meta property="og:locale" content="en_IN"/>
  <meta property="og:url" content="<?= Security::e($canonicalUrl) ?>"/>
  <meta property="og:image" content="<?= Security::e($socialImage) ?>"/>
  <meta property="og:image:width" content="1200"/>
  <meta property="og:image:height" content="630"/>
  <meta property="og:image:alt" content="ELLCY wedding and event experiences in Chennai"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="<?= Security::e($meta_title) ?>"/>
  <meta name="twitter:description" content="<?= Security::e($meta_description ?? '') ?>"/>
  <meta name="twitter:image" content="<?= Security::e($socialImage) ?>"/>
  <?php endif; ?>
  <title><?= Security::e($page_title ?? 'ELLCY | Event Services') ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= PUBLIC_URL ?>/uploads/branding/favicon.svg?v=20260922.1"/>
  <link rel="alternate icon" type="image/png" sizes="32x32" href="<?= PUBLIC_URL ?>/uploads/branding/favicon-32.png"/>
  <link rel="apple-touch-icon" sizes="180x180" href="<?= PUBLIC_URL ?>/uploads/branding/apple-touch-icon.png"/>
  <?php if (!empty($preload_image)): ?>
  <link rel="preload" as="image" href="<?= Security::e($preload_image) ?>" type="image/webp" fetchpriority="high"/>
  <?php endif; ?>
  <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/style.css?v=20261015.2"/>
  <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/cart.css?v=20261014.2"/>
  <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/brand.css?v=20260908.5"/>
  <?php if (!empty($extra_css)): ?>
    <?php foreach ((array)$extra_css as $css): ?>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/css/<?= Security::e($css) ?>"/>
    <?php endforeach; ?>
  <?php endif; ?>
  <link rel="stylesheet" href="<?= PUBLIC_URL ?>/vendor/fontawesome/css/all.min.css?v=6.5.0"/>
  <?php if ($canonicalPath === '/' || $canonicalPath === ''): ?>
  <script type="application/ld+json"><?= json_encode([
    '@context'=>'https://schema.org',
    '@graph'=>[
      ['@type'=>'Organization','@id'=>rtrim(APP_URL,'/').'/#organization','name'=>'ELLCY','url'=>APP_URL,
       'description'=>'Event services and celebration experiences for weddings and special occasions in Chennai.',
       'logo'=>rtrim(PUBLIC_URL,'/').'/uploads/branding/ellcy-logo-violet.png',
       'image'=>$socialImage,'telephone'=>'+919361011717',
       'areaServed'=>['@type'=>'City','name'=>'Chennai']],
      ['@type'=>'WebSite','@id'=>rtrim(APP_URL,'/').'/#website','name'=>'ELLCY','url'=>APP_URL,
       'publisher'=>['@id'=>rtrim(APP_URL,'/').'/#organization'],'inLanguage'=>'en-IN',
       'description'=>'Discover and book wedding and event services in Chennai.']
    ]
  ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG) ?></script>
  <?php endif; ?>
</head>
<body class="<?= Security::e($body_class ?? '') ?>">
