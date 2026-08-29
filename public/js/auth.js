// ============================================================
// auth.js — ELLCY Auth & Account Widget
// Handles: "who is the user" (session check), the header
// Sign-in/Account widget, the "Booking" header link, and the
// mobile hamburger drawer (Amazon.in-style).
//
// This file must be loaded BEFORE cart.js on every page, since
// cart.js's Buy Now flow calls window.EllcyAuth.checkAuth() and
// window.EllcyAuth.getLoginPath(). cart.js itself only owns cart
// storage (add/remove/buy now) — it has no idea who's logged in.
// ============================================================
(function () {
  'use strict';

  /* ── Root path (works from any folder depth) ─────────────
     Derives the path back to the site root from the very
     <script src="…/js/auth.js"> tag that loaded this file. This
     stays correct no matter how deeply nested the current page is
     (root, /pages/, /services/X/, /services/X/Y/, …) instead of
     guessing. cart.js and any other script can read this off
     window.EllcyAuth.ROOT_PREFIX rather than recomputing it.
  ─────────────────────────────────────────────────────────── */
  var ROOT_PREFIX = (function () {
    if (typeof window.ELLCY_BASE === 'string' && window.ELLCY_BASE !== '') {
      return window.ELLCY_BASE.replace(/\/$/, '') + '/';
    }
    var thisScript = document.currentScript;
    if (!thisScript) {
      var scripts = document.getElementsByTagName('script');
      for (var i = 0; i < scripts.length; i++) {
        if (/(^|\/)auth\.js(\?.*)?$/.test(scripts[i].getAttribute('src') || '')) {
          thisScript = scripts[i];
          break;
        }
      }
    }
    var src = thisScript ? (thisScript.src || thisScript.getAttribute('src') || '') : '';
    try {
      var scriptUrl = new URL(src, document.baseURI);
      var path = scriptUrl.pathname;
      var publicMarker = '/public/js/auth.js';
      var legacyMarker = '/js/auth.js';
      var marker = path.indexOf(publicMarker) >= 0 ? publicMarker : legacyMarker;
      var idx = path.indexOf(marker);
      return idx >= 0 ? path.slice(0, idx + 1) : '/';
    } catch (e) {
      return '/';
    }
  })();

  function getCartPath()     { return ROOT_PREFIX + 'cart'; }
  function getBookingPath()  { return ROOT_PREFIX + 'booking'; }
  /* "My Bookings" nav links (header ribbon, hamburger drawer, account
     dropdown) must point at the user's actual booking HISTORY — the
     "My Bookings & Orders" list on the account page (service, date,
     price, status) — not at pages/booking.html, which is the
     "Complete Your Booking" CHECKOUT flow for a booking in progress.
     Those are two different pages; mixing them up sent people who
     just wanted to see what they'd already booked into a blank/wrong
     checkout screen instead. */
  function getMyBookingsPath() { return getAccountPath() + '#orders'; }
  function getLoginPath()    { return ROOT_PREFIX + 'login'; }
  function getRegisterPath() { return ROOT_PREFIX + 'register'; }
  function getAccountPath()  { return ROOT_PREFIX + 'account'; }
  function getAuthMePath()   { return ROOT_PREFIX + 'api/auth/me'; }
  function currentPathForReturn() {
    try { return window.location.pathname + window.location.search; }
    catch (e) { return '/'; }
  }

  /* ── Auth check (Requirement: Book Now is gated, browsing isn't) ──
     Cached for the lifetime of the page so repeated clicks don't
     re-fetch. Cache is intentionally NOT persisted across page loads.
  ─────────────────────────────────────────────────────────── */
  var _authCache = null;
  function checkAuth(cb) {
    if (_authCache !== null) { cb(_authCache); return; }
    fetch(getAuthMePath(), { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(data){ _authCache = { loggedIn: !!data.logged_in, name: data.name || '' }; cb(_authCache); })
      .catch(function(){ _authCache = { loggedIn: false, name: '' }; cb(_authCache); });
  }

  /* ── "Please log in to book" modal ─────────────────────────
     Shown instead of a silent redirect whenever the user tries to
     complete a booking (Buy Now, or the final Confirm Booking step)
     while logged out. Browsing/adding to cart stays open — this is
     only shown at the actual booking action.
  ─────────────────────────────────────────────────────────── */
  function showLoginRequiredModal(loginUrl) {
    var old = document.getElementById('ellcyLoginReqModal');
    if (old) old.remove();

    var overlay = document.createElement('div');
    overlay.id = 'ellcyLoginReqModal';
    overlay.className = 'ellcy-loginreq-overlay';
    overlay.innerHTML =
      '<div class="ellcy-loginreq-box" role="dialog" aria-modal="true" aria-label="Sign in required">' +
        '<button type="button" class="ellcy-loginreq-close" aria-label="Close">' +
          '<i class="fa-solid fa-xmark"></i></button>' +
        '<div class="ellcy-loginreq-emoji">🔐</div>' +
        '<h3>Sign in to book this</h3>' +
        '<p>You&rsquo;re almost there! Please log in (or create a free account) to' +
           ' confirm your booking &mdash; it only takes a moment.</p>' +
        '<a class="ellcy-loginreq-btn" href="' + loginUrl + '">' +
          '<i class="fa-solid fa-right-to-bracket"></i> Log In / Sign Up</a>' +
        '<button type="button" class="ellcy-loginreq-later">Maybe later</button>' +
      '</div>';
    document.body.appendChild(overlay);
    document.body.classList.add('ellcy-drawer-locked');

    function close() {
      overlay.remove();
      document.body.classList.remove('ellcy-drawer-locked');
    }
    overlay.addEventListener('click', function(e){ if (e.target === overlay) close(); });
    overlay.querySelector('.ellcy-loginreq-close').addEventListener('click', close);
    overlay.querySelector('.ellcy-loginreq-later').addEventListener('click', close);
  }

  /* ── Header account/booking widget + mobile hamburger drawer ──
     (Amazon.in reference layout: [Sign in / Account] [Returns &
     Orders] [Cart] on desktop; everything but the cart icon tucked
     behind a hamburger "≡" on mobile.)

     Injected next to the existing cart icon in every header
     variant (they all reliably contain a ".cart-badge" element),
     so this works across every page template without needing to
     hand-edit dozens of individual header markups.

     Desktop (≥769px): "Hello, sign in / Account" widget, then a
     "Booking" link (our equivalent of Amazon's "Returns & Orders"),
     then the Cart icon — always in that fixed order, always at the
     top-right, never reshuffled by page state.

     Mobile (<769px): a "≡" button is injected at the START of the
     header (top-left, like Amazon.in's app). Tapping it slides out
     a drawer from the left containing, top to bottom: Sign in /
     Account, Booking, My Orders / Cart, Logout — mirroring Amazon's
     own mobile menu. The Cart icon itself stays visible in the top
     bar so it's reachable in one tap, matching requirement #12.

     Styling lives in css/cart.css (.ellcy-acct*, .ellcy-hamburger*,
     .ellcy-drawer*) so it stays themeable and consistent instead of
     scattered inline styles.
  ─────────────────────────────────────────────────────────── */
  function removeStaleFloatingCart() {
    var stale = document.getElementById('ellcyFloatCart');
    if (stale) stale.remove();
  }

  function ensureResponsiveHeaderStyles() {
    if (document.getElementById('ellcyResponsiveHeaderPatch')) return;
    var style = document.createElement('style');
    style.id = 'ellcyResponsiveHeaderPatch';
    style.textContent =
      '@media (min-width:769px){' +
      '.ellcy-hamburger{display:none!important}' +
      '.ellcy-right-actions{display:inline-flex!important;align-items:center!important;margin-left:auto!important}' +
      'header .ellcy-mobile-brand{position:static!important;inset:auto!important;transform:none!important;margin-left:0!important;text-align:left!important}' +
      '}' +
      '@media (max-width:768px){' +
      '.ellcy-hamburger{display:flex!important;order:0!important}' +
      'header .header-right{order:3!important;margin-left:auto!important;flex-shrink:0!important}' +
      '.ellcy-right-actions{order:3!important;margin-left:auto!important;gap:6px!important;flex-shrink:0!important}' +
      '.ellcy-booking-link{display:none!important}' +
      '.ellcy-acct{display:inline-flex!important}' +
      '.ellcy-acct-text{display:none!important}' +
      '.ellcy-acct-btn{padding:5px!important;background:#fff!important;color:#6a1b9a!important;border-radius:50%!important}' +
      '.ellcy-acct-icon,.ellcy-acct-avatar{width:28px!important;height:28px!important;background:#fff!important;color:#6a1b9a!important;border-color:#fff!important}' +
      'header button[class*="back-btn"],header a[class*="back-btn"]{display:none!important}' +
      'header .ellcy-mobile-brand-wrap{display:block!important;position:static!important;order:1!important;flex:1 1 auto!important;min-width:0!important;margin:0 4px!important;text-align:left!important}' +
      'header .ellcy-mobile-brand,header .ellcy-mobile-context,header .sd-mobile-context{display:block!important;position:static!important;inset:auto!important;transform:none!important;order:1!important;flex:1 1 auto!important;min-width:0!important;max-width:none!important;margin:0 4px!important;padding:0!important;color:#fff!important;text-decoration:none!important;text-align:left!important;font-size:.92rem!important;font-weight:800!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;pointer-events:auto!important}' +
      'header .ellcy-mobile-brand{font-size:1.02rem!important}' +
      '.ellcy-drawer-signin,.ellcy-drawer-createacct,.ellcy-drawer-account a{color:#fff!important}' +
      '.ellcy-drawer-nav a,.ellcy-drawer-nav a i{color:#1a1a2e!important}' +
      '}';
    document.head.appendChild(style);
  }

  function buildDrawer(auth) {
    removeStaleFloatingCart();
    var old = document.getElementById('ellcyDrawer');
    if (old) old.remove();
    var oldBackdrop = document.getElementById('ellcyDrawerBackdrop');
    if (oldBackdrop) oldBackdrop.remove();

    var backdrop = document.createElement('div');
    backdrop.id = 'ellcyDrawerBackdrop';
    backdrop.className = 'ellcy-drawer-backdrop';

    var drawer = document.createElement('div');
    drawer.id = 'ellcyDrawer';
    drawer.className = 'ellcy-drawer';

    var returnQS = '?return_to=' + encodeURIComponent(currentPathForReturn());
    var accountBlock = auth.loggedIn
      ? ('<div class="ellcy-drawer-account">' +
           '<span class="ellcy-acct-avatar">' + (auth.name || 'A').trim().charAt(0).toUpperCase() + '</span>' +
           '<div><strong>Hello, ' + ((auth.name || 'Account').trim().split(' ')[0]) + '</strong>' +
           '<a href="' + getAccountPath() + '">My Profile</a></div>' +
         '</div>')
      : ('<div class="ellcy-drawer-account">' +
           '<a class="ellcy-drawer-signin" href="' + getLoginPath() + returnQS + '">' +
             '<i class="fa-solid fa-user"></i> Hello, sign in</a>' +
           '<a class="ellcy-drawer-createacct" href="' + getRegisterPath() + returnQS + '">Create account</a>' +
         '</div>');

    drawer.innerHTML =
      '<div class="ellcy-drawer-head">' + accountBlock +
        '<button type="button" class="ellcy-drawer-close" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>' +
      '</div>' +
      '<nav class="ellcy-drawer-nav">' +
        '<a href="' + ROOT_PREFIX + '"><i class="fa-solid fa-house"></i> Home</a>' +
        '<a href="' + getMyBookingsPath() + '"><i class="fa-solid fa-calendar-check"></i> My Bookings</a>' +
        '<a href="' + getCartPath() + '"><i class="fa-solid fa-cart-shopping"></i> My Orders / Cart</a>' +
        (auth.loggedIn ? '<a href="' + getAccountPath() + '#settings"><i class="fa-solid fa-gear"></i> Account Settings</a>' : '') +
        (auth.loggedIn ? '<a href="' + ROOT_PREFIX + 'logout" class="ellcy-drawer-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>' : '') +
      '</nav>';

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    function closeDrawer() {
      drawer.classList.remove('open');
      backdrop.classList.remove('open');
      document.body.classList.remove('ellcy-drawer-locked');
    }
    function openDrawer() {
      drawer.classList.add('open');
      backdrop.classList.add('open');
      document.body.classList.add('ellcy-drawer-locked');
    }
    backdrop.addEventListener('click', closeDrawer);
    drawer.querySelector('.ellcy-drawer-close').addEventListener('click', closeDrawer);

    return openDrawer;
  }

  function injectAuthWidget() {
    ensureResponsiveHeaderStyles();
    var badges = document.querySelectorAll('.cart-badge');
    var seenParents = [];
    var openDrawerFn = null;

    badges.forEach(function(badge){
      var cartLink = badge.closest('a');
      if (!cartLink || !cartLink.parentNode) return;
      if (seenParents.indexOf(cartLink.parentNode) !== -1) return; /* avoid double-inject */
      if (cartLink.parentNode.querySelector('.ellcy-acct')) return;
      seenParents.push(cartLink.parentNode);

      var headerEl = cartLink.closest('header') || cartLink.parentNode;

      /* ── Hamburger button (mobile-only, shown via CSS) ─────── */
      var burger = headerEl.querySelector('.ellcy-hamburger');
      if (!burger) {
        burger = document.createElement('button');
        burger.type = 'button';
        burger.className = 'ellcy-hamburger';
        burger.setAttribute('aria-label', 'Open menu');
        burger.innerHTML = '<i class="fa-solid fa-bars"></i>';
        headerEl.insertBefore(burger, headerEl.firstChild);
        burger.addEventListener('click', function(){ if (openDrawerFn) openDrawerFn(); });
      }

      /* Detail templates historically used a back button as the mobile title.
         Keep the useful page name next to the hamburger, while navigation now
         follows the site's normal menu and breadcrumbs. */
      var pageContext = headerEl.querySelector('.hdr-mobile-title');
      /* Older service templates use several logo class names (snk-logo,
         cm-logo, pd-logo, and others).  The accessible home label is the
         stable contract shared by all of them, so use it as the primary
         selector and retain the legacy class fallbacks. */
      var nativeBrand = headerEl.querySelector(
        'a[aria-label="ELLCY Home"], .logo, .sd-logo, .snk-logo, .cm-logo, .pd-logo, .bnc-logo'
      );
      if (pageContext) pageContext.classList.add('ellcy-mobile-context');
      else if (nativeBrand) {
        nativeBrand.classList.add('ellcy-mobile-brand');
        if (nativeBrand.parentElement && nativeBrand.parentElement !== headerEl) {
          nativeBrand.parentElement.classList.add('ellcy-mobile-brand-wrap');
        }
      }
      if (!pageContext && !nativeBrand && !headerEl.querySelector('.sd-mobile-context, .ellcy-mobile-context')) {
        var oldBack = headerEl.querySelector('button[class*="back-btn"], a[class*="back-btn"]');
        var labelNode = oldBack && oldBack.querySelector('span');
        var label = labelNode ? labelNode.textContent.trim() : '';
        if (!label || /^back$/i.test(label)) {
          var heading = document.querySelector('main h1, h1');
          label = heading ? heading.textContent.trim() : 'Services';
        }
        if (label) {
          var context = document.createElement('a');
          context.className = 'ellcy-mobile-context';
          context.href = ROOT_PREFIX + 'services';
          context.textContent = label;
          burger.insertAdjacentElement('afterend', context);
        }
      }

      /* ── Right-side actions group ────────────────────────────
         IMPORTANT: some header templates (e.g. the mobile
         ".cm-topbar" used on service description pages) are a bare
         2-item flex row — a Back button and the Cart link — laid
         out with justify-content:space-between. Inserting the
         Booking link and Account widget as loose siblings into that
         same row let space-between spread all four items evenly
         across the header, which visually separated the avatar from
         Cart and grouped it with the Back button instead (the
         reported "avatar moves to the left" bug).

         Fixing this properly: wrap Booking + Cart + Avatar in one
         flex container and drop that container in at the cart
         link's original position. Whatever the outer header's own
         layout rules are, Cart and Avatar now always move as a
         single, adjacent unit. Order is Booking -> Cart -> Avatar,
         so the avatar sits immediately next to Cart as required. ── */
      var actionsGroup = document.createElement('div');
      actionsGroup.className = 'ellcy-right-actions';
      cartLink.parentNode.insertBefore(actionsGroup, cartLink);

      /* ── Booking link (desktop's "Returns & Orders" equivalent) ── */
      var bookingLink = document.createElement('a');
      bookingLink.className = 'ellcy-booking-link';
      bookingLink.href = getMyBookingsPath();
      bookingLink.innerHTML =
        '<span class="ellcy-booking-line1">My</span>' +
        '<span class="ellcy-booking-line2">Booking</span>';
      actionsGroup.appendChild(bookingLink);

      /* Move the existing cart link into the group (same DOM
         position it already occupied, just re-parented). */
      actionsGroup.appendChild(cartLink);

      /* ── Sign in / Account widget ──────────────────────────── */
      var wrap = document.createElement('div');
      wrap.className = 'ellcy-acct';
      actionsGroup.appendChild(wrap);

      var btn = document.createElement('a');
      btn.className = 'ellcy-acct-btn';
      btn.href = getLoginPath() + '?return_to=' + encodeURIComponent(currentPathForReturn());
      btn.setAttribute('aria-label', 'Sign in to your account');
      btn.innerHTML =
        '<span class="ellcy-acct-icon"><i class="fa-solid fa-user"></i></span>' +
        '<span class="ellcy-acct-text">' +
          '<span class="ellcy-acct-line1">Hello, sign in</span>' +
          '<span class="ellcy-acct-line2">Account &amp; Lists <i class="fa-solid fa-caret-down"></i></span>' +
        '</span>';
      wrap.appendChild(btn);

      var guestMenu = document.createElement('div');
      guestMenu.className = 'ellcy-acct-menu';
      guestMenu.innerHTML =
        '<a href="' + getLoginPath() + '?return_to=' + encodeURIComponent(currentPathForReturn()) + '">' +
          '<i class="fa-solid fa-right-to-bracket"></i> Sign In</a>' +
        '<a href="' + getRegisterPath() + '?return_to=' + encodeURIComponent(currentPathForReturn()) + '">' +
          '<i class="fa-solid fa-user-plus"></i> Create Account</a>';
      wrap.appendChild(guestMenu);
      btn.addEventListener('click', function(e){
        if (window.matchMedia && window.matchMedia('(hover: hover)').matches) return; /* desktop: let link navigate */
        e.preventDefault();
        guestMenu.classList.toggle('open');
      });
      document.addEventListener('click', function(e){
        if (!wrap.contains(e.target)) guestMenu.classList.remove('open');
      });

      checkAuth(function(auth) {
        openDrawerFn = buildDrawer(auth);

        if (!auth.loggedIn) return; /* logged-out state already rendered above */
        guestMenu.remove();

        var initial = (auth.name || 'A').trim().charAt(0).toUpperCase();
        var firstName = (auth.name || 'Account').trim().split(' ')[0];
        btn.removeAttribute('href');
        btn.classList.add('is-logged-in');
        btn.innerHTML =
          '<span class="ellcy-acct-avatar">' + initial + '</span>' +
          '<span class="ellcy-acct-text">' +
            '<span class="ellcy-acct-line1">Hello, ' + firstName + '</span>' +
            '<span class="ellcy-acct-line2">Account &amp; Lists <i class="fa-solid fa-caret-down"></i></span>' +
          '</span>';

        var menu = document.createElement('div');
        menu.className = 'ellcy-acct-menu';
        menu.innerHTML =
          '<a href="' + getAccountPath() + '"><i class="fa-solid fa-id-card"></i> My Profile</a>' +
          '<a href="' + getMyBookingsPath() + '"><i class="fa-solid fa-calendar-check"></i> My Bookings</a>' +
          '<a href="' + getCartPath() + '"><i class="fa-solid fa-cart-shopping"></i> My Orders / Cart</a>' +
          '<a href="' + getAccountPath() + '#settings"><i class="fa-solid fa-gear"></i> Account Settings</a>' +
          '<div class="ellcy-acct-menu-divider"></div>' +
          '<a href="' + ROOT_PREFIX + 'logout" class="ellcy-acct-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>';
        wrap.appendChild(menu);
        btn.addEventListener('click', function(e){
          e.preventDefault();
          menu.classList.toggle('open');
        });
        document.addEventListener('click', function(e){
          if (!wrap.contains(e.target)) menu.classList.remove('open');
        });
      });
    });
  }

  function injectVendorSignup() {
    var footer = document.querySelector('footer.site-footer');
    if (!footer || footer.querySelector('.ellcy-vendor-cta')) return;
    var divider = footer.querySelector('.footer-divider, .footer-bottom');
    var card = document.createElement('section');
    card.className = 'ellcy-vendor-cta';
    card.setAttribute('aria-label', 'Sell services on ELLCY');
    card.innerHTML =
      '<div><span class="ellcy-vendor-kicker">ELLCY for vendors</span>' +
      '<h3>Grow your event business with ELLCY</h3>' +
      '<p>Reach customers, manage enquiries and build a trusted service profile.</p></div>' +
      '<a href="' + ROOT_PREFIX + 'vendor-signup"><i class="fa-solid fa-store"></i> Vendor Sign Up</a>';
    if (divider) footer.insertBefore(card, divider); else footer.appendChild(card);
  }

  function initialiseSharedUi() {
    injectAuthWidget();
    injectVendorSignup();
  }

  /* ── Public API — used by cart.js and available to any other
     script that needs to know "who is the user" or where the
     account/login/booking/cart pages live. ────────────────── */
  window.EllcyAuth = {
    ROOT_PREFIX:         ROOT_PREFIX,
    getCartPath:         getCartPath,
    getBookingPath:      getBookingPath,
    getMyBookingsPath:   getMyBookingsPath,
    getLoginPath:        getLoginPath,
    getRegisterPath:     getRegisterPath,
    getAccountPath:      getAccountPath,
    currentPathForReturn: currentPathForReturn,
    checkAuth:           checkAuth,
    showLoginRequiredModal: showLoginRequiredModal
  };

  /* ── Run on DOM ready ─────────────────────────────────────── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseSharedUi);
  } else {
    initialiseSharedUi();
  }

})();
