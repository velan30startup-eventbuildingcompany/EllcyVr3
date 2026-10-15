<?php
$page_title       = 'Wedding & Event Services in Chennai | ELLCY';
$meta_description = 'Discover wedding and event services in Chennai with ELLCY. Explore DJs, Chenda Melam, brass bands, stage décor, photography, catering and more.';
$meta_title       = 'Wedding & Event Services in Chennai | ELLCY';
$extra_css        = [];
$preload_image    = PUBLIC_URL . '/uploads/home/service-dj.webp';
$minimal_home_scripts = true;
require VIEWS_PATH . '/layouts/header.php';
?>

<!-- HEADER -->
<header class="header" role="banner">
  <div class="header-left">
    <a class="logo" href="<?= APP_URL ?>/" aria-label="ELLCY Home">ELLCY</a>
  </div>
  <div class="header-center desktop-only">
    <div class="search-box" id="desktopSearchTrigger" role="button" tabindex="0"
         aria-label="Open search" aria-haspopup="dialog">
      <i class="fa fa-search" aria-hidden="true"></i>
      <span class="placeholder">Search services, prices, events…</span>
      <span class="search-hint-chips">
        <span class="chip">DJ under ₹20k</span>
        <span class="chip">Wedding stage</span>
        <span class="chip">Band Set</span>
      </span>
    </div>
  </div>
  <div class="header-right">
    <a id="homeCartBtn" href="<?= APP_URL ?>/cart" class="cart-header-btn" aria-label="View cart">
      <i class="fa-solid fa-cart-shopping"></i>
      <span class="cart-btn-label">Cart</span>
      <span class="cart-badge" style="display:none">0</span>
    </a>
  </div>
</header>

<!-- MOBILE SEARCH -->
<div class="mobile-search-below" role="search">
  <div class="search-box" id="mobileSearchTrigger" role="button" tabindex="0"
       aria-label="Open search" aria-haspopup="dialog">
    <i class="fa fa-search" aria-hidden="true"></i>
    <span class="placeholder">Search services or prices…</span>
  </div>
</div>

<main>
<section class="ellcy-seo-hero" aria-labelledby="ellcy-home-title">
  <div class="ellcy-seo-hero__inner">
    <p class="ellcy-seo-kicker">ELLCY — Event Services &amp; Celebration Experiences</p>
    <h1 id="ellcy-home-title">Wedding &amp; Event Services in Chennai</h1>
    <p class="ellcy-seo-tagline">Celebrate Beyond Ordinary.</p>
    <p class="ellcy-seo-lead">Discover trusted professionals and thoughtfully curated experiences for Tamil weddings, receptions and special occasions—from Chenda Melam and brass bands to décor, photography, DJ entertainment, bridal styling and catering.</p>
    <div class="ellcy-seo-actions">
      <a href="<?= APP_URL ?>/services">Explore All Services</a>
      <a class="secondary" href="<?= APP_URL ?>/request-for-call">Plan Your Celebration</a>
    </div>
  </div>
</section>

