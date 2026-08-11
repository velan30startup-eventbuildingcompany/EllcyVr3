// ============================================================
// cart.js — ELLCY Cart Engine v2.0
// Shared across all pages. Load auth.js first, then data.js,
// then this file. This file owns ONLY cart storage (add/remove/
// buy now) and the "Add to Cart" / "Book Now" button wiring —
// it defers to window.EllcyAuth for anything about who the user
// is or where the login/account/booking pages live.
// ============================================================
(function () {
  'use strict';

  var STORAGE_KEY = 'ellcy_cart';
  var BUYNOW_KEY  = 'ellcy_buynow';

  /* auth.js must load before this file — see js/auth.js */
  var Auth = window.EllcyAuth || {
    /* Defensive fallback so the cart still works (minus login-
       aware redirects) even if auth.js failed to load for some
       reason — should never happen in normal operation. */
    getCartPath: function(){ return 'pages/cart.html'; },
    getBookingPath: function(){ return 'pages/booking.html'; },
    getLoginPath: function(){ return 'login'; },
    checkAuth: function(cb){ cb({ loggedIn: false, name: '' }); }
  };

  /* ── Cart data structure ────────────────────────────────────
     Each item: { id, title, price, image, slug, package, slot }
  ─────────────────────────────────────────────────────────── */
  function loadCart() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch(e) { return []; }
  }
  function saveCart(items) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    syncAllBadges();
    window.dispatchEvent(new CustomEvent('ellcy:cartchange', { detail: { items: items } }));
  }

  /* ── Image path normalizer ──────────────────────────────────
     Different pages sit at different folder depths (/pages/,
     /services/<slug>/, /services/<slug>/<sub>/, …) so the same
     image can arrive as "uploads/...", "../uploads/...",
     "../../uploads/..." etc. Rather than trying to fix the path
     at every place an item is *displayed*, we normalize it once
     here — at the single place every item enters the cart — by
     extracting everything from "uploads/" onward and storing it
     as a value that's safe to resolve from any known depth later.
  ─────────────────────────────────────────────────────────── */
  function normalizeImage(src) {
    if (!src) return '';
    var m = String(src).match(/uploads\/.*$/);
    return m ? m[0] : src;
  }

  window.EllcyCart = {
    getItems: loadCart,

    /* ── Buy Now / Book Now — Amazon-style instant checkout ───
       Stores ONLY this single item (does not touch the persistent
       cart) and sends the user straight to booking.html in
       "buynow" mode, skipping cart.html entirely.
       Requirement: browsing is always open, but this action itself
       requires login. If the user isn't logged in, we preserve the
       item and send them to /login, which returns them here after
       a successful login/registration.
    ─────────────────────────────────────────────────────────── */
    buyNow: function(item) {
      item = Object.assign({}, item, { image: normalizeImage(item.image) });
      try { sessionStorage.setItem(BUYNOW_KEY, JSON.stringify(item)); } catch(e) {}
      var bookingUrl = Auth.getBookingPath() + '?mode=buynow';
      /* Resolve to an absolute site path (starts with "/") — required
         because AuthController.returnTo() only accepts absolute
         internal paths (never an open redirect). */
      var bookingAbsolute;
      try { bookingAbsolute = new URL(bookingUrl, window.location.href).pathname + '?mode=buynow'; }
      catch (e) { bookingAbsolute = '/booking?mode=buynow'; }
      Auth.checkAuth(function(auth){
        if (auth.loggedIn) {
          window.location.href = bookingUrl;
        } else {
          var loginUrl = Auth.getLoginPath() + '?return_to=' + encodeURIComponent(bookingAbsolute);
          if (Auth.showLoginRequiredModal) {
            Auth.showLoginRequiredModal(loginUrl);
          } else {
            window.location.href = loginUrl; /* fallback if auth.js didn't load */
          }
        }
      });
    },

    add: function(item) {
      /* item: { id, title, price, image, slug, package, slot } */
      var cart = loadCart();
      item = Object.assign({}, item, { image: normalizeImage(item.image) });
      /* Prevent exact duplicate (same id + same package key) */
      var dup = cart.find(function(c){
        return c.id === item.id && c.package === item.package && c.slot === item.slot;
      });
      if (dup) { showToast('Already in cart — <a href="'+ Auth.getCartPath() +'">View Cart</a>', true); return; }
      cart.push(item);
      saveCart(cart);
      showToast('<i class="fa fa-check-circle"></i> Added to cart — <a href="'+ Auth.getCartPath() +'">View Cart</a>');
      syncAllBadges();
    },

    remove: function(uid) {
      var cart = loadCart().filter(function(c){ return c.uid !== uid; });
      saveCart(cart);
    },

    clear: function() { saveCart([]); },

    count: function() { return loadCart().length; },
  };

  /* ── Badge sync ─────────────────────────────────────────── */
  function syncAllBadges() {
    var n = loadCart().length;
    document.querySelectorAll('.cart-badge').forEach(function(el){
      el.textContent = n;
      el.style.display = n > 0 ? 'flex' : 'none';
    });
  }

  /* ── Toast notification ──────────────────────────────────── */
  var toastTimer;
  function showToast(html, warn) {
    var t = document.getElementById('ellcyToast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'ellcyToast';
      document.body.appendChild(t);
    }
    t.innerHTML = html;
    t.className = 'ellcy-toast' + (warn ? ' warn' : '') + ' show';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function(){ t.classList.remove('show'); }, 3200);
  }

  /* ── Inject Cart button into headers ───────────────────────
     Replaces or appends to .header-right / .cm-desktop-hdr.
     Also adds cart icon to mobile topbar.
  ─────────────────────────────────────────────────────────── */
  function injectCartButtons() {
    /* ① Home page header-right: already has cart btn in HTML — just sync */
    /* ② Inner pages: cart btn already in HTML — just sync badges */
    /* cart.js only needs to keep badges in sync */
    syncAllBadges();
  }

  function buildCartBtnHTML(id) {
    return '<a id="' + id + '" href="' + Auth.getCartPath() + '" class="cart-header-btn" aria-label="View cart">' +
      '<i class="fa-solid fa-cart-shopping"></i>' +
      '<span class="cart-btn-label">Cart</span>' +
      '<span class="cart-badge" style="display:none">0</span>' +
    '</a>';
  }

  /* ── Wire "Add to Cart" + "Book Now" on desc pages ─────────
     Requirement: every applicable service description page keeps
     BOTH a real "Add to Cart" action (adds to the persistent cart,
     EllcyCart.add) AND a separate "Book Now" action (instant
     single-item checkout, EllcyCart.buyNow) side by side. Request
     for Call (where present) is untouched.

     Some older templates (cm-btn-book / btnBookD-M used by the
     Chenda Melam + Dancers pages) only render a single button in
     HTML and expect cart.js to wire it up — for those we wire it
     as "Add to Cart" (preserving its existing label/icon) and then
     inject a matching "Book Now" button right next to it, exactly
     like every other template already does natively.
  ─────────────────────────────────────────────────────────── */
  function wireDescPageButtons() {
    /* These buttons are injected by service-desc.js via btnBookD / btnBookM */
    function patchBtn(el) {
      if (!el || el.dataset.cartWired) return;
      el.dataset.cartWired = '1';
      el.addEventListener('click', function(e) {
        e.preventDefault();
        EllcyCart.add(gatherCurrentItem());
      });
      injectBuyNowButton(el, 'cm-btn-buynow');
    }
    /* Try immediately and also after SD init (service-desc.js fires after DOMContentLoaded) */
    function tryPatch() {
      patchBtn(document.getElementById('btnBookD'));
      patchBtn(document.getElementById('btnBookM'));
    }
    tryPatch();
    setTimeout(tryPatch, 400); /* safety retry after service-desc.js renders */

    /* ── Snack-style pages (snk-btn-cart) ───────────────────────
       These already have their own inline script wiring the click
       to a real "Add to Cart" call — leave that alone. We only
       add the missing "Book Now" companion button next to it.
    ─────────────────────────────────────────────────────────── */
    function addBuyNowNextTo(id) {
      var btn = document.getElementById(id);
      if (!btn || !btn.classList.contains('snk-btn-cart')) return;
      injectBuyNowButton(btn, 'snk-btn-buynow');
    }
    addBuyNowNextTo('btnCartD');
    addBuyNowNextTo('btnCartM');

    /* ── bnc-btn-book pages (bouncers, enter-show-down, real-flowers) ──
       These already wire their own "Add to Cart" (EllcyCart.add) and
       inject their own "Book Now" companion button inline — cart.js
       must not touch them at all, or it double-binds click handlers
       and overwrites the "Add to Cart" label. Intentionally left
       untouched here.
    ─────────────────────────────────────────────────────────── */
  }

  /* ── Insert a "Buy Now" button right after a given Add-to-Cart
     button, sharing the same flex row. Skips items, only the
     currently-selected package/slot is sent — same data
     gatherCurrentItem() already collects for Add to Cart.
  ─────────────────────────────────────────────────────────── */
  function injectBuyNowButton(afterEl, cssClass) {
    if (!afterEl || !afterEl.parentNode) return;
    if (afterEl.parentNode.querySelector('.' + cssClass)) return; /* already injected */
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = cssClass;
    btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Book Now';
    btn.addEventListener('click', function(e){
      e.preventDefault();
      EllcyCart.buyNow(gatherCurrentItem());
    });
    afterEl.insertAdjacentElement('afterend', btn);
  }

  /* NOTE: the floating cart indicator has been removed site-wide
     per requirement — the header cart icon (with live badge count)
     is the single, consistent way to reach the cart on every page
     and every screen size. Any element with id="ellcyFloatCart"
     left over from a previous deploy is also removed defensively
     below, in case a page's HTML still contains stale markup. The
     sign-in widget, "Booking" link and mobile hamburger drawer now
     live in js/auth.js — see that file. */
  function removeStaleFloatingCart() {
    var stale = document.getElementById('ellcyFloatCart');
    if (stale) stale.remove();
  }

  /* Collect current service info from the page */
  function gatherCurrentItem() {
    var priceEl  = document.getElementById('sdPrice') || document.getElementById('sdPriceD') ||
                   document.getElementById('cmPrice') || document.getElementById('cmPriceD') ||
                   document.querySelector('.snk-price-val') ||
                   document.getElementById('mTotal') || document.getElementById('dTotal') ||
                   document.querySelector('.bnc-total-val b');
    var priceRaw = priceEl ? priceEl.textContent.replace(/[^0-9]/g,'') : '0';
    var price    = parseInt(priceRaw) || 0;

    var titleEl  = document.querySelector('.sd-title, .snk-title, .snk-page-title, .cm-page-title');
    var title    = titleEl ? titleEl.textContent.trim() : document.title.replace('ELLCY | ','');

    var imgEl    = document.querySelector('#sdImgMain, .sd-mosaic-main, .snk-mosaic-main, .cm-mosaic-main, #cmHeroImg, .cm-hero-wrap img, .snk-hero-img, .snk-img-main, .bnc-hero-img, .bnc-mosaic-main, #bncHeroImg');
    var image    = imgEl ? imgEl.getAttribute('src') : '';

    var activePill = document.querySelector('.sd-pkg-pill.active, .cm-pkg-pill.active, .cm-tr-card.active .cm-tr-name');
    var pkgLabel  = activePill ? activePill.textContent.trim() : '';

    var activeSlot = document.querySelector('.sd-slot-pill.active, .cm-slot-pill.active');
    var slot       = activeSlot ? activeSlot.textContent.trim() : 'Morning';

    /* unique id per cart entry */
    var uid = title.toLowerCase().replace(/\s+/g,'-') + '-' + (pkgLabel||'default') + '-' + slot;

    return {
      uid:     uid,
      id:      uid,
      title:   pkgLabel ? (title + ' – ' + pkgLabel) : title,
      price:   price,
      image:   image,
      package: pkgLabel || 'Standard',
      slot:    slot,
      page:    location.href,
    };
  }

  /* ── Run on DOM ready ─────────────────────────────────────── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    injectCartButtons();
    removeStaleFloatingCart();
    /* Wire desc page buttons if we're on a description page */
    if (document.querySelector('.cm-btn-book') || document.getElementById('btnBookD') ||
        document.querySelector('.snk-btn-cart') || document.querySelector('.bnc-btn-book')) {
      wireDescPageButtons();
    }
    syncAllBadges();
  }

})();
