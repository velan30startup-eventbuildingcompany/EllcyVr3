<?php
$page_title       = 'ELLCY | Event Booking — Chennai\'s Premier Platform';
$meta_description = 'Book dancers, chenda melam, DJ, catering, photography and 17+ event services for your wedding, birthday or celebration in Chennai.';
$meta_title       = 'ELLCY — Chennai\'s Premier Event Services Platform';
$extra_css        = [];
require VIEWS_PATH . '/layouts/header.php';
?>

<!-- HEADER -->
<header class="header" role="banner">
  <div class="header-left">
    <h1 class="logo" onclick="window.location.href='<?= APP_URL ?>'" aria-label="ELLCY Home" style="cursor:pointer">ELLCY</h1>
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

<?php
$extra_js  = ['script.js'];
require VIEWS_PATH . '/layouts/footer.php';
?>
