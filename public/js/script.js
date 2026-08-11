// ============================================================
// script.js — ELLCY Home Page
// CHANGES v6:
//  1. Search now covers ALL services listed on the site:
//     - All SERVICES_DATA packages
//     - Photography package (with filter variants)
//     - Chenda Melam member packages
//     - Decoration subtypes (Light + Stage)
//     - Musical Band subtypes
//  2. Search results route to the CORRECT description page
//     for each service (not generic service_details.html)
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  /* ──────────────────────────────────────────────────────────
     SEARCH PANEL – Smart NLP-style search
     Understands queries like:
       "DJ under 20000"
       "wedding photography below 15000"
       "music band, chenda melam"
       "stage decoration"
  ────────────────────────────────────────────────────────── */
  const searchPanel      = document.getElementById('searchPanel');
  const searchInput      = document.getElementById('searchInput');
  const resultsList      = document.getElementById('resultsList');
  const noResults        = document.getElementById('noResults');
  const resultsMeta      = document.getElementById('resultsMeta');
  const searchResultsWrap= document.getElementById('searchResultsWrap');
  const searchSuggestions= document.getElementById('searchSuggestions');
  const clearSearchBtn   = document.getElementById('clearSearch');

  /* ── BUILD COMPREHENSIVE SEARCH INDEX ──────────────────────
     Includes every service/package/subtype visible on the site,
     each with a `route` pointing to the correct page.
  ────────────────────────────────────────────────────────── */

  // Slug → correct description page mapping
    const DESC_PAGES = {
    'dj': 'services/dj/index.html',
    'decoration': 'services/decoration/index.html',
    'stage-decoration': 'services/stage-decoration/index.html',
    'light-decoration': 'services/light-decoration/index.html',
    'photography': 'services/photography/index.html',
    'food': 'pages/food-description.html',
    'chenda-melam': 'pages/chenda-melam-description.html',
    'musical-band': 'pages/services.html?type=musical-band',
    'band-set':     'pages/band-set-description.html',
    'melam-set':    'pages/melam-set-description.html',
    'performers': 'pages/performers-description.html',
    'bouncers': 'services/bouncers/index.html',
    'entertainment': 'pages/entertainment-description.html',
    'fun-things': 'pages/entertainment-description.html',
    'snacks-stalls': 'pages/snacks-description.html',
    'enter-show-down': 'pages/enter-show-down-description.html',
    'catering-boys': 'services/catering-boys/index.html',
    'dancers': 'pages/services.html?type=dancers',
    'plates-decoration': 'pages/services.html?type=plates-decoration',
    'aarti-plates': 'pages/services.html?type=aarti-plates',
    'seer-plates': 'pages/services.html?type=seer-plates',
    'dancers-male':   'pages/services.html?type=dancers-male',
    'dancers-female': 'pages/services.html?type=dancers-female',
    'dancers-coed':   'pages/services.html?type=dancers-coed',
    'real-flowers': 'pages/real-flowers-description.html',
    'fake-jewellery': 'pages/fake-jewellery-description.html',
    'bridal-groom-styling': 'services/bridal-groom-styling/index.html',
    'mehandi': 'pages/mehendi-description.html',
    'cake-decoration': 'pages/booking.html',
    'bike-stunt': 'pages/booking.html',
    'fiction-character': 'pages/fictional-description.html',
    'lighting-setup': 'services/light-decoration/index.html',
  };

  // Build the full search index
  function buildSearchIndex() {
    // Normalize image path: strip leading '../' so paths work from root (index.html)
    function normImg(p) { return p ? p.replace(/^\.\.\//, '') : 'uploads/services/stage.png'; }

    const index = [];

    // 1. All standard SERVICES_DATA packages
    Object.entries(SERVICES_DATA).forEach(([slug, items]) => {
      items.forEach(svc => {
        // Determine the correct route for this item
        let route;
        if (DESC_PAGES[slug]) {
          // Services with desc pages: route directly there
          // For items with individual IDs that have desc pages, pass pkg param if relevant
          route = DESC_PAGES[slug];
        } else {
          route = 'pages/service_details.html?id=' + svc.id;
        }
        index.push({
          id:          svc.id,
          title:       svc.title,
          description: svc.description,
          base_price:  svc.base_price || 0,
          image:       normImg(svc.image),
          slug:        slug,
          category:    LABEL_MAP[slug] || slug,
          route:       route,
          hasPriceBadge: svc.base_price > 0,
        });
      });
    });

    // 2. Photography package (with all filter variants)
    PHOTOGRAPHY_FILTERS.forEach(f => {
      const price = PHOTOGRAPHY_BASE_PRICE + f.addPrice;
      index.push({
        id:          'photo-' + f.key,
        title:       'Photography – ' + f.label,
        description: PHOTOGRAPHY_PACKAGE.description,
        base_price:  price,
        image:       normImg(PHOTOGRAPHY_PACKAGE.image),
        slug:        'photography',
        category:    'Photography',
        route:       'pages/photo-description.html',
        hasPriceBadge: true,
      });
    });

    // 3. Chenda Melam member packages
    const CHENDA_PACKAGES = [
      { key:'6m',  label:'6 Members',  price:11994, desc:'Small performance. Best for pooja / home events.' },
      { key:'8m',  label:'8 Members',  price:15992, desc:'Medium sound impact. Suitable for small functions.' },
      { key:'10m', label:'10 Members', price:19990, desc:'Balanced performance. Ideal for weddings.' },
      { key:'12m', label:'12 Members', price:23988, desc:'Medium-large performance. Ideal for weddings & special occasions.' },
      { key:'15m', label:'15 Members', price:29985, desc:'High energy performance. Temple & grand events.' },
      { key:'18m', label:'18 Members', price:35982, desc:'Powerful traditional setup. Large celebrations.' },
      { key:'20m', label:'20 Members', price:39980, desc:'Grand Chenda Melam. Festival-level performance.' },
    ];
    CHENDA_PACKAGES.forEach(p => {
      index.push({
        id:          'chenda-' + p.key,
        title:       'Chenda Melam – ' + p.label,
        description: p.desc,
        base_price:  p.price,
        image:       normImg('../uploads/services/musical_band.png'),
        slug:        'chenda-melam',
        category:    'Chenda Melam',
        route:       'pages/chenda-melam-description.html?pkg=' + encodeURIComponent(p.key),
        hasPriceBadge: true,
      });
    });

    // 4. Dancer team member packages (15 total: 3 teams x 5 member counts)
    var DANCER_SEARCH_DATA = [
      { team:'Male Team',              type:'dancers-male',   slug:'dancers-male',
        packages:[
          { key:'2m', label:'2 Members', price:5598,  dir:'2-members', desc:'Intimate duo — perfect for small gatherings & private celebrations.' },
          { key:'4m', label:'4 Members', price:11196, dir:'4-members', desc:'Compact troupe — ideal for intimate functions & small stage shows.' },
          { key:'5m', label:'5 Members', price:13995, dir:'5-members', desc:'Balanced energy — great for medium-sized celebrations.' },
          { key:'7m', label:'7 Members', price:19593, dir:'7-members', desc:'High-impact group performance for weddings & large parties.' },
          { key:'9m', label:'9 Members', price:25191, dir:'9-members', desc:'Full ensemble — grand stage spectacle for major events.' }
        ]
      },
      { team:'Female Team',            type:'dancers-female', slug:'dancers-female',
        packages:[
          { key:'2m', label:'2 Members', price:7598,  dir:'2-members', desc:'Elegant duet — perfect for intimate receptions & sangeet events.' },
          { key:'4m', label:'4 Members', price:15196, dir:'4-members', desc:'Compact troupe — ideal for intimate functions & small stage shows.' },
          { key:'5m', label:'5 Members', price:18995, dir:'5-members', desc:'Balanced grace — great for medium-sized celebrations.' },
          { key:'7m', label:'7 Members', price:26593, dir:'7-members', desc:'High-impact group performance for weddings & large parties.' },
          { key:'9m', label:'9 Members', price:34191, dir:'9-members', desc:'Full ensemble — grand stage spectacle for major events.' }
        ]
      },
      { team:'Co-ed Team',             type:'dancers-coed',   slug:'dancers-coed',
        packages:[
          { key:'2m', label:'2 Members', price:6499,  dir:'2-members', desc:'Mixed duet — perfect for couple-centric celebrations & sangeet.' },
          { key:'4m', label:'4 Members', price:12998, dir:'4-members', desc:'Compact mixed team — ideal for intimate functions & small stage shows.' },
          { key:'5m', label:'5 Members', price:16248, dir:'5-members', desc:'Balanced co-ed group — great for medium-sized celebrations.' },
          { key:'7m', label:'7 Members', price:22747, dir:'7-members', desc:'High-impact mixed performance for weddings & large parties.' },
          { key:'9m', label:'9 Members', price:29246, dir:'9-members', desc:'Full ensemble — spectacular stage show for major events.' }
        ]
      }
    ];
    DANCER_SEARCH_DATA.forEach(function(teamData) {
      teamData.packages.forEach(function(p) {
        index.push({
          id:          'dancer-' + teamData.type + '-' + p.key,
          title:       'Dancers — ' + teamData.team + ' ' + p.label,
          description: p.desc,
          base_price:  p.price,
          image:       'uploads/services/dancers.png',
          slug:        teamData.slug,
          category:    'Dancers',
          route:       'services/dancers/' + teamData.slug.replace('dancers-','') + '-team/' + p.dir + '/index.html',
          hasPriceBadge: true,
        });
      });
      // Also index the team-level page
      index.push({
        id:          'dancer-team-' + teamData.type,
        title:       'Dancers — ' + teamData.team,
        description: 'Professional dance troupe — choose your team size from 2 to 9 members.',
        base_price:  0,
        image:       'uploads/services/dancers.png',
        slug:        teamData.slug,
        category:    'Dancers',
        route:       'pages/services.html?type=' + teamData.type,
        hasPriceBadge: false,
      });
    });

    // 5. Decoration subtypes
    DECORATION_SUBTYPES.forEach(sub => {
      index.push({
        id:          'deco-' + sub.slug,
        title:       sub.name,
        description: sub.desc,
        base_price:  0,
        image:       normImg(sub.img),
        slug:        sub.slug,
        category:    'Decoration',
        route:       'pages/services.html?type=' + encodeURIComponent(sub.slug),
        hasPriceBadge: false,
      });
    });

    // 6. Musical Band subtypes
    const BAND_IMG_MAP = {
      'band-set':           'uploads/services/bandset.png',
      'nadhaswaram-thavil': 'uploads/services/nadhaswaram.png',
      'chenda-melam':       'uploads/services/musical_band.png',
      'melam-set':          'uploads/services/musical_band.png',
    };
    MUSICAL_BAND_SUBTYPES.forEach(sub => {
      index.push({
        id:          'band-' + sub.slug,
        title:       sub.name,
        description: sub.desc,
        base_price:  0,
        image:       BAND_IMG_MAP[sub.slug] || normImg(sub.img),
        slug:        sub.slug,
        category:    'Musical Band',
        route:       'pages/services.html?type=' + encodeURIComponent(sub.slug),
        hasPriceBadge: false,
      });
    });

    return index;
  }

  const SEARCH_INDEX = buildSearchIndex();

  /* Parse "under / below / within / less than / upto" + number */
  function parseQuery(raw) {
    const q   = raw.trim().toLowerCase();
    const priceRx = /(?:under|below|within|upto|up to|less\s+than|max|maximum|atmost|at most)\s*₹?\s*([\d,.]+)\s*k?/i;
    const plainPriceRx = /₹?\s*([\d,.]+)\s*k?\s*(?:per\s+plate)?$/i;

    let maxPrice = Infinity;
    let keyword  = q;

    const m = q.match(priceRx);
    if (m) {
      let val = parseFloat(m[1].replace(/,/g,''));
      if (/k/i.test(q.slice(q.indexOf(m[1]) + m[1].length, q.indexOf(m[1]) + m[1].length + 2))) val *= 1000;
      maxPrice = val;
      keyword  = q.replace(priceRx,'').trim();
    } else {
      const pm = q.match(plainPriceRx);
      if (pm) {
        let val = parseFloat(pm[1].replace(/,/g,''));
        maxPrice = val;
        keyword = q.replace(plainPriceRx,'').trim();
      }
    }
    const kRx = /(\d+)\s*k\b/i;
    if (maxPrice === Infinity) {
      const km = q.match(kRx);
      if (km) {
        maxPrice = parseInt(km[1]) * 1000;
        keyword  = q.replace(kRx,'').trim();
      }
    }
    return { keyword: keyword.replace(/[^a-z0-9 &]/gi,'').trim(), maxPrice };
  }

  function highlight(text, term) {
    if (!term) return text;
    const escaped = term.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
    return text.replace(new RegExp(`(${escaped})`, 'gi'), '<span class="match-hl">$1</span>');
  }

  function runSearch(raw) {
    if (!raw.trim()) {
      searchResultsWrap.hidden = true;
      searchSuggestions.hidden = false;
      clearSearchBtn.hidden    = true;
      return;
    }
    clearSearchBtn.hidden    = false;
    searchSuggestions.hidden = true;
    searchResultsWrap.hidden = false;

    const { keyword, maxPrice } = parseQuery(raw);
    const words = keyword.split(/\s+/).filter(Boolean);

    // Score each item in the full index
    const scored = SEARCH_INDEX.map(item => {
      const hay = (item.title + ' ' + item.description + ' ' + item.category).toLowerCase();
      let kwScore = 0;
      if (words.length) {
        const hits = words.filter(w => hay.includes(w));
        kwScore = hits.length / words.length;
      } else {
        kwScore = 1;
      }
      // Price filter: items with no price (0) pass through if no price constraint
      const priceOk = item.base_price === 0
        ? (maxPrice === Infinity)   // no-price items shown only when no price filter
        : item.base_price <= maxPrice;
      return { item, kwScore, priceOk };
    })
    .filter(x => x.kwScore > 0 && x.priceOk)
    .sort((a, b) => {
      const aExact = a.item.title.toLowerCase().includes(keyword) ? 1 : 0;
      const bExact = b.item.title.toLowerCase().includes(keyword) ? 1 : 0;
      if (bExact !== aExact) return bExact - aExact;
      if (b.kwScore !== a.kwScore) return b.kwScore - a.kwScore;
      // Items with no price go after priced ones
      if (a.item.base_price === 0 && b.item.base_price > 0) return 1;
      if (b.item.base_price === 0 && a.item.base_price > 0) return -1;
      return a.item.base_price - b.item.base_price;
    });

    resultsList.innerHTML = '';
    resultsMeta.textContent = '';
    noResults.hidden = true;

    if (!scored.length) {
      noResults.hidden = false;
      resultsMeta.textContent = `No results for "${raw}"`;
      return;
    }

    const priceLabel = maxPrice < Infinity ? ` under ₹${maxPrice.toLocaleString('en-IN')}` : '';
    resultsMeta.textContent = `${scored.length} service${scored.length>1?'s':''} found${priceLabel}`;

    scored.forEach(({ item }) => {
      const li = document.createElement('li');
      li.setAttribute('role','option');

      const titleHl = highlight(item.title, keyword || raw.trim());
      const priceDisplay = item.base_price > 0
        ? (item.base_price < 500
            ? `₹${item.base_price}/plate`
            : `₹${Number(item.base_price).toLocaleString('en-IN')}`)
        : 'Enquire';

      li.innerHTML = `
        <img src="${item.image}" alt="${item.title}" onerror="this.src='uploads/services/stage.png'"/>
        <div class="r-body">
          <div class="r-title">${titleHl}</div>
          <div class="r-sub">
            <span class="r-badge">${item.category}</span>
            <span>${item.description.slice(0,68)}…</span>
          </div>
        </div>
        <div class="r-price">${priceDisplay}</div>`;

      // Route to correct description page
      li.addEventListener('click', () => {
        window.location.href = item.route;
      });
      resultsList.appendChild(li);
    });
  }

  function showPanel() {
    searchPanel.classList.add('active');
    searchPanel.setAttribute('aria-hidden','false');
    searchInput.focus();
    document.body.style.overflow = 'hidden';
  }
  function hidePanel() {
    searchPanel.classList.remove('active');
    searchPanel.setAttribute('aria-hidden','true');
    searchInput.value = '';
    resultsList.innerHTML = '';
    resultsMeta.textContent = '';
    noResults.hidden = true;
    searchResultsWrap.hidden = true;
    searchSuggestions.hidden = false;
    clearSearchBtn.hidden = true;
    document.body.style.overflow = '';
  }

  document.getElementById('desktopSearchTrigger')?.addEventListener('click', showPanel);
  document.getElementById('mobileSearchTrigger')?.addEventListener('click', showPanel);
  document.getElementById('closeSearch')?.addEventListener('click', hidePanel);
  clearSearchBtn?.addEventListener('click', () => { searchInput.value=''; searchInput.focus(); runSearch(''); });
  document.addEventListener('keydown', e => { if (e.key==='Escape') hidePanel(); });

  // Suggestion chips
  document.querySelectorAll('.suggest-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      searchInput.value = chip.dataset.q || chip.textContent.replace(/[^\w\s₹]/g,'').trim();
      runSearch(searchInput.value);
    });
  });

  // Debounced input
  let searchTimer;
  searchInput?.addEventListener('input', e => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => runSearch(e.target.value), 200);
  });

  // Keyboard: Esc closes, Enter opens first result
  searchInput?.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      const first = resultsList.querySelector('li');
      if (first) first.click();
    }
  });

  /* ──────────────────────────────────────────────────────────
     7 visible circles, rest hidden behind Show More
  ────────────────────────────────────────────────────────── */
  const VISIBLE_COUNT = 7;

  const catContainer = document.getElementById('categoryContainer');
  if (catContainer) {
    const adminData  = getAdminServices();
    const allCats    = [...HOME_CATEGORIES];

    adminData.forEach(svc => {
      const slug = (svc.category || '').toLowerCase().replace(/\s+/g,'-');
      if (!allCats.some(c => (c.slug||c.id) === slug)) {
        allCats.push({
          id: slug, name: svc.category || svc.title,
          image: svc.image || 'uploads/services/stage.png',
          hidden: true, slug
        });
      }
    });

    allCats.forEach((cat, idx) => {
      const isHidden = idx >= VISIBLE_COUNT;
      const slug = cat.slug || cat.id;
      const div = document.createElement('div');
      div.className = 'category-item' + (isHidden ? ' hidden-category' : '');
      div.setAttribute('role','listitem');
      div.innerHTML = `<div class="category-image"><img src="${cat.image}" alt="${cat.name}" loading="lazy"/></div><p>${cat.name}</p>`;
      div.addEventListener('click', () => window.location.href = 'pages/services.html?type=' + encodeURIComponent(slug));
      catContainer.appendChild(div);
    });

    const toggleBtn = document.createElement('button');
    toggleBtn.textContent = 'Show More';
    toggleBtn.className   = 'show-more-btn';
    toggleBtn.setAttribute('aria-expanded','false');
    toggleBtn.innerHTML   = '<i class="fa fa-chevron-down"></i> Show More';
    catContainer.after(toggleBtn);

    let expanded = false;
    toggleBtn.addEventListener('click', () => {
      expanded = !expanded;
      document.querySelectorAll('#categoryContainer .hidden-category')
        .forEach(el => el.classList.toggle('visible', expanded));
      toggleBtn.setAttribute('aria-expanded', String(expanded));
      toggleBtn.innerHTML = expanded
        ? '<i class="fa fa-chevron-up"></i> Show Less'
        : '<i class="fa fa-chevron-down"></i> Show More';
      if (!expanded) {
        document.querySelector('.event-category')?.scrollIntoView({ behavior:'smooth' });
      }
    });
  }

  /* ──────────────────────────────────────────────────────────
     Render 4 Event Category tiles
  ────────────────────────────────────────────────────────── */
  const svcContainer = document.getElementById('servicesContainer');
  if (svcContainer) {
    HOME_EVENTS.forEach(s => {
      const div = document.createElement('div');
      div.className = 'service-card' + (s.comingSoon ? ' coming-soon-card' : '');
      div.setAttribute('role', s.comingSoon ? 'article' : 'button');
      if (!s.comingSoon) div.setAttribute('tabindex','0');
      div.innerHTML = `
        <div class="service-card-img-wrap">
          <img src="${s.image}" alt="${s.title}" loading="lazy"/>
          ${s.comingSoon ? '<span class="coming-soon-badge">Coming Soon</span>' : ''}
        </div>
        <h3>${s.title}</h3>
        <p>${s.desc}</p>
        ${s.comingSoon ? '<p class="coming-soon-note">We\'re gearing up for this! Stay tuned.</p>' : ''}
      `;
      if (!s.comingSoon) {
        const go = () => window.location.href = 'pages/category.html?type=' + s.id;
        div.addEventListener('click', go);
        div.addEventListener('keydown', e => { if(e.key==='Enter'||e.key===' ') go(); });
      }
      svcContainer.appendChild(div);
    });
  }

  /* ──────────────────────────────────────────────────────────
     Footer year
  ────────────────────────────────────────────────────────── */
  const yr = document.getElementById('year');
  if (yr) yr.textContent = new Date().getFullYear();

  /* ──────────────────────────────────────────────────────────
     Helper: read admin-added services from localStorage
  ────────────────────────────────────────────────────────── */
  function getAdminServices() {
    try {
      return JSON.parse(localStorage.getItem('ellcy_services') || '[]');
    } catch { return []; }
  }
});
