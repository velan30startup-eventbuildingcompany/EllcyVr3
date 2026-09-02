/**
 * service-desc.js — ELLCY Service Description Page Logic v3
 *
 * Works with the service-description-style templates (sd- prefixed markup).
 * window.SD_CONFIG must be set before this script loads:
 *
 * window.SD_CONFIG = {
 *   serviceKey  : 'dj',
 *   serviceName : 'DJ',
 *   rating      : '4.4',
 *   img         : '../uploads/services/dj.webp',
 *   images      : ['../uploads/services/dj.webp', ...],  // optional extras
 *   availability: 'Available for Weddings, Birthdays & College Events',
 *   subtags     : 'Premium Sound | LED Lights | Non-Stop Music',
 *   priceMeta   : 'Groovy | Electric | Unforgettable',
 *   overviewHtml: '<p>...</p>',
 *   reviews     : [{ name, stars, text }, ...],
 *   showPkgPills: true,   // false = hide package pill row (selection via cards only)
 *   showSlotPills: true,  // false = hide time slot section entirely
 *   pillLabel   : 'Select Package',   // override label above package pills
 *   packages    : [{ key, label, price, priceB, desc }, ...],   // used when NOT using groups
 *
 *   // ── Optional two-tier selector, e.g. "Reception" vs "Marriage" ──
 *   // When `groups` is set, top-level `packages` above is ignored —
 *   // each group carries its own package list and the active group's
 *   // packages drive the pills/cards exactly like the flat list does.
 *   // Fully backward compatible: pages that don't set `groups` behave
 *   // exactly as before this feature was added.
 *   showGroupPills: true,             // false = hide the group/occasion pills
 *   groupLabel  : 'Select Occasion',  // label above group pills
 *   groups      : [
 *     { key: 'reception', label: 'Reception', packages: [{ key, label, price, priceB, desc }, ...] },
 *     { key: 'marriage',  label: 'Marriage',  packages: [{ key, label, price, priceB, desc }, ...] }
 *   ],
 *
 *   phone       : '+919876543210',
 *
 *   // ── Optional quantity/count selector, e.g. "Number of Human Dolls" ──
 *   // Opt-in: only appears when showQty is true. Multiplies the
 *   // displayed price and the Add to Cart / Buy Now payload by qty.
 *   // Fully backward compatible: pages that don't set showQty behave
 *   // exactly as before this feature was added.
 *   showQty  : true,
 *   qtyLabel : 'Number of Human Dolls',   // defaults to 'Quantity'
 *   minQty   : 1,                         // defaults to 1
 *   defaultQty: 1,                        // defaults to minQty
 *   maxQty   : 20,                        // defaults to 20
 * };
 */
