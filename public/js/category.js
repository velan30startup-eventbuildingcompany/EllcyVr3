// category.js — ELLCY Patch 3.0
// CHANGE 1: Coming Soon card properly centered (vertically + horizontally)
// CHANGE 3: birthday/college/temple → coming-soon (no service cards)
(function () {
  'use strict';

  function esc(str) {
    const d = document.createElement('div');
    d.textContent = String(str);
    return d.innerHTML;
  }

  /* Resolve the application mount point from the Home link. This keeps the
     same JavaScript valid at both localhost/ellcy and the Vercel domain root. */
  function appPath(path) {
    const home = document.querySelector('a[aria-label="ELLCY Home"]');
    let base = '';
    if (home) {
      const homePath = new URL(home.href, window.location.href).pathname;
      base = homePath.replace(/\/index\.html\/?$/i, '').replace(/\/$/, '');
    }
    return base + '/' + String(path || '').replace(/^\/+/, '');
  }

  const CATEGORY_DESCRIPTIONS = {
    decoration: 'Stage, floral and lighting decor planned around your venue.',
    photography: 'Professional photo and video coverage for every key moment.',
    food: 'Breakfast, lunch and dinner menus for celebrations of every size.',
    dj: 'Curated music, sound and lighting packages for your celebration.',
    'musical-band': 'Traditional and live music performers for grand event entries.',
    bouncers: 'Trained event security staff for organised guest management.',
    'entertainment-activities': 'Interactive experiences and photo attractions for every age.',
    'snacks-stalls': 'Fresh live counters for popular snacks, drinks and desserts.',
    'enter-show-down': 'Professional entry effects that make every arrival memorable.',
    'catering-boys': 'Uniformed serving staff and graceful welcome hosts for your event.',
    dancers: 'Male, female and co-ed dance teams for energetic performances.',
    'real-flowers': 'Fresh floral styling for stages, entrances and special moments.',
    'fake-jewellery': 'Elegant jewellery sets for bridal and celebration styling.',
    'car-entry': 'Premium and luxury vehicle options for your grand entrance.',
    'bridal-groom-styling': 'Complete bridal and groom makeup and styling services.',
    'plates-decoration': 'Beautifully arranged aarti and seer plates for ceremonies.',
    'flower-rangoli': 'Hand-arranged floral rangoli designs in multiple sizes.',
    'food-breakfast': 'Fresh breakfast menus and serving options for morning events.',
    'food-lunch': 'Traditional and contemporary lunch menus for your guests.',
    'food-dinner': 'Complete dinner and buffet selections for evening celebrations.'
  };

  document.addEventListener('DOMContentLoaded', () => {
    const params    = new URLSearchParams(window.location.search);
    const eventType = params.get('type') || 'wedding';

    /* Allowlist */
    const allowed = ['wedding','food','birthday','college','temple'];
    const safeType = allowed.includes(eventType) ? eventType : 'wedding';

    const label = LABEL_MAP[safeType] || safeType;
    document.title = 'ELLCY | ' + label;

    const bc = document.getElementById('breadcrumb-label');
    if (bc) bc.textContent = label;
    const ph = document.getElementById('page-heading');
    if (ph) ph.textContent = label;

    const grid = document.getElementById('categoryGrid');
    if (!grid) return;
    grid.innerHTML = '';

    /* ════════════════════════════════════════════════════════
       COMING SOON — birthday / college / temple
       CHANGE 1: wrapper fills viewport for true centering
    ════════════════════════════════════════════════════════ */
    const comingSoonTypes = ['birthday', 'college', 'temple'];
    if (comingSoonTypes.includes(safeType)) {
      const categoryLabels = {
        birthday: 'Birthday Events',
        college:  'College Events',
        temple:   'Temple Events',
      };
      const categoryIcons = {
        birthday: 'fa-cake-candles',
        college:  'fa-graduation-cap',
        temple:   'fa-place-of-worship',
      };
      const eventLabel = esc(categoryLabels[safeType] || label);
      const iconClass  = categoryIcons[safeType] || 'fa-star';

      /* Hide page heading — the card has its own title */
      if (ph) ph.style.display = 'none';

      /* Stretch grid to fill available vertical space */
      grid.className = 'coming-soon-fullpage';
      grid.setAttribute('role', 'main');
      grid.setAttribute('aria-label', eventLabel + ' — Coming Soon');

      grid.innerHTML = `
        <div class="cs-center-wrap">
          <div class="coming-soon-card" role="article">
            <div class="cs-icon-wrap" aria-hidden="true">
              <i class="fa-solid ${iconClass} cs-icon"></i>
            </div>
            <h1 class="cs-title">Coming Soon</h1>
            <p class="cs-category-name">${eventLabel.toUpperCase()}</p>
            <p class="cs-body">
              We are currently working on developing and expanding our business into
              different categories. <strong>${eventLabel}</strong> services are under
              progress and will be available very soon.
            </p>
            <div class="cs-divider" role="separator"></div>
            <p class="cs-enquiry-line">If you have any enquiries, kindly contact</p>
            <a class="cs-email" href="mailto:enquiry@elly.in">enquiry@elly.in</a>
            <div class="cs-actions">
              <a href="${appPath('')}" class="cs-btn cs-btn-home">
                <i class="fa-solid fa-house" aria-hidden="true"></i> Back to Home
              </a>
              <a href="${appPath('booking')}" class="cs-btn cs-btn-enquiry">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i> Book Now
              </a>
            </div>
          </div>
        </div>`;

      setYear(); return;
    }

    /* ════════════════════════════════════════════════════════
       WEDDING — render service-type cards
    ════════════════════════════════════════════════════════ */
    /* Slugs that skip the intermediate "choose a variant" list and go
       straight to a single merged description page. */
    const DIRECT_LINKS = {
      'food':           appPath('category?type=food'),
      'food-breakfast': appPath('services/food/breakfast/'),
      'food-lunch':     appPath('services/food/lunch/'),
      'food-dinner':    appPath('services/food/dinner/')
    };

    const list = CATEGORY_MAPPINGS[safeType] || [];
    list.forEach(item => {
      const a = document.createElement('a');
      a.className = 'category-card-link';
      a.href      = DIRECT_LINKS[item.slug] || appPath('services?type=' + encodeURIComponent(item.slug));
      a.title     = item.name;
      a.setAttribute('aria-label', item.name);
      a.innerHTML = `
        <div class="category-card">
          <div class="category-image">
            <img src="${esc(item.img)}" alt="${esc(item.name)}" loading="lazy"/>
          </div>
          <div class="category-name">${esc(item.name)}</div>
          <p class="category-desc">${esc(item.desc || CATEGORY_DESCRIPTIONS[item.slug] || 'Explore packages and options for this event service.')}</p>
        </div>`;
      grid.appendChild(a);
    });

    setYear();
  });

  function setYear() {
    const yr = document.getElementById('year');
    if (yr) yr.textContent = new Date().getFullYear();
  }
})();