<!-- SMART SEARCH PANEL -->
<div class="search-panel" id="searchPanel" role="dialog" aria-modal="true" aria-label="Search" aria-hidden="true">
  <div class="search-header">
    <div class="search-input-wrap">
      <i class="fa fa-search search-icon" aria-hidden="true"></i>
      <input id="searchInput" type="text"
             placeholder="Try: DJ under 20000, Wedding stage, Chenda Melam…"
             autocomplete="off" autocorrect="off" spellcheck="false"
             aria-label="Search services"/>
      <button class="clear-search" id="clearSearch" aria-label="Clear search" hidden>
        <i class="fa fa-times"></i>
      </button>
    </div>
    <button class="close-btn" id="closeSearch" aria-label="Close search">✕</button>
  </div>
  <div class="search-suggestions" id="searchSuggestions">
    <p class="suggest-label">Popular searches</p>
    <div class="suggest-chips">
      <button class="suggest-chip" data-q="DJ under 20000">🎵 DJ under ₹20,000</button>
      <button class="suggest-chip" data-q="stage decoration">🌸 Stage Decoration</button>
      <button class="suggest-chip" data-q="photography">📷 Photography</button>
      <button class="suggest-chip" data-q="chenda melam">🥁 Chenda Melam</button>
      <button class="suggest-chip" data-q="bridal makeup">💄 Bridal Styling</button>
      <button class="suggest-chip" data-q="catering">🙏 Catering</button>
    </div>
  </div>
  <div class="search-results-wrap" id="searchResultsWrap" hidden>
    <p class="results-meta" id="resultsMeta"></p>
    <ul class="results" id="resultsList" role="listbox"></ul>
    <div class="no-results" id="noResults" hidden>
      <i class="fa fa-search-minus"></i>
      <p>No services found. Try different keywords.</p>
    </div>
  </div>
</div>

<!-- EVENT SERVICES CIRCLES -->
<section class="event-category" aria-labelledby="svc-heading">
  <h2 id="svc-heading">Our Event Services</h2>
  <div class="category-scroll" id="categoryContainer" role="list"></div>
</section>

<!-- EVENT CATEGORY TILES -->
<section class="event-services" aria-labelledby="evt-heading">
  <h2 id="evt-heading">Our Event Category</h2>
  <div class="service-grid" id="servicesContainer"></div>
</section>

<section class="ellcy-experiences" aria-labelledby="experience-heading">
  <div class="ellcy-experiences__inner">
    <p class="ellcy-seo-kicker">Curated for meaningful celebrations</p>
    <h2 id="experience-heading">Discover Experiences for Every Celebration</h2>
    <p class="ellcy-experiences__intro">Compare genuine service options, understand what is included and choose an experience that fits your celebration.</p>
    <div class="ellcy-experience-grid">
      <a href="<?= APP_URL ?>/services/music-performers/chenda-melam/"><h3>Traditional Music &amp; Grand Entrances</h3><p>Celebrate culture with Chenda Melam and wedding brass bands in ensemble sizes suited to your occasion.</p><span>Explore traditional music →</span></a>
      <a href="<?= APP_URL ?>/services/dj/"><h3>DJ &amp; Live Entertainment</h3><p>Set the mood for weddings, receptions and parties with celebration-ready music experiences.</p><span>Explore DJ experiences →</span></a>
      <a href="<?= APP_URL ?>/services/decoration/"><h3>Wedding Stage &amp; Event Décor</h3><p>Transform your venue with elegant stage settings, lighting and memorable celebration backdrops.</p><span>Explore decoration →</span></a>
      <a href="<?= APP_URL ?>/services/photography/"><h3>Photography &amp; Wedding Memories</h3><p>Capture the emotions and stories of your wedding through photography and visual storytelling.</p><span>Explore photography →</span></a>
      <a href="<?= APP_URL ?>/services/bridal-groom-styling/"><h3>Bridal Styling &amp; Make Over</h3><p>Discover bridal and groom styling experiences designed around individual preferences.</p><span>Explore make over →</span></a>
      <a href="<?= APP_URL ?>/services/food/"><h3>Catering &amp; Celebration Dining</h3><p>Bring guests together with food and hospitality for weddings, receptions and family celebrations.</p><span>Explore catering →</span></a>
    </div>
    <div class="ellcy-final-cta"><h2>Make Your Next Celebration Extraordinary</h2><p>Beautiful moments begin with thoughtful planning.</p><div class="ellcy-seo-actions"><a href="<?= APP_URL ?>/services">Explore All Services</a><a class="secondary" href="<?= APP_URL ?>/request-for-call">Enquire Now</a></div></div>
  </div>
</section>
</main>

<?php
$extra_js  = ['script.js'];
require VIEWS_PATH . '/layouts/footer.php';
?>