(function () {
  'use strict';

  var C = window.SD_CONFIG;
  if (!C) return;

  var qs = new URLSearchParams(window.location.search);

  /* ── Quantity / count (optional) ──────────────────────────── */
  var HAS_QTY = C.showQty === true;
  var MIN_QTY = Math.max(1, parseInt(C.minQty || '1', 10));
  var MAX_QTY = Math.max(MIN_QTY, parseInt(C.maxQty || '20', 10));
  var qty     = Math.max(MIN_QTY, Math.min(MAX_QTY, parseInt(C.defaultQty || MIN_QTY, 10)));

  /* ── Groups (optional two-tier selector) ──────────────────── */
  var GROUPS      = C.groups || [];
  var HAS_GROUPS  = GROUPS.length > 0;
  var SHOW_GROUPS = HAS_GROUPS && C.showGroupPills !== false;
  var activeGroupKey = qs.get('group') || qs.get('type') || (HAS_GROUPS ? GROUPS[0].key : '');

  function getGroup() {
    return GROUPS.find(function (g) { return g.key === activeGroupKey; }) || GROUPS[0];
  }
  function currentPackages() {
    if (HAS_GROUPS) { var g = getGroup(); return g ? (g.packages || []) : []; }
    return C.packages || [];
  }

  var PACKAGES   = currentPackages();
  var HAS_PKGS   = C.showPkgPills !== false && PACKAGES.length > 0;
  var HAS_SLOTS  = C.showSlotPills !== false;
  var activeKey  = qs.get('pkg') || (PACKAGES[0] ? PACKAGES[0].key : '');
  var activeSlot = 'Morning';

  function fmt(n) { return Number(n).toLocaleString('en-IN'); }
  function getPkg() { return PACKAGES.find(function(p){ return p.key === activeKey; }) || PACKAGES[0]; }
  function getPrice(p) {
    if (!p) return 0;
    return (HAS_SLOTS && activeSlot === 'Both' && p.priceB) ? p.priceB : p.price;
  }
  function $(id) { return document.getElementById(id); }
  function esc(s) { var d=document.createElement('div'); d.textContent=String(s||''); return d.innerHTML; }

  /* ── Update all price displays ────────────────────────────── */
  function updateAll() {
    var p     = getPkg();
    var total = getPrice(p) * (HAS_QTY ? qty : 1);

    // Mobile price
    var em = $('sdPrice'); if (em) em.textContent = fmt(total);
    // Desktop price
    var ed = $('sdPriceD'); if (ed) ed.textContent = fmt(total);
    // Back label (mobile topbar)
    var bl = $('sdBackLabel'); if (bl) bl.textContent = C.serviceName;

    // Sync group pills active state (separate class from package pills
    // on purpose — see renderGroupPills — so this loop can't stomp on it)
    if (SHOW_GROUPS) {
      document.querySelectorAll('.sd-grp-pill').forEach(function(el) {
        el.classList.toggle('active', el.dataset.key === activeGroupKey);
      });
    }

    // Sync package pills active state
    document.querySelectorAll('.sd-pkg-pill').forEach(function(el) {
      el.classList.toggle('active', el.dataset.key === activeKey);
    });

    // Sync package cards active state + prices
    document.querySelectorAll('.sd-pkg-card').forEach(function(card) {
      var isActive = card.dataset.key === activeKey;
      card.classList.toggle('active', isActive);
      var priceEl = card.querySelector('.sd-card-price-value, .sd-card-price');
      var pkg = PACKAGES.find(function(pkg){ return pkg.key === card.dataset.key; });
      if (priceEl && pkg) priceEl.textContent = '₹' + fmt(getPrice(pkg));
    });
  }

  /* ── Set images ───────────────────────────────────────────── */
  var imgs = C.images && C.images.length >= 3 ? C.images : [C.img, C.img, C.img];
  var setImg = function(id, src) { var el=$(id); if(el) el.src=src; };
  setImg('sdImgMain', imgs[0]);
  setImg('sdImgT1',   imgs[1]);
  setImg('sdImgT2',   imgs[2]);
  setImg('sdImgMob',  imgs[0]);

  // Rating
  document.querySelectorAll('.sd-rating-val').forEach(function(el){ el.textContent = '★ ' + (C.rating || '4.5'); });

  // Title
  document.querySelectorAll('.sd-title').forEach(function(el){ el.textContent = C.serviceName || ''; });

  // Breadcrumb — "Home / Category" where Category links back to its listing page
  var bc = document.getElementById('sdBreadcrumbLabel');
  if (bc) {
    var name = C.serviceName || 'Service';
    if (typeof C.categorySlug !== 'undefined') {
      var a = document.createElement('a');
      /* Determine the correct relative path to services.html.
         C.servicesPageUrl can be set explicitly; otherwise we
         auto-detect depth from the current URL path. */
      var servicesBase = C.servicesPageUrl || (function() {
        /* Split pathname, drop the filename (last segment = index.html or similar),
           then count folder levels below "services" to determine the correct prefix. */
        var parts = window.location.pathname.split('/').filter(Boolean);
        /* Remove the last segment if it looks like a file (has a dot) */
        if (parts.length && parts[parts.length - 1].indexOf('.') !== -1) {
          parts = parts.slice(0, -1);
        }
        /* Find "services" folder in the remaining directory parts */
        var svcIdx = parts.indexOf('services');
        /* depth = number of folder levels between "services" and current dir */
        var depth = svcIdx >= 0 ? (parts.length - svcIdx - 1) : 1;
        /* We need to go up (depth + 1) levels: past our own folders plus past "services" */
        var prefix = '';
        for (var i = 0; i < depth + 1; i++) prefix += '../';
        return prefix + 'pages/';
      })();
      a.href = C.categorySlug
        ? servicesBase + 'services.html?type=' + encodeURIComponent(C.categorySlug)
        : servicesBase + 'services.html';
      a.textContent = name;
      bc.innerHTML = '';
      bc.appendChild(a);
    } else {
      bc.textContent = name;
    }
  }

  // Availability
  document.querySelectorAll('.sd-avail').forEach(function(el){ el.textContent = C.availability || ''; });

  // Sub-tags
  document.querySelectorAll('.sd-subtags').forEach(function(el){ el.textContent = C.subtags || ''; });

  // Price meta
  document.querySelectorAll('.sd-price-meta, .sd-price-meta-d').forEach(function(el){ el.textContent = C.priceMeta || ''; });

  // Page title
  document.title = 'ELLCY | ' + (C.serviceName || 'Service');

  /* ── Group pills (e.g. Reception / Marriage occasion toggle) ─
     Opt-in: only runs when C.groups is set. Switching group swaps
     PACKAGES and re-renders the package pills + cards underneath it. */
  if (!SHOW_GROUPS) {
    document.querySelectorAll('.sd-group-section').forEach(function(el){ el.style.display='none'; });
  } else {
    var groupLabel = C.groupLabel || 'Select Occasion';
    document.querySelectorAll('.sd-group-label').forEach(function(el){ el.textContent = groupLabel; });
    ['sdGroupPillsM','sdGroupPillsD'].forEach(function(cid) {
      var container = $(cid);
      if (!container) return;
      GROUPS.forEach(function(g) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'sd-grp-pill' + (g.key === activeGroupKey ? ' active' : '');
        btn.dataset.key = g.key;
        btn.textContent = g.label;
        btn.setAttribute('aria-label', g.label);
        btn.addEventListener('click', function() {
          if (g.key === activeGroupKey) return;
          activeGroupKey = g.key;
          PACKAGES = currentPackages();
          activeKey = PACKAGES[0] ? PACKAGES[0].key : '';
          renderPkgPills();
          renderCards();
          updateAll();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        container.appendChild(btn);
      });
    });
  }

  /* ── Package pills (desktop + mobile) ─────────────────────── */
  function renderPkgPills() {
    if (!HAS_PKGS) {
      document.querySelectorAll('.sd-pkg-section').forEach(function(el){ el.style.display='none'; });
      return;
    }
    var pillLabel = C.pillLabel || 'Select Package';
    document.querySelectorAll('.sd-pkg-label').forEach(function(el){ el.textContent = pillLabel; });
    ['sdPkgPillsM','sdPkgPillsD'].forEach(function(cid) {
      var container = $(cid);
      if (!container) return;
      container.innerHTML = '';
      PACKAGES.forEach(function(p) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'sd-pkg-pill' + (p.key === activeKey ? ' active' : '');
        btn.dataset.key = p.key;
        btn.textContent = p.label;
        btn.setAttribute('aria-label', p.label);
        btn.addEventListener('click', function() { activeKey = p.key; updateAll(); });
        container.appendChild(btn);
      });
    });
  }
  renderPkgPills();

  /* ── Time slot pills ──────────────────────────────────────── */
  if (!HAS_SLOTS) {
    document.querySelectorAll('.sd-slot-section').forEach(function(el){ el.style.display='none'; });
  } else {
    document.querySelectorAll('.sd-slot-pill').forEach(function(btn) {
      btn.addEventListener('click', function() {
        activeSlot = btn.dataset.slot;
        document.querySelectorAll('.sd-slot-pill').forEach(function(b) {
          b.classList.toggle('active', b.dataset.slot === activeSlot);
        });
        updateAll();
      });
    });
  }

  /* ── Package cards grid ───────────────────────────────────── */
  function renderCards() {
    var cardsGrid = $('sdCardsGrid');
    if (!cardsGrid) return;
    cardsGrid.innerHTML = '';
    var sec = cardsGrid.closest('.sd-cards-section');
    if (C.hideCards === true) {
      if (sec) sec.style.display = 'none';
      return;
    }
    if (!PACKAGES.length) {
      if (sec) sec.style.display = 'none';
      return;
    }
    if (sec) sec.style.display = '';

    PACKAGES.forEach(function(p) {
      var card = document.createElement('div');
      card.className = 'sd-pkg-card' + (p.key === activeKey ? ' active' : '');
      card.dataset.key = p.key;
      card.setAttribute('role', 'button');
      card.setAttribute('tabindex', '0');
      card.setAttribute('aria-label', p.label);
      if (C.catalogCards) {
        var packageIndex = PACKAGES.indexOf(p);
        var reviewCount = p.reviews || (32 + (packageIndex * 17));
        card.innerHTML =
          '<div class="sd-card-img">' +
            '<img src="' + esc(p.img || C.img) + '" alt="' + esc(p.label) + '" loading="lazy"/>' +
            ((packageIndex === 1 || packageIndex === 2) ? '<span class="sd-catalog-popular"><i class="fa-solid fa-fire"></i> Popular</span>' : '') +
          '</div>' +
          '<div class="sd-card-body">' +
            '<div class="sd-catalog-title-row">' +
              '<div class="sd-card-name">' + esc(p.label) + '</div>' +
              '<div class="sd-catalog-rating"><i class="fa-solid fa-star"></i> ' + esc(C.rating || '4.5') + ' <span>(' + reviewCount + ' reviews)</span></div>' +
            '</div>' +
            '<div class="sd-card-desc">' + esc(p.desc || '') + '</div>' +
            '<div class="sd-catalog-divider"></div>' +
            '<div class="sd-catalog-price-row"><span>Starting Package</span><strong><span class="sd-card-price-value">₹' + fmt(getPrice(p)) + '</span> <small>onwards</small></strong></div>' +
            '<div class="sd-catalog-badge"><i class="fa-solid fa-medal"></i> ' + esc(p.badge || 'Professional event service') + '</div>' +
          '</div>';
      } else {
        card.innerHTML =
          '<div class="sd-card-img">' +
            '<img src="' + esc(p.img || C.img) + '" alt="' + esc(p.label) + '" loading="lazy"/>' +
            '<span class="sd-card-rating">★ ' + esc(C.rating || '4.5') + '</span>' +
            '<span class="sd-card-price" id="sdcp-' + esc(p.key) + '">₹' + fmt(getPrice(p)) + '</span>' +
          '</div>' +
          '<div class="sd-card-body">' +
            '<div class="sd-card-name">' + esc(p.label) + '</div>' +
            '<div class="sd-card-desc">' + esc(p.desc || '') + '</div>' +
            '<button class="sd-card-add-btn" type="button">' +
              '<i class="fa-solid fa-cart-shopping"></i> Add to Cart' +
            '</button>' +
          '</div>';
      }

      // Select this package
      card.addEventListener('click', function(e) {
        if (e.target.classList.contains('sd-card-add-btn') || e.target.closest('.sd-card-add-btn')) return;
        activeKey = p.key;
        updateAll();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
      card.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.click(); }
      });

      // Add to cart button on card
      var addButton = card.querySelector('.sd-card-add-btn');
      if (addButton) {
        addButton.addEventListener('click', function(e) {
          e.stopPropagation();
          activeKey = p.key;
          updateAll();
          addToCart();
        });
      }

      cardsGrid.appendChild(card);
    });
  }
  renderCards();

  /* ── Quantity/count selector (optional) ──────────────────────
     Injected right after each price display so it always sits
     between "price" and the Add to Cart / Buy Now buttons,
     regardless of which sd- template variant the page uses. ── */
  function injectQtyUI() {
    if (!HAS_QTY) return;
    var label = C.qtyLabel || 'Quantity';

    [{ priceId: 'sdPrice', tag: 'm' }, { priceId: 'sdPriceD', tag: 'd' }].forEach(function (cfg) {
      var priceEl = $(cfg.priceId);
      if (!priceEl) return;
      // Find a sensible anchor: the closest price-line wrapper, else the price element itself.
      var anchor = priceEl.closest('.sd-price-block, .sd-dsk-price-block') || priceEl;
      if (anchor.parentNode.querySelector('.sd-qty-card[data-tag="' + cfg.tag + '"]')) return;

      var card = document.createElement('div');
      card.className = 'sd-qty-card';
      card.setAttribute('data-tag', cfg.tag);
      card.innerHTML =
        '<div class="sd-qty-header">' +
          '<label class="sd-qty-label">' + esc(label) + '</label>' +
          '<div class="sd-qty-ctrl">' +
            '<button type="button" class="sd-qty-minus" aria-label="Decrease">−</button>' +
            '<input type="number" class="sd-qty-inp" value="' + qty + '" min="' + MIN_QTY + '" max="' + MAX_QTY + '" aria-label="' + esc(label) + '"/>' +
            '<button type="button" class="sd-qty-plus" aria-label="Increase">+</button>' +
          '</div>' +
        '</div>';
      anchor.insertAdjacentElement('afterend', card);

      var inp   = card.querySelector('.sd-qty-inp');
      var minus = card.querySelector('.sd-qty-minus');
      var plus  = card.querySelector('.sd-qty-plus');

      function setQty(v) {
        qty = Math.max(MIN_QTY, Math.min(MAX_QTY, parseInt(v, 10) || MIN_QTY));
        document.querySelectorAll('.sd-qty-inp').forEach(function (el) { el.value = qty; });
        updateAll();
      }
      minus.addEventListener('click', function () { setQty(qty - 1); });
      plus.addEventListener('click',  function () { setQty(qty + 1); });
      inp.addEventListener('input',  function () { setQty(this.value); });
      inp.addEventListener('change', function () { setQty(this.value); });
    });
  }
  injectQtyUI();

  /* ── Overview tab content ──────────────────────────────────── */
  var overviewEl = $('tabOverview');
  if (overviewEl && C.overviewHtml) overviewEl.innerHTML = C.overviewHtml;

  /* ── Reviews ───────────────────────────────────────────────── */
  var reviewsEl = $('tabReviews');
  if (reviewsEl && C.reviews && C.reviews.length) {
    var html = '<div class="sd-reviews">';
    C.reviews.forEach(function(r) {
      var stars = '';
      var full = Math.floor(r.stars || 5);
      for (var i=0; i<5; i++) stars += (i < full ? '★' : '☆');
      html += '<div class="sd-review-card">' +
        '<div class="sd-review-hdr">' +
          '<span class="sd-reviewer">' + esc(r.name) + '</span>' +
          '<span class="sd-rstars">' + stars + '</span>' +
        '</div>' +
        '<p>' + esc(r.text) + '</p>' +
      '</div>';
    });
    html += '</div>';
    reviewsEl.innerHTML = html;
  }

  /* ── Add to Cart function ──────────────────────────────────── */
  function addToCart() {
    if (typeof EllcyCart === 'undefined') return;
    var p     = getPkg();
    var price = getPrice(p) * (HAS_QTY ? qty : 1);
    var g     = HAS_GROUPS ? getGroup() : null;
    var uid   = C.serviceKey + '-' + (g ? g.key + '-' : '') + (p ? p.key : 'default') + '-' + activeSlot + (HAS_QTY ? '-' + qty : '');
    EllcyCart.add({
      uid:     uid,
      id:      uid,
      title:   C.serviceName + (g ? ' (' + g.label + ')' : '') + (p && p.label ? ' – ' + p.label : '') + (HAS_QTY ? ' × ' + qty : ''),
      price:   price,
      image:   (p && p.img) || C.img,
      slug:    C.serviceKey,
      package: (g ? g.label + ' – ' : '') + (p ? p.label : '') + (HAS_QTY ? ' (' + qty + ')' : ''),
      package_slug: p && p.slug ? p.slug : '',
      reference_upload_token: window.ELLCY_JEWELLERY_REFERENCE_TOKEN || '',
      slot:    HAS_SLOTS ? activeSlot : '',
      page:    location.href,
    });
  }

  /* ── Buy Now — same item payload as Add to Cart, but skips
     the persistent cart entirely and jumps straight to a
     single-item checkout on booking.html (Amazon-style). ── */
  function buyNow() {
    if (typeof EllcyCart === 'undefined') return;
    var p     = getPkg();
    var price = getPrice(p) * (HAS_QTY ? qty : 1);
    var g     = HAS_GROUPS ? getGroup() : null;
    var uid   = C.serviceKey + '-' + (g ? g.key + '-' : '') + (p ? p.key : 'default') + '-' + activeSlot + (HAS_QTY ? '-' + qty : '');
    EllcyCart.buyNow({
      uid:     uid,
      id:      uid,
      title:   C.serviceName + (g ? ' (' + g.label + ')' : '') + (p && p.label ? ' – ' + p.label : '') + (HAS_QTY ? ' × ' + qty : ''),
      price:   price,
      image:   (p && p.img) || C.img,
      slug:    C.serviceKey,
      package: (g ? g.label + ' – ' : '') + (p ? p.label : '') + (HAS_QTY ? ' (' + qty + ')' : ''),
      package_slug: p && p.slug ? p.slug : '',
      reference_upload_token: window.ELLCY_JEWELLERY_REFERENCE_TOKEN || '',
      slot:    HAS_SLOTS ? activeSlot : '',
      page:    location.href,
    });
  }

  /* ── Wire Add to Cart buttons (hero CTAs) ──────────────────── */
  ['btnCartM', 'btnCartD'].forEach(function(id) {
    var btn = $(id);
    if (!btn) return;
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      addToCart();
    });
    /* Inject a matching "Buy Now" button right next to it */
    if (btn.parentNode && !btn.parentNode.querySelector('.sd-btn-buynow')) {
      var buyBtn = document.createElement('button');
      buyBtn.type = 'button';
      buyBtn.className = 'sd-btn-buynow';
      buyBtn.innerHTML = '<i class="fa-solid fa-bolt"></i> Book Now';
      buyBtn.addEventListener('click', function(e) {
        e.preventDefault();
        buyNow();
      });
      btn.insertAdjacentElement('afterend', buyBtn);
    }
  });

  /* ── Request for Call ────────────────────────────────────────── */
  /* Update text and route all call buttons to the request-for-call page */
  document.querySelectorAll('.sd-btn-call').forEach(function(el) {
    var callUrl = publicRoot() + 'request-for-call?service=' + encodeURIComponent(C.slug || C.serviceKey || '');
    if (window.ELLCY_JEWELLERY_REFERENCE_TOKEN) {
      callUrl += '&reference_token=' + encodeURIComponent(window.ELLCY_JEWELLERY_REFERENCE_TOKEN);
    }
    el.href = callUrl;
    /* Update visible label */
    el.innerHTML = el.innerHTML.replace('Call Now', 'Request for Call');
  });

  /* ── Tabs ───────────────────────────────────────────────────── */
  document.querySelectorAll('.sd-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
      document.querySelectorAll('.sd-tab').forEach(function(t) {
        t.classList.remove('active'); t.setAttribute('aria-selected','false');
      });
      document.querySelectorAll('.sd-tab-body').forEach(function(b) { b.classList.add('hidden'); });
      tab.classList.add('active'); tab.setAttribute('aria-selected','true');
      var key = tab.dataset.tab;
      var target = $('tab' + key.charAt(0).toUpperCase() + key.slice(1));
      if (target) target.classList.remove('hidden');
    });
  });

  /* ── Init ──────────────────────────────────────────────────── */
  updateAll();

  /* ── PHP admin synchronisation ────────────────────────────────
     Detail templates keep useful static fallbacks, while prices and
     uploaded media are refreshed from the PHP/MySQL admin whenever an
     adminSlug is supplied on the service or a package. */
  function publicRoot() {
    var path = window.location.pathname;
    var marker = path.indexOf('/services/');
    return marker >= 0 ? path.slice(0, marker + 1) : '/';
  }

  function normaliseService(raw) {
    if (!raw) return null;
    var media = Array.isArray(raw.images) ? raw.images : [];
    var primary = media.find(function (item) { return !!item.is_primary; }) ||
      media.find(function (item) { return item.media_type === 'image'; });
    var baseImage = raw.image;
    if (/\/stage\.png(?:\?.*)?$/i.test(String(baseImage || ''))) baseImage = '';
    raw._displayImage = primary && primary.media_type === 'image' ? primary.path : baseImage;
    return raw;
  }

  function loadAdminService(slug) {
    if (!slug) return Promise.resolve(null);
    return fetch(publicRoot() + 'api/services/' + encodeURIComponent(slug), {
      headers: { Accept: 'application/json' }
    })
      .then(function (response) { return response.ok ? response.json() : null; })
      .then(function (payload) { return normaliseService(payload && payload.service); })
      .catch(function () { return null; });
  }

  function refreshHeader() {
    var refreshed = C.images && C.images.length >= 3 ? C.images : [C.img, C.img, C.img];
    setImg('sdImgMain', refreshed[0]);
    setImg('sdImgT1', refreshed[1]);
    setImg('sdImgT2', refreshed[2]);
    setImg('sdImgMob', refreshed[0]);
    document.querySelectorAll('.sd-title').forEach(function (el) { el.textContent = C.serviceName || ''; });
    document.querySelectorAll('.sd-rating-val').forEach(function (el) { el.textContent = '★ ' + (C.rating || '4.5'); });
    document.title = 'ELLCY | ' + (C.serviceName || 'Service');
  }

  var adminTargets = [];
  if (C.adminSlug) adminTargets.push({ kind: 'service', slug: C.adminSlug });
  function collectPackageTargets(list) {
    (list || []).forEach(function (pkg) {
      if (pkg.adminSlug) adminTargets.push({ kind: 'package', slug: pkg.adminSlug, pkg: pkg });
    });
  }
  collectPackageTargets(C.packages);
  (C.groups || []).forEach(function (group) { collectPackageTargets(group.packages); });

  if (adminTargets.length) {
    Promise.all(adminTargets.map(function (target) {
      return loadAdminService(target.slug).then(function (service) {
        return { target: target, service: service };
      });
    })).then(function (results) {
      results.forEach(function (result) {
        var target = result.target;
        var service = result.service;
        if (!service) return;

        if (target.kind === 'service') {
          if (service.title && !C.keepTemplateTitle) C.serviceName = service.title;
          if (service.rating) C.rating = String(service.rating);
          if (service._displayImage) C.img = service._displayImage;
          if (C.useAdminDescription && (service.description || service.short_description)) {
            C.overviewHtml = '<p>' + esc(service.description || service.short_description) + '</p>';
            if (overviewEl) overviewEl.innerHTML = C.overviewHtml;
          }
          var managedPackages = Array.isArray(service.packages) ? service.packages : [];
          if (managedPackages.length && !C.keepTemplatePackages) {
            C.packages = managedPackages.map(function (pkg, index) {
              var fallback = (PACKAGES || [])[index] || {};
              return {
                key: pkg.pkg_key || pkg.slug || fallback.key || ('p' + (index + 1)),
                label: pkg.label || fallback.label || service.title,
                price: Number(pkg.price || service.price || fallback.price || 0),
                priceB: Number(fallback.priceB || 0),
                desc: pkg.description || fallback.desc || service.short_description || '',
                slug: pkg.slug || fallback.slug || '',
                img: pkg.image || service._displayImage || fallback.img || C.img
              };
            });
          } else if (C.packages && C.packages.length) {
            C.packages[0].price = Number(service.price || C.packages[0].price || 0);
            C.packages[0].img = service._displayImage || C.packages[0].img || C.img;
          }
        } else if (target.pkg) {
          target.pkg.price = Number(service.price || target.pkg.price || 0);
          target.pkg.img = service._displayImage || target.pkg.img || C.img;
          if (service.short_description || service.description) {
            target.pkg.desc = service.short_description || service.description;
          }
        }
      });

      PACKAGES = currentPackages();
      HAS_PKGS = C.showPkgPills !== false && PACKAGES.length > 0;
      if (!PACKAGES.some(function (pkg) { return pkg.key === activeKey; })) {
        activeKey = PACKAGES[0] ? PACKAGES[0].key : '';
      }
      refreshHeader();
      renderPkgPills();
      renderCards();
      updateAll();
    });
  }
  var yr = $('year'); if (yr) yr.textContent = new Date().getFullYear();

})();
