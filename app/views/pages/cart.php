<?php
$page_title       = 'ELLCY | My Cart';
$meta_description = 'Review your selected event services and proceed to booking.';
$extra_css        = ['header2.css'];
require VIEWS_PATH . '/layouts/header.php';
?>

<header class="header new-header" role="banner">
  <button class="hdr-back-btn" onclick="_ellcySmartBack('<?= APP_URL ?>/')" aria-label="Go back">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i><span>Back</span>
  </button>
  <a class="logo" href="<?= APP_URL ?>/" aria-label="ELLCY Home">ELLCY</a>
  <a href="<?= APP_URL ?>/cart" class="cart-header-btn hdr-cart-right" aria-label="View cart">
    <i class="fa-solid fa-cart-shopping"></i>
    <span class="cart-btn-label">Cart</span>
    <span class="cart-badge" style="display:none">0</span>
  </a>
</header>

<div class="cart-page-wrap">
  <div class="cart-items-col">
    <h2 id="cartHeading">My Cart</h2>
    <div id="cartItemsList"></div>
  </div>
  <aside class="cart-summary" id="cartSummary">
    <h3>Order Summary</h3>
    <div id="cartSummaryRows"></div>
    <div class="cart-summary-row total">
      <span>Total</span>
      <span id="cartTotal">₹0</span>
    </div>
    <a id="cartCheckoutBtn" href="<?= APP_URL ?>/booking" class="cart-checkout-btn">
      <i class="fa-solid fa-paper-plane"></i> Proceed to Booking
    </a>
    <p class="cart-enquiry-note">Our team will call you back within <strong>2 hours</strong> to confirm your slot and pricing.</p>
    <a class="cart-continue-link" href="<?= APP_URL ?>/">← Continue Browsing</a>
    <div class="cart-trust">
      <div class="cart-trust-item"><i class="fa-solid fa-shield-halved"></i> Secure Booking</div>
      <div class="cart-trust-item"><i class="fa-solid fa-headset"></i> 2-hr Response</div>
      <div class="cart-trust-item"><i class="fa-solid fa-star"></i> 1000+ Happy Clients</div>
    </div>
  </aside>
</div>

<script>function _ellcySmartBack(u){if(window.history.length>1&&document.referrer&&document.referrer.includes(window.location.hostname)){window.history.back();}else{window.location.href=u;}}</script>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>
