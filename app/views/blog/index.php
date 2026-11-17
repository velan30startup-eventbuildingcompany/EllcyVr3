<?php
$body_class = 'blog-page';
require VIEWS_PATH . '/layouts/header.php';
?>
<header class="header new-header" role="banner">
  <span class="hdr-mobile-title">ELLCY Blog</span>
  <a class="logo" href="<?= APP_URL ?>/" aria-label="ELLCY Home">ELLCY</a>
  <a href="<?= APP_URL ?>/services" class="cart-header-btn hdr-cart-right">Explore Services</a>
</header>

<main>
  <section class="blog-hero">
    <div class="blog-shell blog-hero__inner">
      <div>
        <p class="blog-kicker">ELLCY Planning Journal</p>
        <h1>Wedding and Event Planning Ideas for Chennai</h1>
        <p>Practical guidance for Tamil weddings, receptions, engagements and meaningful occasions—from choosing vendors to creating a smoother online booking experience.</p>
      </div>
      <img src="<?= PUBLIC_URL ?>/uploads/branding/ellcy-celebrate-wedding.webp?v=20261016.2" width="1200" height="630" alt="Tamil wedding celebration planned with ELLCY" fetchpriority="high"/>
    </div>
  </section>

  <section class="blog-index blog-shell" aria-labelledby="latest-guides">
    <p class="blog-kicker" style="color:#6a1b9a">Planning resources</p>
    <h2 id="latest-guides">Latest ELLCY guide</h2>
    <article class="blog-card">
      <img src="<?= PUBLIC_URL ?>/uploads/home/event-wedding.webp" width="560" height="420" alt="Tamil bride and groom during a wedding celebration" loading="lazy"/>
      <div class="blog-card__body">
        <p class="blog-card__meta"><time datetime="2026-11-18">18 November 2026</time> · 12 minute read</p>
        <h3>Complete Wedding and Event Planning Guide for Chennai</h3>
        <p>How to organise a marriage, reception or special occasion with the right sequence, realistic vendor comparisons and one connected booking journey.</p>
        <a href="<?= APP_URL ?>/blog/complete-wedding-event-planning-chennai">Read the complete guide →</a>
      </div>
    </article>
  </section>
</main>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>
