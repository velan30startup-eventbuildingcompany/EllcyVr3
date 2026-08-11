// ============================================================
// services.js — ELLCY Patch 5.0  (Production Build)
// C1: DJ pricing bar REMOVED
// C2: Decoration: light-decoration shows cards (no price badge)
//     stage-decoration shows 3 items → redirect to desc page
// C3: Photography → single ₹80k package card + filter pills
//     that adjust price; click → ../services/photography/index.html
// C4: crackers / rental-things / invitation-flex removed
// ============================================================
(function () {
  'use strict';

  /* ── XSS guard ──────────────────────────────────────────── */
  function esc(str) {
    const d = document.createElement('div');
    d.textContent = String(str == null ? '' : str);
    return d.innerHTML;
  }

  function fmt(n) {
    return '₹' + Number(n).toLocaleString('en-IN');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const type   = params.get('type') || '';

    /* Slug allowlist — reject malformed input */
    if (type && !/^[a-z0-9\-]+$/.test(type)) {
      window.location.replace('../index.html');
      return;
    }
    if (type === 'flower-rangoli') {
      window.location.replace('services.html');
      return;
    }

    const label = LABEL_MAP[type] || type.replace(/-/g,' ').replace(/\b\w/g, c => c.toUpperCase());
    document.title = 'ELLCY | ' + label;

    const bc              = document.getElementById('breadcrumb-label');
    const ph              = document.getElementById('page-heading');
    const filterContainer = document.getElementById('filtersContainer');
    const grid            = document.getElementById('servicesGrid');

    /* ── Breadcrumb ─────────────────────────────────────── */
    if (bc) {
      if (type === 'stage-decoration' || type === 'light-decoration') {
        bc.innerHTML = `<a href="services.html?type=decoration" style="color:#6b21a8;text-decoration:none">Decoration</a> / ${esc(label)}`;
      } else if (type === 'musical-band' || type === 'music-performers') {
        bc.textContent = 'Music Performers';
      } else if (type === 'real-flowers-reception') {
        bc.innerHTML = `<a href="services.html?type=real-flowers" style="color:#6b21a8;text-decoration:none">Real Flowers</a> / Reception`;
      } else if (type === 'real-flowers-marriage') {
        bc.innerHTML = `<a href="services.html?type=real-flowers" style="color:#6b21a8;text-decoration:none">Real Flowers</a> / Marriage`;
      } else if (type === 'chenda-melam' || type === 'nadhaswaram-thavil' || type === 'band-set' || type === 'melam-set') {
        bc.innerHTML = `<a href="services.html?type=musical-band" style="color:#6b21a8;text-decoration:none">Music Performers</a> / ${esc(label)}`;
      } else if (type === 'human-doll' || type === '360-camera' || type === 'photo-booth') {
        bc.innerHTML = `<a href="services.html?type=entertainment-activities" style="color:#6b21a8;text-decoration:none">Entertainment Activities</a> / ${esc(label)}`;
      } else if (type === 'nadhaswaram-reception' || type === 'nadhaswaram-marriage') {
        bc.innerHTML = `<a href="services.html?type=musical-band" style="color:#6b21a8;text-decoration:none">Music Performers</a> / <a href="services.html?type=nadhaswaram-thavil" style="color:#6b21a8;text-decoration:none">Nadhaswaram &amp; Thavil</a> / ${esc(label.replace('Nadhaswaram & Thavil — ',''))}`;
      } else {
        bc.textContent = label;
      }
    }
    if (ph) ph.textContent = label || 'All Services';

    /* ════════════════════════════════════════════════════
       NO TYPE → Show all service categories as cards
    ════════════════════════════════════════════════════ */
    if (!type) {
      if (ph) ph.textContent = 'Our Event Services';
      if (bc) bc.textContent = 'All Services';
      if (filterContainer) filterContainer.style.display = 'none';
      renderAllServicesGrid(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       DECORATION parent → 2 sub-type cards (no price)
    ════════════════════════════════════════════════════ */
    if (type === 'decoration') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDecorationParent(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       MUSIC PERFORMERS alias → redirect to musical-band
    ════════════════════════════════════════════════════ */
    if (type === 'music-performers') {
      window.location.replace('services.html?type=musical-band');
      return;
    }

    /* ════════════════════════════════════════════════════
       MUSICAL BAND parent → 4 sub-type cards (no price)
    ════════════════════════════════════════════════════ */
    if (type === 'musical-band') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (ph) ph.textContent = 'Music Performers';
      document.title = 'ELLCY | Music Performers';
      renderMusicalBandParent(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       CHENDA MELAM → 7 package cards → detail page
    ════════════════════════════════════════════════════ */
    if (type === 'chenda-melam') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderChendaMelamCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       LIGHT DECORATION → standard cards with price
    ════════════════════════════════════════════════════ */
    if (type === 'light-decoration') {
      if (filterContainer) filterContainer.style.display = 'none';
      const items = SERVICES_DATA['light-decoration'] || [];
      renderCardsNoBadge(grid, items);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       C2 — STAGE DECORATION: 3 cards → desc page (no price)
    ════════════════════════════════════════════════════ */
    if (type === 'stage-decoration') {
      if (filterContainer) filterContainer.style.display = 'none';
      const items = SERVICES_DATA['stage-decoration'] || [];
      renderStageCards(grid, items);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       C3 — PHOTOGRAPHY: single package + filter pills
    ════════════════════════════════════════════════════ */
    if (type === 'photography') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderPhotography(ph, grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       DJ → description page (package-select flow)
    ════════════════════════════════════════════════════ */
    if (type === 'dj') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDjCards(grid, SERVICES_DATA['dj'] || [], '../services/dj/index.html');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       BRIDAL & GROOM STYLING → description page
    ════════════════════════════════════════════════════ */
    if (type === 'bridal-groom-styling') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['bridal-groom-styling'] || [], '../services/bridal-groom-styling/index.html',
        '../uploads/services/bridal.png', 'bridal-groom-styling');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       MEHENDI → description page
    ════════════════════════════════════════════════════ */
    if (type === 'mehandi' || type === 'mehendi') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['mehandi'] || [], '../services/mehendi/index.html',
        '../uploads/services/mehandi.png', 'mehendi');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       CAKE & DECORATION → description page
    ════════════════════════════════════════════════════ */
    if (type === 'cake-decoration') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['cake-decoration'] || [], 'cake-description.html',
        '../uploads/services/cake.png', 'cake-decoration');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       CATERING BOYS → description page
    ════════════════════════════════════════════════════ */
    if (type === 'catering-boys') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderCateringBoysParent(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       FICTIONAL CHARACTERS → description page
    ════════════════════════════════════════════════════ */
    if (type === 'fiction-character' || type === 'fictional-characters') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['fiction-character'] || [], 'fictional-description.html',
        '../uploads/services/fiction.png', 'fictional-characters');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       BIKE & CAR STUNTS → description page
    ════════════════════════════════════════════════════ */
    if (type === 'bike-stunt' || type === 'bike-car-stunts') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['bike-stunt'] || [], 'stunts-description.html',
        '../uploads/services/bikestunts.png', 'bike-car-stunts');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       SNACKS & STALLS → description page
    ════════════════════════════════════════════════════ */
    if (type === 'snacks-stalls') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderSnacksCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       FOOD → existing Breakfast, Lunch, Dinner pages
    ════════════════════════════════════════════════════ */
    if (type === 'food') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, [
        {
          id: 'food-breakfast', title: 'Breakfast',
          description: 'Fresh vegetarian breakfast spreads with 5, 10, 15 or 20 dish options.',
          base_price: 250, image: '../uploads/services/food-veg.jpg',
          href: '../services/food/breakfast/'
        },
        {
          id: 'food-lunch', title: 'Lunch',
          description: 'Choose vegetarian or non-vegetarian lunch menus with flexible dish counts.',
          base_price: 350, image: '../uploads/services/food-veg.jpg',
          href: '../services/food/lunch/'
        },
        {
          id: 'food-dinner', title: 'Dinner',
          description: 'Buffet or banana-leaf dinner menus with vegetarian and non-vegetarian choices.',
          base_price: 450, image: '../uploads/services/food-buffet-veg.jpg',
          href: '../services/food/dinner/'
        }
      ], '../services/food/index.html', '../uploads/services/catering.png', 'food');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       NADHASWARAM & THAVIL — Reception / Marriage cards
    ════════════════════════════════════════════════════ */
    if (type === 'nadhaswaram-thavil') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (!grid) { setYear(); return; }
      grid.innerHTML = '';
      var NT_OCCASIONS = [
        { key:'nadhaswaram-reception', label:'Reception', desc:'Nadhaswaram & Thavil for reception entries — choose 2, 4, 6 or 8 members.', img:'../uploads/services/musical_band.png' },
        { key:'nadhaswaram-marriage',  label:'Marriage',  desc:'Nadhaswaram & Thavil for wedding ceremonies — choose 6, 8, 10 or 12 members.', img:'../uploads/services/musical_band.png' }
      ];
      NT_OCCASIONS.forEach(function(occ) {
        var a = document.createElement('a');
        a.className = 'service-card stage-desc-card';
        a.href = 'services.html?type=' + occ.key;
        a.setAttribute('aria-label', occ.label);
        a.innerHTML =
          '<div class="card-image">' +
            '<img src="' + esc(occ.img) + '" alt="' + esc(occ.label) + '" loading="lazy"/>' +
          '</div>' +
          '<div class="card-body">' +
            '<h3 class="card-title">' + esc(occ.label) + '</h3>' +
            '<p class="card-desc">' + esc(occ.desc) + '</p>' +
          '</div>';
        grid.appendChild(a);
      });
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       NADHASWARAM — RECEPTION packages (4 cards with price)
    ════════════════════════════════════════════════════ */
    if (type === 'nadhaswaram-reception') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderNTReceptionCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       NADHASWARAM — MARRIAGE packages (4 cards with price)
    ════════════════════════════════════════════════════ */
    if (type === 'nadhaswaram-marriage') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderNTMarriageCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       BAND SET — 7 member-count cards → ../services/music-performers/band-set/index.html
    ════════════════════════════════════════════════════ */
    if (type === 'band-set') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderBandSetCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       MELAM SET — 8 member-count cards → ../services/music-performers/melam-set/index.html
    ════════════════════════════════════════════════════ */
    if (type === 'melam-set') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderMelamSetCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       ENTERTAINMENT ACTIVITIES — Human Doll, 360 Cam, Photo Booth
    ════════════════════════════════════════════════════ */
    if (type === 'entertainment-activities') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderEntertainmentCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       HUMAN DOLL — Mascot type cards
    ════════════════════════════════════════════════════ */
    if (type === 'human-doll') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderHumanDollCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       360 DEGREE CAMERA — With/Without iPhone cards
    ════════════════════════════════════════════════════ */
    if (type === '360-camera') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      render360CameraCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       PHOTO BOOTH — Frame count cards
    ════════════════════════════════════════════════════ */
    if (type === 'photo-booth') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderPhotoBoothCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       ENTER SHOW DOWN — 7 effects
    ════════════════════════════════════════════════════ */
    if (type === 'enter-show-down') {
      if (filterContainer) filterContainer.style.display = 'none';
      grid.classList.add('chenda-grid');
      renderEnterShowDownCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       DANCERS — 3 team types, each routed to its own desc page
    ════════════════════════════════════════════════════ */
    if (type === 'dancers') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDancersCards(grid);
      setYear(); return;
    }

    if (type === 'plates-decoration') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (ph) ph.textContent = 'Plates Decoration';
      renderPlatesDecorationCards(grid);
      setYear(); return;
    }
    if (type === 'aarti-plates' || type === 'seer-plates') {
      if (filterContainer) filterContainer.style.display = 'none';
      var plateBcMap = { 'aarti-plates':'Aarti Plates', 'seer-plates':'Seer Plates' };
      if (bc) bc.innerHTML = '<a href="services.html?type=plates-decoration" style="color:#6b21a8;text-decoration:none">Plates Decoration</a> / ' + esc(plateBcMap[type] || '');
      if (ph) ph.textContent = plateBcMap[type] || '';
      renderPlateCountCards(grid, type);
      setYear(); return;
    }

    if (type === 'flower-rangoli') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (ph) ph.textContent = 'Flower Rangoli';
      renderFlowerRangoliCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       DANCERS MALE / FEMALE / COED — member-count pages
    ════════════════════════════════════════════════════ */
    if (type === 'dancers-male' || type === 'dancers-female' || type === 'dancers-coed') {
      if (filterContainer) filterContainer.style.display = 'none';
      var dancerBcMap = { 'dancers-male':'Male Team', 'dancers-female':'Female Team', 'dancers-coed':'Co-ed Men & Women Team' };
      var dancerH1Map = { 'dancers-male':'Male Dance Team', 'dancers-female':'Female Dance Team', 'dancers-coed':'Co-ed Men & Women Team' };
      if (bc) bc.innerHTML = '<a href="services.html?type=dancers" style="color:#6b21a8;text-decoration:none">Dancers</a> / ' + esc(dancerBcMap[type] || '');
      if (ph) ph.textContent = dancerH1Map[type] || '';
      renderDancerMemberCards(grid, type);
      setYear(); return;
    }


    /* ════════════════════════════════════════════════════
       FLOWERS — Reception & Marriage sub-categories
    ════════════════════════════════════════════════════ */
    if (type === 'real-flowers') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (ph) ph.textContent = 'Flowers';
      renderFlowerCategoryCards(grid);
      setYear(); return;
    }
    if (type === 'real-flowers-reception') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=real-flowers" style="color:#6b21a8;text-decoration:none">Flowers</a> / Reception';
      if (ph) ph.textContent = 'Reception Flowers';
      renderFlowerSubCards(grid, 'reception');
      setYear(); return;
    }
    if (type === 'real-flowers-marriage') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=real-flowers" style="color:#6b21a8;text-decoration:none">Flowers</a> / Marriage';
      if (ph) ph.textContent = 'Marriage Flowers';
      renderFlowerSubCards(grid, 'marriage');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       FAKE JEWELLERY — 3 sub-service cards
    ════════════════════════════════════════════════════ */
    if (type === 'fake-jewellery') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (ph) ph.textContent = 'Fake Jewellery';
      renderJewelleryCards(grid);
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       INVITATION
    ════════════════════════════════════════════════════ */
    if (type === 'invitation') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['invitation'] || [], '../services/invitation/index.html',
        '../uploads/services/invitation.png', 'invitation');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       REAL FLOWERS
    ════════════════════════════════════════════════════ */
    /* ════════════════════════════════════════════════════
       REAL FLOWERS — two-level flow:
       Parent: Reception | Marriage (no price)
       Sub-level: Real | Artificial (with price)
    ════════════════════════════════════════════════════ */
    if (type === 'real-flowers') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderRealFlowersParent(grid);
      setYear(); return;
    }
    if (type === 'real-flowers-reception' || type === 'real-flowers-marriage') {
      if (filterContainer) filterContainer.style.display = 'none';
      var groupKey = (type === 'real-flowers-reception') ? 'reception' : 'marriage';
      var groupLabel = (type === 'real-flowers-reception') ? 'Reception' : 'Marriage';
      // Update breadcrumb (use rfBc to avoid redeclaring bc from outer scope)
      var rfBc = document.getElementById('breadcrumb-label');
      if (rfBc) rfBc.innerHTML = '<a href="services.html?type=real-flowers" style="color:#6b21a8;text-decoration:none">Real Flowers</a> / ' + groupLabel;
      var rfH1 = document.getElementById('page-heading');
      if (rfH1) rfH1.textContent = 'Real Flowers — ' + groupLabel;
      // filter items by eventGroup
      var groupItems = (SERVICES_DATA['real-flowers'] || []).filter(function(s){ return s.eventGroup === groupKey; });
      if (!grid) { setYear(); return; }
      grid.innerHTML = '';
      if (!groupItems.length) {
        grid.innerHTML = '<p class="no-services">No services found.</p>';
      } else {
        groupItems.forEach(function(s) {
          var a = document.createElement('a');
          a.className = 'service-card';
          a.href = '../services/real-flowers/index.html?pkg=' + encodeURIComponent(s.id) + '&group=' + encodeURIComponent(groupKey);
          a.setAttribute('aria-label', s.title);
          var priceStr = s.base_price > 0 ? fmt(s.base_price) : '';
          a.innerHTML =
            '<div class="card-image">' +
              '<img src="' + esc(s.image) + '" alt="' + esc(s.title) + '" loading="lazy"/>' +
              (priceStr ? '<div class="price-badge">' + esc(priceStr) + '</div>' : '') +
            '</div>' +
            '<div class="card-body">' +
              '<h3 class="card-title">' + esc(s.title.replace(/^(Reception|Marriage) — /, '')) + ' Flowers</h3>' +
              '<p class="card-desc">' + esc(s.description) + '</p>' +
            '</div>';
          grid.appendChild(a);
        });
      }
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       FAKE JEWELLERY — Gold, Silver, Kundan
    ════════════════════════════════════════════════════ */
    if (type === 'fake-jewellery') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['fake-jewellery'] || [], '../services/fake-jewellery/index.html',
        '../uploads/services/jewellery-gold.jpg', 'fake-jewellery');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       CAR ENTRY — Normal & Luxury
    ════════════════════════════════════════════════════ */
    if (type === 'car-entry') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (ph) ph.textContent = 'Car Entry';
      renderCarEntryCategoryCards(grid);
      setYear(); return;
    }

    /* ── Car Entry: Normal Cars ── */
    if (type === 'car-entry-normal') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=car-entry" style="color:#6b21a8;text-decoration:none">Car Entry</a> / Normal Cars';
      if (ph) ph.textContent = 'Normal Cars';
      renderCarNormalCards(grid);
      setYear(); return;
    }

    /* ── Car Entry: Luxury Cars ── */
    if (type === 'car-entry-luxury') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=car-entry" style="color:#6b21a8;text-decoration:none">Car Entry</a> / Luxury Cars';
      if (ph) ph.textContent = 'Luxury Cars';
      renderCarLuxuryBrandsCards(grid);
      setYear(); return;
    }

    /* ── Car Entry: BMW models ── */
    if (type === 'car-entry-bmw') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=car-entry" style="color:#6b21a8;text-decoration:none">Car Entry</a> / <a href="services.html?type=car-entry-luxury" style="color:#6b21a8;text-decoration:none">Luxury Cars</a> / BMW';
      if (ph) ph.textContent = 'BMW Models';
      renderCarBrandModels(grid, 'bmw');
      setYear(); return;
    }

    /* ── Car Entry: Rolls-Royce models ── */
    if (type === 'car-entry-rolls') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=car-entry" style="color:#6b21a8;text-decoration:none">Car Entry</a> / <a href="services.html?type=car-entry-luxury" style="color:#6b21a8;text-decoration:none">Luxury Cars</a> / Rolls-Royce';
      if (ph) ph.textContent = 'Rolls-Royce';
      renderCarBrandModels(grid, 'rolls-royce');
      setYear(); return;
    }

    /* ── Car Entry: Mercedes-Benz models ── */
    if (type === 'car-entry-mercedes') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=car-entry" style="color:#6b21a8;text-decoration:none">Car Entry</a> / <a href="services.html?type=car-entry-luxury" style="color:#6b21a8;text-decoration:none">Luxury Cars</a> / Mercedes-Benz';
      if (ph) ph.textContent = 'Mercedes-Benz';
      renderCarBrandModels(grid, 'mercedes');
      setYear(); return;
    }

    /* ── Car Entry: Audi models ── */
    if (type === 'car-entry-audi') {
      if (filterContainer) filterContainer.style.display = 'none';
      if (bc) bc.innerHTML = '<a href="services.html?type=car-entry" style="color:#6b21a8;text-decoration:none">Car Entry</a> / <a href="services.html?type=car-entry-luxury" style="color:#6b21a8;text-decoration:none">Luxury Cars</a> / Audi';
      if (ph) ph.textContent = 'Audi Models';
      renderCarBrandModels(grid, 'audi');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       AARTHI PLATE
    ════════════════════════════════════════════════════ */
    if (type === 'aarthi-plate') {
      if (filterContainer) filterContainer.style.display = 'none';
      renderDescPageCards(grid, SERVICES_DATA['aarthi-plate'] || [], '../services/aarthi-plate/index.html',
        '../uploads/services/stage.png', 'aarthi-plate');
      setYear(); return;
    }

    /* ════════════════════════════════════════════════════
       BOUNCERS → description page
    ════════════════════════════════════════════════════ */
    if (type === 'bouncers') {
      if (filterContainer) filterContainer.style.display = 'none';
      window.location.replace('../services/bouncers/index.html');
      return;
    }

    /* ════════════════════════════════════════════════════
       EXCLUDED SERVICES — redirect home
       (Lighting Setup, Fun Things)
    ════════════════════════════════════════════════════ */
    if (type === 'lighting-setup' || type === 'fun-things') {
      window.location.replace('../index.html');
      return;
    }

    /* ════════════════════════════════════════════════════
       ALL OTHER SERVICES: standard cards (C1: no DJ bar)
    ════════════════════════════════════════════════════ */

    /* Merge admin-added services */
    function getAdminServices() {
      try { return JSON.parse(localStorage.getItem('ellcy_services') || '[]'); } catch { return []; }
    }
    const staticList = SERVICES_DATA[type] || [];
    const adminList  = getAdminServices()
      .filter(s => {
        const slug = (s.category || '').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/-+$/,'');
        return slug === type || s.category_slug === type;
      })
      .map((s, i) => ({
        id:          90000 + i,
        title:       s.title,
        description: s.description,
        base_price:  parseFloat(s.base_price) || 0,
        image:       s.image || '../uploads/services/stage.png',
        event_types: ['wedding'],
      }));
    const services = [...staticList, ...adminList];

    /* No filter pills (all wedding-only) */
    if (filterContainer) filterContainer.innerHTML = '';

    /* Standard cards with price badge */
    if (grid) {
      grid.innerHTML = '';
      if (!services.length) {
        grid.innerHTML = '<p class="no-services">No services found for this category.</p>';
      } else {
        services.forEach(s => {
          const a = document.createElement('a');
          a.className = 'service-card';
          a.href      = (s.id === 40) ? '../services/bouncers/index.html' : 'service_details.html?id=' + encodeURIComponent(s.id);
          a.setAttribute('data-events', 'wedding');
          a.setAttribute('aria-label', s.title);
          const priceStr = s.base_price > 0
            ? (s.base_price < 500 ? fmt(s.base_price) + '/plate' : fmt(s.base_price))
            : '';
          a.innerHTML = `
            <div class="card-image">
              <img src="${esc(s.image)}" alt="${esc(s.title)}" loading="lazy"/>
              ${priceStr ? `<div class="price-badge">${esc(priceStr)}</div>` : ''}
            </div>
            <div class="card-body">
              <h3 class="card-title">${esc(s.title)}</h3>
              <p class="card-desc">${esc(s.description)}</p>
            </div>`;
          grid.appendChild(a);
        });
      }
    }

    setYear();
  });

  /* ── HELPERS ────────────────────────────────────────────── */

  /* All-services landing: show every category as a card */
  function renderAllServicesGrid(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    // Use CATEGORY_MAPPINGS.wedding as the canonical full list
    var cats = (typeof CATEGORY_MAPPINGS !== 'undefined' && CATEGORY_MAPPINGS.wedding)
      ? CATEGORY_MAPPINGS.wedding
      : [];
    if (!cats.length) {
      // Fallback: build from HOME_CATEGORIES if CATEGORY_MAPPINGS not available
      cats = (typeof HOME_CATEGORIES !== 'undefined' ? HOME_CATEGORIES : []).map(function(c){
        return { name: c.name, slug: c.slug || c.id, img: '../' + c.image };
      });
    }
    cats.forEach(function(cat) {
      var a = document.createElement('a');
      a.className = 'service-card decoration-sub-card';
      a.href = 'services.html?type=' + encodeURIComponent(cat.slug);
      a.setAttribute('aria-label', cat.name);
      a.innerHTML =
        '<div class="card-image">' +
          '<img src="' + esc(cat.img) + '" alt="' + esc(cat.name) + '" loading="lazy"/>' +
        '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-title">' + esc(cat.name) + '</h3>' +
        '</div>';
      grid.appendChild(a);
    });
  }

  /* Decoration parent: 2 sub-type cards */
  function renderDecorationParent(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    grid.style.gridTemplateColumns = 'repeat(2, 1fr)';
    DECORATION_SUBTYPES.forEach(sub => {
      const a = document.createElement('a');
      a.className = 'service-card decoration-sub-card';
      a.href      = 'services.html?type=' + encodeURIComponent(sub.slug);
      a.setAttribute('aria-label', sub.name);
      a.innerHTML = `
        <div class="card-image">
          <img src="${esc(sub.img)}" alt="${esc(sub.name)}" loading="lazy"/>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(sub.name)}</h3>
          <p class="card-desc">${esc(sub.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Dancers parent: 3 team-type cards → member selection pages */
  /* Shared reference-card renderer used only by Dancers, Car Entry and Snacks & Stalls. */
  function appendReferenceCard(grid, item, kind) {
    const a = document.createElement('a');
    const isPackage = kind === 'package';
    const rating = item.rating == null ? 4.5 : Number(item.rating);
    const suffix = item.suffix == null ? (isPackage ? '' : 'onwards') : item.suffix;

    a.className = 'service-card reference-card ' + (isPackage ? 'reference-package-card' : 'reference-category-card');
    a.href = item.href;
    a.setAttribute('aria-label', item.title);
    a.innerHTML = `
      <div class="card-image reference-card-image">
        <img src="${esc(item.img)}" alt="${esc(item.title)}" loading="lazy"/>
      </div>
      <div class="card-body reference-card-body">
        <div class="reference-card-toprow">
          <h3 class="card-title reference-card-title">${esc(item.title)}</h3>
          <span class="reference-card-rating"><i class="fa-solid fa-star" aria-hidden="true"></i> ${rating.toFixed(1)}</span>
        </div>
        <p class="card-desc reference-card-desc">${esc(item.desc)}</p>
        <div class="reference-card-price-row">
          <span class="reference-card-price-label">${isPackage ? 'Package Price' : 'Starting Package'}</span>
          <span class="reference-card-price">${esc(fmt(item.price))}${suffix ? ` <span>${esc(suffix)}</span>` : ''}</span>
        </div>
        <div class="reference-card-tags">
          <span class="reference-card-tag"><i class="fa-solid fa-medal" aria-hidden="true"></i> ${esc(item.tag)}</span>
        </div>
      </div>`;
    grid.appendChild(a);
  }

  function renderDancersCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const DANCER_TEAMS = [
      { name: 'Male Team',              type: 'dancers-male',
        img: '../uploads/services/dancers-male.jpg',
        desc: 'High-energy all-male dance troupe performing Bollywood, folk and western styles — perfect for weddings & stage shows.',
        from: 11196, tag: 'All-male dance troupe' },
      { name: 'Female Team',            type: 'dancers-female',
        img: '../uploads/services/dancers-female.jpg',
        desc: 'Graceful all-female dance team with classical, semi-classical and contemporary repertoire for every occasion.',
        from: 15196, tag: 'All-female dance troupe' },
      { name: 'Co-ed Men & Women Team', type: 'dancers-coed',
        img: '../uploads/services/dancers-coed.jpg',
        desc: 'Dynamic mixed-gender dance troupe with choreographed group performances for weddings, birthdays & stage shows.',
        from: 12998, tag: 'Mixed dance ensemble' },
    ];
    DANCER_TEAMS.forEach(team => {
      appendReferenceCard(grid, {
        title: team.name,
        href: 'services.html?type=' + team.type,
        img: team.img,
        desc: team.desc,
        price: team.from,
        rating: 4.5,
        tag: team.tag
      }, 'category');
    });
  }

  /* Plates Decoration parent: 2 sub-type cards (Aarti / Seer) */
  function renderPlatesDecorationCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const PLATE_TYPES = [
      { name: 'Aarti Plates', type: 'aarti-plates',
        img: '../uploads/services/aarthi-plates.jpg',
        desc: 'Beautifully decorated aarti plates for poojas, weddings and auspicious ceremonies — choose your plate count.',
        from: 1499 },
      { name: 'Seer Plates', type: 'seer-plates',
        img: '../uploads/services/aarthi-plates.jpg',
        desc: 'Elegant seer plate decoration for wedding trousseau presentations — traditional and premium styling.',
        from: 2499 },
    ];
    PLATE_TYPES.forEach(pt => {
      const a = document.createElement('a');
      a.className = 'service-card stage-desc-card';
      a.href      = 'services.html?type=' + pt.type;
      a.setAttribute('aria-label', pt.name);
      a.innerHTML = `
        <div class="card-image">
          <img src="${esc(pt.img)}" alt="${esc(pt.name)}" loading="lazy"/>
          <div class="price-badge">From ${esc(fmt(pt.from))}</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(pt.name)}</h3>
          <p class="card-desc">${esc(pt.desc)}</p>
          <span class="card-view-btn">Choose Plate Count &rarr;</span>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Aarti/Seer Plates: 4 count-based item cards, routed to desc pages */
  function renderPlateCountCards(grid, plateType) {
    if (!grid) return;
    grid.innerHTML = '';
    grid.classList.add('chenda-grid');

    var PLATE_DATA = {
      'aarti-plates': {
        basePath: '../services/plates-decoration/aarti-plates/',
        img: '../uploads/services/aarthi-plates.jpg',
        packages: [
          { dir:'9-plates',  label:'9 Plates',  price:1499, desc:'Compact aarti plate set — ideal for smaller poojas and home ceremonies.' },
          { dir:'11-plates', label:'11 Plates', price:1999, desc:'Traditional 11-plate set for standard temple and wedding rituals.' },
          { dir:'15-plates', label:'15 Plates', price:2999, desc:'Larger set for grand poojas and multi-family ceremonies.' },
          { dir:'21-plates', label:'21 Plates', price:3999, desc:'Full auspicious 21-plate set for major temple events and large weddings.' }
        ]
      },
      'seer-plates': {
        basePath: '../services/plates-decoration/seer-plates/',
        img: '../uploads/services/aarthi-plates.jpg',
        packages: [
          { dir:'9-plates',  label:'9 Plates',  price:2499, desc:'Elegant 9 seer plate set for intimate trousseau presentations.' },
          { dir:'11-plates', label:'11 Plates', price:3499, desc:'Traditional 11 seer plate set with premium decorative styling.' },
          { dir:'15-plates', label:'15 Plates', price:4999, desc:'Grand 15 seer plate set for larger wedding trousseau ceremonies.' },
          { dir:'21-plates', label:'21 Plates', price:6999, desc:'Full 21 seer plate luxury set for the most elaborate presentations.' }
        ]
      }
    };

    var plates = PLATE_DATA[plateType];
    if (!plates) return;

    plates.packages.forEach(function(p) {
      var a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href      = plates.basePath + p.dir + '/index.html';
      a.setAttribute('aria-label', p.label);
      a.innerHTML =
        '<div class="card-image">' +
          '<img src="' + esc(plates.img) + '" alt="' + esc(p.label) + '" loading="lazy"/>' +
          '<div class="price-badge">₹' + Number(p.price).toLocaleString('en-IN') + '</div>' +
        '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-title">' + esc(p.label) + '</h3>' +
          '<p class="card-desc">' + esc(p.desc) + '</p>' +
        '</div>';
      grid.appendChild(a);
    });
  }

  /* Flower Rangoli: 4 size-based item cards, routed to desc pages */
  function renderFlowerRangoliCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    grid.classList.add('chenda-grid');

    var basePath = '../services/flower-rangoli/';
    var img = '../uploads/services/flowers-decoration-2.jpg';
    var SIZES = [
      { dir:'3x3-feet', label:'3 × 3 Feet', price:2999, desc:'Compact fresh-flower rangoli — perfect for entrances and small courtyards.' },
      { dir:'4x4-feet', label:'4 × 4 Feet', price:4499, desc:'Medium-sized rangoli with richer floral detailing for main entrances.' },
      { dir:'5x5-feet', label:'5 × 5 Feet', price:6499, desc:'Large statement rangoli for wedding halls and grand entryways.' },
      { dir:'6x6-feet', label:'6 × 6 Feet', price:8999, desc:'Premium extra-large rangoli — the centrepiece for major celebrations.' }
    ];

    SIZES.forEach(function(p) {
      var a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href      = basePath + p.dir + '/index.html';
      a.setAttribute('aria-label', p.label);
      a.innerHTML =
        '<div class="card-image">' +
          '<img src="' + esc(img) + '" alt="' + esc(p.label) + '" loading="lazy"/>' +
          '<div class="price-badge">₹' + Number(p.price).toLocaleString('en-IN') + '</div>' +
        '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-title">' + esc(p.label) + '</h3>' +
          '<p class="card-desc">' + esc(p.desc) + '</p>' +
        '</div>';
      grid.appendChild(a);
    });
  }

  /* Catering Boys parent: 2 sub-type cards (no price) */
  function renderCateringBoysParent(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const CATERING_SUBTYPES = [
      { name: 'Catering Boys', slug: 'boys',
        img: '../uploads/services/catering-boys.jpg',
        desc: "Uniformed serving staff to manage food service at your event — breakfast, lunch & dinner." },
      { name: 'Welcome Girls', slug: 'girls',
        img: '../uploads/services/catering-welcome-girls.jpg',
        desc: "Graceful welcome staff to greet & guide your guests — breakfast, lunch & dinner shifts." },
    ];
    CATERING_SUBTYPES.forEach(sub => {
      const a = document.createElement('a');
      a.className = 'service-card decoration-sub-card';
      a.href      = '../services/catering-boys/' + sub.slug + '/index.html';
      a.setAttribute('aria-label', sub.name);
      a.innerHTML = `
        <div class="card-image">
          <img src="${esc(sub.img)}" alt="${esc(sub.name)}" loading="lazy"/>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(sub.name)}</h3>
          <p class="card-desc">${esc(sub.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Real Flowers parent: Reception | Marriage (no price) */
  function renderRealFlowersParent(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    var FLOWER_GROUPS = [
      { name: 'Reception',
        slug: 'real-flowers-reception',
        img:  '../uploads/services/flowers-decoration-1.jpg',
        desc: 'Real & Artificial flower decoration for your wedding reception — stage, entry arch and tables.' },
      { name: 'Marriage',
        slug: 'real-flowers-marriage',
        img:  '../uploads/services/flowers-decoration-2.jpg',
        desc: 'Real & Artificial flower mandapam, garlands and full-venue decor for the wedding ceremony.' },
    ];
    FLOWER_GROUPS.forEach(function(sub) {
      var a = document.createElement('a');
      a.className = 'service-card decoration-sub-card';
      a.href      = 'services.html?type=' + encodeURIComponent(sub.slug);
      a.setAttribute('aria-label', sub.name);
      a.innerHTML =
        '<div class="card-image">' +
          '<img src="' + esc(sub.img) + '" alt="' + esc(sub.name) + '" loading="lazy"/>' +
        '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-title">' + esc(sub.name) + '</h3>' +
          '<p class="card-desc">' + esc(sub.desc) + '</p>' +
        '</div>';
      grid.appendChild(a);
    });
  }

  function ensureMusicPerformerListingStyles() {
    if (document.getElementById('musicPerformerListingStyles')) return;
    const style = document.createElement('style');
    style.id = 'musicPerformerListingStyles';
    style.textContent = `
      .music-performer-card { position:relative; }
      .music-performer-image { position:relative; }
      .music-performer-body { min-height:235px; gap:12px; }
      .music-performer-toprow { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
      .music-performer-title { flex:1; }
      .music-performer-rating { flex-shrink:0; display:inline-flex; align-items:center; gap:4px; color:#1a1a2e; font-size:.8rem; font-weight:800; }
      .music-performer-rating i { color:#1a1a2e; }
      .music-performer-desc { min-height:63px; -webkit-line-clamp:3; line-clamp:3; }
      .music-performer-price-row { display:flex; align-items:baseline; justify-content:space-between; gap:8px; margin-top:auto; padding-top:12px; border-top:1px dashed rgba(0,0,0,.1); }
      .music-performer-price-label { color:#888; font-size:.72rem; font-weight:600; }
      .music-performer-price { color:#6a1b9a; font-size:1.05rem; font-weight:800; white-space:nowrap; }
      .music-performer-price span { color:#888; font-size:.72rem; font-weight:600; }
      .music-performer-tags { min-height:28px; }
      .music-performer-tag { display:inline-flex; align-items:center; gap:5px; max-width:100%; padding:5px 10px; border-radius:999px; background:#f4e9ff; color:#6a1b9a; font-size:.72rem; font-weight:700; }
      @media (max-width:600px) {
        .music-performer-body { min-height:255px; padding:12px; gap:9px; }
        .music-performer-toprow, .music-performer-price-row { align-items:flex-start; flex-direction:column; }
        .music-performer-desc { min-height:78px; -webkit-line-clamp:4; line-clamp:4; }
        .music-performer-price-row { gap:3px; }
        .music-performer-tag { border-radius:12px; white-space:normal; }
      }
    `;
    document.head.appendChild(style);
  }

  /* Music Performers parent: DJ-style cards for the four performer types. */
  function renderMusicalBandParent(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    grid.classList.add('music-performer-grid');

    const displayOrder = ['chenda-melam', 'band-set', 'melam-set', 'nadhaswaram-thavil'];
    const displayMeta = {
      'chenda-melam':       { price:11994, rating:4.5, tag:'Traditional percussion' },
      'band-set':           { price:11994, rating:4.5, tag:'Brass band ensemble' },
      'melam-set':          { price:7994,  rating:4.5, tag:'Traditional melam' },
      'nadhaswaram-thavil': { price:2999,  rating:4.5, tag:'Classical live ensemble' }
    };

    const performerTypes = MUSICAL_BAND_SUBTYPES.slice().sort((a, b) =>
      displayOrder.indexOf(a.slug) - displayOrder.indexOf(b.slug)
    );

    performerTypes.forEach(sub => {
      const meta = displayMeta[sub.slug];
      const a = document.createElement('a');
      a.className = 'service-card music-performer-card';
      a.href      = 'services.html?type=' + encodeURIComponent(sub.slug);
      a.setAttribute('aria-label', sub.name);
      a.innerHTML = `
        <div class="card-image music-performer-image">
          <img src="${esc(sub.img)}" alt="${esc(sub.name)}" loading="lazy"/>
        </div>
        <div class="card-body music-performer-body">
          <div class="music-performer-toprow">
            <h3 class="card-title music-performer-title">${esc(sub.name)}</h3>
            <span class="music-performer-rating"><i class="fa-solid fa-star" aria-hidden="true"></i> ${meta.rating.toFixed(1)}</span>
          </div>
          <p class="card-desc music-performer-desc">${esc(sub.desc)}</p>
          <div class="music-performer-price-row">
            <span class="music-performer-price-label">Starting Package</span>
            <span class="music-performer-price">${esc(fmt(meta.price))} <span>onwards</span></span>
          </div>
          <div class="music-performer-tags">
            <span class="music-performer-tag"><i class="fa-solid fa-medal" aria-hidden="true"></i> ${esc(meta.tag)}</span>
          </div>
        </div>`;
      grid.appendChild(a);
    });

    ensureMusicPerformerListingStyles();
  }

  /* Light decoration: cards without price badge */
  /* Light-decoration: cards → ../services/light-decoration/index.html (enquiry) */
  function renderCardsNoBadge(grid, items) {
    if (!grid) return;
    grid.innerHTML = '';
    items.forEach(s => {
      const a = document.createElement('a');
      a.className = 'service-card stage-desc-card';
      a.href      = '../services/light-decoration/index.html';
      a.setAttribute('aria-label', s.title);
      a.innerHTML = `
        <div class="card-image">
          <img src="${esc(s.image)}" alt="${esc(s.title)}" loading="lazy"/>
          <div class="stage-enquire-badge">Enquire Now</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(s.title)}</h3>
          <p class="card-desc">${esc(s.description)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Stage decoration: 3 cards → ../services/photography/index.html with context */
  function renderStageCards(grid, items) {
    if (!grid) return;
    grid.innerHTML = '';
    items.forEach(s => {
      const a = document.createElement('a');
      a.className = 'service-card stage-desc-card';
      a.href      = '../services/stage-decoration/index.html';
      a.setAttribute('aria-label', s.title);
      a.innerHTML = `
        <div class="card-image">
          <img src="${esc(s.image)}" alt="${esc(s.title)}" loading="lazy"/>
          <div class="stage-enquire-badge">Enquire Now</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(s.title)}</h3>
          <p class="card-desc">${esc(s.description)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Photography: single package card — NO filter pills on this page.
     Filter pills live exclusively on ../services/photography/index.html (the booking page). */
  function renderPhotography(ph, grid) {
    if (!grid) return;
    if (ph) ph.textContent = 'Photography';
    grid.innerHTML = '';
    grid.classList.add('music-performer-grid');

    [
      { dir:'photo-package', label:'Photo Package', price:25000, rating:4.8,
        desc:'Professional photo-only coverage for your full-day event — dedicated photographer, edited gallery delivered digitally.', tag:'Photo-only coverage' },
      { dir:'photo-video', label:'Photo + Video', price:30000, rating:4.8,
        desc:'Complete photo and cinematic video coverage — professional photographer plus a videography team with edited highlight reel.', tag:'Photo + video coverage' },
    ].forEach(function(pkg) {
      var a = document.createElement('a');
      a.className = 'service-card music-performer-card';
      a.href = '/ellcy/services/photography/' + pkg.dir + '/';
      a.setAttribute('aria-label', pkg.label);
      a.innerHTML = `
        <div class="card-image music-performer-image">
          <img src="../uploads/services/photo.png" alt="${esc(pkg.label)}" loading="lazy"/>
        </div>
        <div class="card-body music-performer-body">
          <div class="music-performer-toprow">
            <h3 class="card-title music-performer-title">${esc(pkg.label)}</h3>
            <span class="music-performer-rating"><i class="fa-solid fa-star" aria-hidden="true"></i> ${pkg.rating.toFixed(1)}</span>
          </div>
          <p class="card-desc music-performer-desc">${esc(pkg.desc)}</p>
          <div class="music-performer-price-row">
            <span class="music-performer-price-label">Starting Package</span>
            <span class="music-performer-price">${esc(fmt(pkg.price))} <span>onwards</span></span>
          </div>
          <div class="music-performer-tags">
            <span class="music-performer-tag"><i class="fa-solid fa-medal" aria-hidden="true"></i> ${esc(pkg.tag)}</span>
          </div>
        </div>`;
      grid.appendChild(a);
    });
    ensureMusicPerformerListingStyles();
  }

  function renderPhotographyLegacy(ph, grid) {
    if (!grid) return;
    if (ph) ph.textContent = 'Photography';
    grid.innerHTML = '';
    grid.style.gridTemplateColumns = '1fr';

    const total = PHOTOGRAPHY_BASE_PRICE; /* Default / starting price */

    const card = document.createElement('a');
    card.className = 'service-card photo-package-card';
    card.setAttribute('aria-label', PHOTOGRAPHY_PACKAGE.title);
    card.href = '../services/photography/index.html';

    card.innerHTML = `
      <div class="photo-pkg-inner">
        <div class="photo-pkg-img-wrap">
          <img src="${esc(PHOTOGRAPHY_PACKAGE.image)}" alt="Photography Package" loading="lazy"/>
          <div class="photo-pkg-price-wrap">
            <span class="photo-pkg-price">Starting ₹${Number(total).toLocaleString('en-IN')}</span>
            <span class="photo-pkg-addon">Choose your package type on the next page</span>
          </div>
        </div>
        <div class="photo-pkg-body">
          <h3 class="photo-pkg-title">${esc(PHOTOGRAPHY_PACKAGE.title)}</h3>
          <p class="photo-pkg-desc">${esc(PHOTOGRAPHY_PACKAGE.description)}</p>
          <div class="photo-pkg-features">
            <span class="photo-feat">📷 Professional Photographer</span>
            <span class="photo-feat">🖼️ 300+ Edited Photos</span>
            <span class="photo-feat">📦 Premium Printed Album</span>
            <span class="photo-feat">☁️ Private Online Gallery</span>
          </div>
          <span class="photo-pkg-cta">View Packages &amp; Book →</span>
        </div>
      </div>`;

    grid.appendChild(card);
  }

  /* Chenda Melam: 7 package cards — 2 per row mobile / 4 per row desktop */
  function renderChendaMelamCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    // Use CSS service-grid (already 2-col on mobile via services.css)
    const PACKAGES = [
      { key:'6m',  label:'6 Members',  price:11994, desc:'Small performance. Best for pooja / home events.' },
      { key:'8m',  label:'8 Members',  price:15992, desc:'Medium sound impact. Suitable for small functions.' },
      { key:'10m', label:'10 Members', price:19990, desc:'Balanced performance. Ideal for weddings.' },
      { key:'12m', label:'12 Members', price:23988, desc:'Medium-large performance. Ideal for weddings & special occasions.' },
      { key:'15m', label:'15 Members', price:29985, desc:'High energy performance. Temple & grand events.' },
      { key:'18m', label:'18 Members', price:35982, desc:'Powerful traditional setup. Large celebrations.' },
      { key:'20m', label:'20 Members', price:39980, desc:'Grand Chenda Melam. Festival-level performance.' },
    ];
    PACKAGES.forEach(p => {
      const a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href      = '../services/music-performers/chenda-melam/index.html?pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Chenda Melam ' + p.label);
      a.innerHTML = `
        <div class="card-image">
          <img src="../uploads/services/chenda-melam.png" alt="Chenda Melam ${esc(p.label)}" loading="lazy"/>
          <div class="price-badge">₹${Number(p.price).toLocaleString('en-IN')}</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(p.label)}</h3>
          <p class="card-desc">${esc(p.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Nadhaswaram & Thavil — Reception: 4 cards with price → description page */
  function renderNTReceptionCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    var PKGS = [
      { key:'rec-2', label:'2 Members', price:2999,  desc:'Compact Nadhaswaram & Thavil duo — perfect for intimate reception entries and auspicious welcomes.' },
      { key:'rec-4', label:'4 Members', price:4999,  desc:'Balanced 4-member ensemble bringing a fuller, richer sound to mid-sized reception ceremonies.' },
      { key:'rec-6', label:'6 Members', price:6999,  desc:'Rich 6-member ensemble for grand reception entries with elevated festive energy.' },
      { key:'rec-8', label:'8 Members', price:9999,  desc:'Full 8-member ensemble delivering a powerful, celebratory welcome for large receptions.' }
    ];
    PKGS.forEach(function(p) {
      var a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = '../services/music-performers/nadhaswaram-thavil/index.html?pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Nadhaswaram & Thavil Reception ' + p.label);
      a.innerHTML =
        '<div class="card-image">' +
          '<img src="../uploads/services/musical_band.png" alt="NT Reception ' + esc(p.label) + '" loading="lazy"/>' +
          '<div class="price-badge">₹' + Number(p.price).toLocaleString('en-IN') + '</div>' +
        '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-title">' + esc(p.label) + '</h3>' +
          '<p class="card-desc">' + esc(p.desc) + '</p>' +
        '</div>';
      grid.appendChild(a);
    });
  }

  /* Nadhaswaram & Thavil — Marriage: 4 cards with price → description page */
  function renderNTMarriageCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    var PKGS = [
      { key:'mar-6',  label:'6 Members',  price:12999, desc:'6-member ensemble for wedding rituals and processions, blending tradition with festive energy.' },
      { key:'mar-8',  label:'8 Members',  price:15999, desc:'8-member ensemble bringing a fuller, more resonant sound to grand wedding ceremonies.' },
      { key:'mar-10', label:'10 Members', price:17999, desc:'Grand 10-member ensemble ideal for larger weddings and elaborate procession routes.' },
      { key:'mar-12', label:'12 Members', price:19999, desc:'Our largest ensemble — 12 musicians for a truly grand, temple-festival-scale wedding celebration.' }
    ];
    PKGS.forEach(function(p) {
      var a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = '../services/music-performers/nadhaswaram-thavil/index.html?pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Nadhaswaram & Thavil Marriage ' + p.label);
      a.innerHTML =
        '<div class="card-image">' +
          '<img src="../uploads/services/musical_band.png" alt="NT Marriage ' + esc(p.label) + '" loading="lazy"/>' +
          '<div class="price-badge">₹' + Number(p.price).toLocaleString('en-IN') + '</div>' +
        '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-title">' + esc(p.label) + '</h3>' +
          '<p class="card-desc">' + esc(p.desc) + '</p>' +
        '</div>';
      grid.appendChild(a);
    });
  }

  /* Generic desc-page redirect cards:
     shows service cards with image + "Book Now" badge → description page */
  /* Band Set: 7 member-count cards — 2-col mobile / 4-col desktop */
  function renderBandSetCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const PACKAGES = [
      { key:'bs-6',  label:'6 Members',  price:11994, desc:'Compact 6-member brass band for intimate wedding entries and smaller processions.' },
      { key:'bs-8',  label:'8 Members',  price:15992, desc:'8-member ensemble delivering a fuller brass sound for mid-sized wedding processions.' },
      { key:'bs-10', label:'10 Members', price:19990, desc:'Impressive 10-member brass band for larger wedding ceremonies and grand entries.' },
      { key:'bs-12', label:'12 Members', price:23988, desc:'Grand 12-member ensemble with uniformed performers and drum major for large processions.' },
      { key:'bs-15', label:'15 Members', price:29985, desc:'Premium 15-member brass band with LED costumes and choreographed drum majors.' },
      { key:'bs-18', label:'18 Members', price:35982, desc:'Elite 18-member ensemble delivering a wall of sound for extravagant weddings.' },
      { key:'bs-20', label:'20 Members', price:39980, desc:'Our flagship 20-member full brass band — the ultimate grand entry experience.' },
    ];
    PACKAGES.forEach(p => {
      const a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href      = '../services/music-performers/band-set/index.html?pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Band Set ' + p.label);
      const badgeHtml = p.price > 0
        ? `<div class="price-badge">₹${Number(p.price).toLocaleString('en-IN')}</div>`
        : `<div class="stage-enquire-badge">Contact for Price</div>`;
      a.innerHTML = `
        <div class="card-image">
          <img src="../uploads/services/musical_band.png" alt="Band Set ${esc(p.label)}" loading="lazy"/>
          ${badgeHtml}
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(p.label)}</h3>
          <p class="card-desc">${esc(p.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Melam Set: 8 member-count cards — 2-col mobile / 4-col desktop */
  function renderMelamSetCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const PACKAGES = [
      { key:'ms-4',  label:'4 Members',  price:7994,  desc:'Compact 4-member melam set for intimate ceremonies, home poojas and smaller festive occasions.' },
      { key:'ms-6',  label:'6 Members',  price:11994, desc:'6-member traditional percussion ensemble for mid-sized processions and auspicious family functions.' },
      { key:'ms-8',  label:'8 Members',  price:15992, desc:'8-member melam set delivering a fuller, resonant sound for wedding processions.' },
      { key:'ms-10', label:'10 Members', price:19990, desc:'Grand 10-member ensemble for larger wedding processions and temple festival ceremonies.' },
      { key:'ms-12', label:'12 Members', price:23988, desc:'12-member percussion ensemble creating a powerful atmosphere for grand weddings.' },
      { key:'ms-15', label:'15 Members', price:29985, desc:'Premium 15-member melam set for large-scale processions and cultural celebrations.' },
      { key:'ms-18', label:'18 Members', price:35982, desc:'Elite 18-member ensemble delivering an immersive wall of percussion for grand events.' },
      { key:'ms-20', label:'20 Members', price:39980, desc:'Our flagship 20-member grand procession ensemble — the ultimate traditional melam experience.' },
    ];
    PACKAGES.forEach(p => {
      const a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href      = '../services/music-performers/melam-set/index.html?pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Melam Set ' + p.label);
      const badgeHtml = p.price > 0
        ? `<div class="price-badge">₹${Number(p.price).toLocaleString('en-IN')}</div>`
        : `<div class="stage-enquire-badge">Contact for Price</div>`;
      a.innerHTML = `
        <div class="card-image">
          <img src="../uploads/services/musical_band.png" alt="Melam Set ${esc(p.label)}" loading="lazy"/>
          ${badgeHtml}
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(p.label)}</h3>
          <p class="card-desc">${esc(p.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Enter Show Down: 7 effect type cards */
  function renderEnterShowDownCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const EFFECTS = [
      { key:'pyro-show',       label:'Pyro Show',        price:299,  emoji:'🎆', desc:'Spectacular choreographed pyro burst — colourful and dramatic for grand entries.' },
      { key:'entry-pot-fag',   label:'Entry Pot Fag',    price:459,  emoji:'🌫️', desc:'Mystical low-lying fog from entry pots — cinematic entrance for bride & groom.' },
      { key:'paper-blast',     label:'Paper Blast',      price:299,  emoji:'🎊', desc:'High-energy confetti paper blast for entries, first dance and celebrations.' },
      { key:'rose-blast',      label:'Rose Blast',       price:299,  emoji:'🌹', desc:'Romantic rose petal shower burst — elegant and fragrant for couple moments.' },
      { key:'bollon-blast',    label:'Balloon Blast',    price:599,  emoji:'🎈', desc:'Hundreds of balloons released simultaneously — festive and crowd-delighting.' },
      { key:'stage-fog-setup', label:'Stage Fog Setup',  price:599,  emoji:'💨', desc:'Professional stage fog machine setup for dramatic atmospheric effects.' },
      { key:'gun-paper-blast', label:'Gun Paper Blast',  price:499,  emoji:'🎉', desc:'Handheld confetti gun blast — instant celebration effect for any special moment.' },
    ];
    EFFECTS.forEach(p => {
      const a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = '../services/enter-show-down/index.html?pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Enter Show Down ' + p.label);
      a.innerHTML = `
        <div class="card-image">
          <img src="../uploads/services/entershow-pyro-show.jpg" alt="${esc(p.label)}" loading="lazy"/>
          <div class="price-badge">₹${Number(p.price).toLocaleString('en-IN')}</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${p.emoji} ${esc(p.label)}</h3>
          <p class="card-desc">${esc(p.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Entertainment Activities: 3 category cards → sub-type pages */
  function renderEntertainmentCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    grid.classList.add('music-performer-grid');
    const ITEMS = [
      { label:'Human Doll (Mascots)', price:2499,  rating:4.5, tag:'Interactive mascots', desc:'Life-size human doll & mascot characters — Cute, Giant, Cartoon & Couple styles for entertaining guests.', img:'../uploads/services/fun.png',       link:'/ellcy/services/entertainment-activities/?type=human-doll' },
      { label:'360° Degree Camera',   price:11899, rating:4.7, tag:'Immersive 360° video', desc:'Immersive 360° slow-motion video booth — instant shareable clips for your guests.',                        img:'../uploads/services/photobooth.png', link:'/ellcy/services/entertainment-activities/?type=360-camera' },
      { label:'Photo Booth',          price:17999, rating:4.4, tag:'Instant photo experience', desc:'Fully branded photo booth with props and instant prints. Crowd favourite at every event.',                  img:'../uploads/services/photobooth.png', link:'/ellcy/services/entertainment-activities/?type=photo-booth' },
    ];
    ITEMS.forEach(item => {
      const a = document.createElement('a');
      a.className = 'service-card music-performer-card';
      a.href = item.link;
      a.setAttribute('aria-label', item.label);
      a.innerHTML = `
        <div class="card-image music-performer-image">
          <img src="${esc(item.img || '../uploads/services/photobooth.png')}" alt="${esc(item.label)}" loading="lazy"/>
        </div>
        <div class="card-body music-performer-body">
          <div class="music-performer-toprow">
            <h3 class="card-title music-performer-title">${esc(item.label)}</h3>
            <span class="music-performer-rating"><i class="fa-solid fa-star" aria-hidden="true"></i> ${item.rating.toFixed(1)}</span>
          </div>
          <p class="card-desc music-performer-desc">${esc(item.desc)}</p>
          <div class="music-performer-price-row">
            <span class="music-performer-price-label">Starting Package</span>
            <span class="music-performer-price">${esc(fmt(item.price))} <span>onwards</span></span>
          </div>
          <div class="music-performer-tags">
            <span class="music-performer-tag"><i class="fa-solid fa-medal" aria-hidden="true"></i> ${esc(item.tag)}</span>
          </div>
        </div>`;
      grid.appendChild(a);
    });
    ensureMusicPerformerListingStyles();
  }

  /* Human Doll: 5 mascot type cards */
  function renderHumanDollCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const PACKAGES = [
      { key:'hd-cute',    label:'Cute Mascot',    price:2499, desc:'Adorable cute character mascot — perfect for kids events and sweet photo moments.' },
      { key:'hd-giant',   label:'Giant Mascot',   price:3899, desc:'Towering giant mascot that commands attention and wows every guest at the venue.' },
      { key:'hd-cartoon', label:'Cartoon Mascot', price:2899, desc:'Popular cartoon character mascot for themed events, birthday parties and children.' },
      { key:'hd-couple',  label:'Couple Mascot',  price:5699, desc:'Matching couple mascot duo — a charming entertainment pair for weddings and receptions.' },
    ];
    PACKAGES.forEach(p => {
      const a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = '../services/entertainment-activities/index.html?type=human-doll&pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Human Doll ' + p.label);
      a.innerHTML = `
        <div class="card-image">
          <img src="../uploads/services/fun.png" alt="Human Doll ${esc(p.label)}" loading="lazy"/>
          <div class="price-badge">₹${Number(p.price).toLocaleString('en-IN')}</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(p.label)}</h3>
          <p class="card-desc">${esc(p.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* 360 Degree Camera: 2 option cards */
  function render360CameraCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const PACKAGES = [
      { key:'cam-iphone',    label:'With iPhone',    price:13899, desc:'Premium 360° booth with the latest iPhone — crisp 4K slow-motion videos for instant sharing.' },
      { key:'cam-no-iphone', label:'Without iPhone',  price:11899, desc:'Full 360° rotating camera experience with professional-grade video quality and instant sharing.' },
    ];
    PACKAGES.forEach(p => {
      const a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = '../services/entertainment-activities/index.html?type=360-camera&pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', '360 Camera ' + p.label);
      a.innerHTML = `
        <div class="card-image">
          <img src="../uploads/services/fun.png" alt="360 Camera ${esc(p.label)}" loading="lazy"/>
          <div class="price-badge">₹${Number(p.price).toLocaleString('en-IN')}</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(p.label)}</h3>
          <p class="card-desc">${esc(p.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* Photo Booth: 4 frame count cards */
  function renderPhotoBoothCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    const PACKAGES = [
      { key:'pb-1', label:'Frame 1', price:17999, desc:'Elegant single-frame photo booth setup with props, instant prints and digital sharing.' },
      { key:'pb-2', label:'Frame 2', price:18499, desc:'Two-frame photo booth experience — double the fun with themed props and instant prints.' },
      { key:'pb-3', label:'Frame 3', price:19999, desc:'Triple-frame booth for larger groups — premium props, lighting, and instant photo delivery.' },
      { key:'pb-4', label:'Frame 4', price:20499, desc:'Full four-frame grand photo booth — the ultimate setup for weddings and premium events.' },
    ];
    PACKAGES.forEach(p => {
      const a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = '../services/entertainment-activities/index.html?type=photo-booth&pkg=' + encodeURIComponent(p.key);
      a.setAttribute('aria-label', 'Photo Booth ' + p.label);
      a.innerHTML = `
        <div class="card-image">
          <img src="../uploads/services/fun.png" alt="Photo Booth ${esc(p.label)}" loading="lazy"/>
          <div class="price-badge">₹${Number(p.price).toLocaleString('en-IN')}</div>
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(p.label)}</h3>
          <p class="card-desc">${esc(p.desc)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }

  /* ── DJ Service Listing — matches client reference screenshot:
     "Popular" badge, star rating + review count, starting-package
     price row, and experience/award tag pills. Only the DJ LISTING
     page uses this — DJ description pages and every other category's
     listing still use the generic renderDescPageCards() above. ── */
  function renderDjCards(grid, items, descPage) {
    if (!grid) return;
    grid.innerHTML = '';
    items.forEach((s, idx) => {
      const a = document.createElement('a');
      a.className = 'service-card dj-listing-card';
      const pkgParam = s.pkgKey || ('p' + (idx + 1));
      a.href = descPage + (descPage.includes('?') ? '&' : '?') + 'pkg=' + encodeURIComponent(pkgParam);
      a.setAttribute('aria-label', s.title);
      const priceStr = fmt(s.base_price);
      const rating = s.rating != null ? s.rating.toFixed(1) : '';
      const reviews = s.reviews || 0;
      a.innerHTML = `
        <div class="card-image dj-card-image">
          ${s.tag ? `<div class="dj-tag-badge"><i class="fa-solid fa-fire"></i> ${esc(s.tag)}</div>` : ''}
          <img src="${esc(s.image)}" alt="${esc(s.title)}" loading="lazy"/>
        </div>
        <div class="card-body dj-card-body">
          <div class="dj-card-toprow">
            <h3 class="card-title dj-card-title">${esc(s.title)}</h3>
            ${rating ? `<span class="dj-rating-pill"><i class="fa-solid fa-star"></i> ${rating}${reviews ? ` <span class="dj-review-count">(${reviews} reviews)</span>` : ''}</span>` : ''}
          </div>
          <p class="card-desc dj-card-desc">${esc(s.description)}</p>
          <div class="dj-price-row">
            <span class="dj-price-label">Starting Package</span>
            <span class="dj-price-val">${esc(priceStr)} <span class="dj-price-unit">onwards</span></span>
          </div>
          <div class="dj-tags-row">
            ${s.experienceYears ? `<span class="dj-tag-pill"><i class="fa-solid fa-medal"></i> ${esc(String(s.experienceYears))}+ years of experience</span>` : ''}
          </div>
        </div>`;
      grid.appendChild(a);
    });
    if (!document.getElementById('djListingStyles')) {
      var style = document.createElement('style');
      style.id = 'djListingStyles';
      style.textContent = `
        .dj-listing-card { position:relative; }
        .dj-card-image { position:relative; }
        .dj-tag-badge { position:absolute; top:10px; left:10px; z-index:2; background:#1a1a2e; color:#fff;
          padding:4px 10px; border-radius:6px; font-size:.7rem; font-weight:700; display:flex; align-items:center; gap:4px; }
        .dj-tag-badge i { color:#ff6b35; }
        .dj-card-toprow { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
        .dj-rating-pill { flex-shrink:0; font-size:.8rem; font-weight:800; color:#1a1a2e; display:flex; align-items:center; gap:4px; }
        .dj-rating-pill i { color:#e91e63; }
        .dj-review-count { font-weight:500; color:#888; font-size:.72rem; }
        .dj-price-row { display:flex; align-items:baseline; justify-content:space-between; margin-top:10px;
          padding-top:10px; border-top:1px dashed rgba(0,0,0,.08); }
        .dj-price-label { font-size:.72rem; color:#888; font-weight:600; }
        .dj-price-val { font-size:1.05rem; font-weight:800; color:#6a1b9a; }
        .dj-price-unit { font-size:.72rem; font-weight:600; color:#888; }
        .dj-tags-row { margin-top:8px; }
        .dj-tag-pill { display:inline-flex; align-items:center; gap:5px; background:#f4e9ff; color:#6a1b9a;
          font-size:.72rem; font-weight:700; padding:4px 10px; border-radius:999px; }
      `;
      document.head.appendChild(style);
    }
  }


  function renderDescPageCards(grid, items, descPage, fallbackImg, serviceSlug) {
    if (!grid) return;
    grid.innerHTML = '';
    // If no static items, show a single "Go to booking" card
    const list = items.length ? items : [{
      id: 0, title: LABEL_MAP[serviceSlug] || serviceSlug,
      description: 'Click to view packages and book this service.',
      base_price: 0, image: fallbackImg, event_types: ['wedding'],
    }];
    list.forEach((s, idx) => {
      const a = document.createElement('a');
      a.className = 'service-card stage-desc-card';
      // Append ?pkg= if item carries a pkgKey; else append index-based key p1,p2...
      // This ensures each card on the listing page routes to its own package on the desc page
      const pkgParam = s.pkgKey || ('p' + (idx + 1));
      a.href = s.href || (descPage + (descPage.includes('?') ? '&' : '?') + 'pkg=' + encodeURIComponent(pkgParam));
      a.setAttribute('aria-label', s.title);
      const priceStr = s.base_price > 0 ? fmt(s.base_price) : '';
      a.innerHTML = `
        <div class="card-image">
          <img src="${esc(s.image || fallbackImg)}" alt="${esc(s.title)}" loading="lazy"/>
          ${priceStr
            ? `<div class="price-badge">${esc(priceStr)}</div>`
            : '<div class="stage-enquire-badge">Book Now</div>'}
        </div>
        <div class="card-body">
          <h3 class="card-title">${esc(s.title)}</h3>
          <p class="card-desc">${esc(s.description)}</p>
        </div>`;
      grid.appendChild(a);
    });
  }



  /* Snacks & Stalls: 6 individual service cards each with own page */
  function renderSnacksCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    var SNACKS = [
      { title:'Cotton Candy',       desc:'Fluffy, colourful cotton candy freshly spun for your guests.',            img:'../uploads/services/cotton-candy.png',       page:'../services/snacks-stalls/cotton-candy/index.html',       price:11994, suffix:'onwards',      tag:'Freshly spun treats' },
      { title:'Pop Corn',           desc:'Fresh, hot popcorn served live at your event for all ages.',              img:'../uploads/services/popcorn.png',            page:'../services/snacks-stalls/popcorn/index.html',            price:3999,  suffix:'onwards',      tag:'Live snack counter' },
      { title:'Chocolate Fountain', desc:'Flowing rich chocolate with fruits and treats for dipping.',              img:'../uploads/services/chocolate-fountain.png', page:'../services/snacks-stalls/chocolate-fountain/index.html', price:6999,  suffix:'onwards',      tag:'Dessert fountain' },
      { title:'Fruit Salad',        desc:'Fresh seasonal fruit salads served cup by cup — choose your combo.',      img:'../uploads/services/fruit-salad.png',        page:'../services/snacks-stalls/fruit-salad/index.html',        price:199,   suffix:'/cup onwards', tag:'Fresh fruit counter' },
      { title:'Ice Cream',          desc:'Premium ice cream in multiple flavours — Vanilla, Choco, Mango & more.',  img:'../uploads/services/ice-cream.png',          page:'../services/snacks-stalls/ice-cream/index.html',          price:49,    suffix:'/scoop onwards',tag:'Ice cream parlour' },
      { title:'Mojito & Tea',       desc:'Refreshing live mojito and specialty tea counter for your guests.',       img:'../uploads/services/mojito.png',             page:'../services/snacks-stalls/mojito/index.html',             price:4000,  suffix:'onwards',      tag:'Live beverage counter' },
    ];
    SNACKS.forEach(function(s) {
      appendReferenceCard(grid, {
        title: s.title,
        href: s.page,
        img: s.img,
        desc: s.desc,
        price: s.price,
        suffix: s.suffix,
        rating: 4.5,
        tag: s.tag
      }, 'category');
    });
  }

  /* Generic standard cards with price badge */
  function renderStandardCards(grid, items) {
    if (!grid) return;
    grid.innerHTML = '';
    if (!items.length) {
      grid.innerHTML = '<p class="no-services">No services found.</p>';
      return;
    }
    items.forEach(function(s) {
      var a = document.createElement('a');
      a.className = 'service-card';
      a.href = 'service_details.html?id=' + encodeURIComponent(s.id);
      a.setAttribute('aria-label', s.title);
      var priceStr = s.base_price > 0
        ? (s.base_price < 500 ? fmt(s.base_price) + '/plate' : fmt(s.base_price))
        : '';
      a.innerHTML =
        '<div class="card-image">' +
          '<img src="' + esc(s.image) + '" alt="' + esc(s.title) + '" loading="lazy"/>' +
          (priceStr ? '<div class="price-badge">' + esc(priceStr) + '</div>' : '') +
        '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-title">' + esc(s.title) + '</h3>' +
          '<p class="card-desc">' + esc(s.description) + '</p>' +
        '</div>';
      grid.appendChild(a);
    });
  }

  /* Dancer member-count cards for a specific team type */
  function renderDancerMemberCards(grid, teamType) {
    if (!grid) return;
    grid.innerHTML = '';
    grid.classList.add('chenda-grid');

    var TEAM_DATA = {
      'dancers-male': {
        basePath: '../services/dancers/male-team/',
        image: '../uploads/services/dancers-male.jpg',
        packages: [
          { dir:'4-members', label:'4 Members', price:11196, desc:'Compact troupe — ideal for intimate functions & small stage shows.' },
          { dir:'5-members', label:'5 Members', price:13995, desc:'Balanced energy — great for medium-sized celebrations.' },
          { dir:'7-members', label:'7 Members', price:19593, desc:'High-impact group performance for weddings & large parties.' },
          { dir:'9-members', label:'9 Members', price:25191, desc:'Full ensemble — grand stage spectacle for major events.' }
        ]
      },
      'dancers-female': {
        basePath: '../services/dancers/female-team/',
        image: '../uploads/services/dancers-female.jpg',
        packages: [
          { dir:'4-members', label:'4 Members', price:15196, desc:'Compact troupe — ideal for intimate functions & small stage shows.' },
          { dir:'5-members', label:'5 Members', price:18995, desc:'Balanced grace — great for medium-sized celebrations.' },
          { dir:'7-members', label:'7 Members', price:26593, desc:'High-impact group performance for weddings & large parties.' },
          { dir:'9-members', label:'9 Members', price:34191, desc:'Full ensemble — grand stage spectacle for major events.' }
        ]
      },
      'dancers-coed': {
        basePath: '../services/dancers/coed-team/',
        image: '../uploads/services/dancers-coed.jpg',
        packages: [
          { dir:'4-members', label:'4 Members',   price:12998, desc:'Compact mixed team — ideal for intimate functions & small stage shows.' },
          { dir:'6-members', label:'6 Members',   price:19497, desc:'Balanced co-ed group — great for medium-sized celebrations.' },
          { dir:'8-members', label:'8 Members',   price:25996, desc:'High-impact mixed performance for weddings & large parties.' },
          { dir:'10-members', label:'10 Members', price:33995, desc:'Grand mixed ensemble — spectacular stage show for major events.' },
          { dir:'12-members', label:'12 Members', price:40794, desc:'Full co-ed production — the ultimate wedding stage spectacle.' }
        ]
      }
    };

    var team = TEAM_DATA[teamType];
    if (!team) return;

    team.packages.forEach(function(p) {
      appendReferenceCard(grid, {
        title: p.label,
        href: team.basePath + p.dir + '/index.html',
        img: team.image,
        desc: p.desc,
        price: p.price,
        rating: 4.5,
        tag: p.label + ' live dance team'
      }, 'package');
    });
  }


  /* ── Flower category cards (Reception / Marriage) ── */
  function renderFlowerCategoryCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    [
      { name:'Reception', type:'real-flowers-reception',
        img:'../uploads/services/flowers-decoration-1.jpg',
        desc:'Elegant flower decoration for your wedding reception — stage, entry arch and table centres.', from:5000 },
      { name:'Marriage',  type:'real-flowers-marriage',
        img:'../uploads/services/flowers-decoration-2.jpg',
        desc:'Traditional and grand flower mandapam decoration for your wedding ceremony.', from:5000 },
    ].forEach(function(cat) {
      var a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = 'services.html?type=' + cat.type;
      a.setAttribute('aria-label', cat.name);
      a.innerHTML =
        '<div class="card-image"><img src="' + esc(cat.img) + '" alt="' + esc(cat.name) + '" loading="lazy"/>' +
        '<div class="price-badge">From ₹' + Number(cat.from).toLocaleString('en-IN') + '</div></div>' +
        '<div class="card-body"><h3 class="card-title">' + esc(cat.name) + '</h3>' +
        '<p class="card-desc">' + esc(cat.desc) + '</p>' +
        '<span class="card-view-btn">View Options →</span></div>';
      grid.appendChild(a);
    });
  }

  /* ── Flower sub-cards (Real / Artificial) ── */
  function renderFlowerSubCards(grid, occasion) {
    if (!grid) return;
    grid.innerHTML = '';
    var items = [
      { name:'Real Flowers', price:5000,
        path:'../services/flowers/' + occasion + '-real/index.html',
        desc:'Fresh seasonal flowers — fragrant, vibrant and perfect for photos.', img:'../uploads/services/flowers-decoration-1.jpg' },
      { name:'Artificial Flowers', price:6000,
        path:'../services/flowers/' + occasion + '-artificial/index.html',
        desc:'Premium artificial flowers — consistent beauty that lasts all day.', img:'../uploads/services/flowers-decoration-2.jpg' },
    ];
    items.forEach(function(item) {
      var a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = item.path;
      a.setAttribute('aria-label', item.name);
      a.innerHTML =
        '<div class="card-image"><img src="' + esc(item.img) + '" alt="' + esc(item.name) + '" loading="lazy"/>' +
        '<div class="price-badge">₹' + Number(item.price).toLocaleString('en-IN') + '</div></div>' +
        '<div class="card-body"><h3 class="card-title">' + esc(item.name) + '</h3>' +
        '<p class="card-desc">' + esc(item.desc) + '</p></div>';
      grid.appendChild(a);
    });
  }

  /* ── Jewellery style cards (Gold / Silver / Kundan) ── */
  function renderJewelleryCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    [
      { name:'Gold Style',   price:6500, path:'../services/jewellery/gold-style/index.html',
        desc:'Premium gold-finish jewellery set — perfect for traditional & bridal looks.', img:'../uploads/services/jewellery-gold.jpg' },
      { name:'Silver Style', price:6500, path:'../services/jewellery/silver-style/index.html',
        desc:'Elegant silver-finish jewellery set — ideal for contemporary & fusion outfits.', img:'../uploads/services/jewellery-silver.jpg' },
      { name:'Kundan Style', price:7000, path:'../services/jewellery/kundan-style/index.html',
        desc:'Intricate Kundan jewellery — the royal choice for brides & grand occasions.', img:'../uploads/services/jewellery-kundan.jpg' },
    ].forEach(function(item) {
      var a = document.createElement('a');
      a.className = 'service-card chenda-pkg-card';
      a.href = item.path;
      a.setAttribute('aria-label', item.name);
      a.innerHTML =
        '<div class="card-image"><img src="' + esc(item.img) + '" alt="' + esc(item.name) + '" loading="lazy"/>' +
        '<div class="price-badge">₹' + Number(item.price).toLocaleString('en-IN') + '</div></div>' +
        '<div class="card-body"><h3 class="card-title">' + esc(item.name) + '</h3>' +
        '<p class="card-desc">' + esc(item.desc) + '</p></div>';
      grid.appendChild(a);
    });
  }


  /* ── Car Entry: Top-level 2 category cards ── */
  function renderCarEntryCategoryCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    [
      { name:'Normal Cars',  type:'car-entry-normal',
        img:'../uploads/services/car-entry-normal.jpg',
        desc:'Choose from Mini, SUV, Prime Sedan and more — beautifully decorated for your wedding entry.', from:3000, tag:'Decorated wedding cars' },
      { name:'Luxury Cars',  type:'car-entry-luxury',
        img:'../uploads/services/car-entry-luxury.jpg',
        desc:'BMW, Rolls-Royce, Mercedes-Benz and Audi — arrive in ultimate style at your wedding.', from:13000, tag:'Luxury wedding cars' },
    ].forEach(function(cat) {
      appendReferenceCard(grid, {
        title: cat.name,
        href: 'services.html?type=' + cat.type,
        img: cat.img,
        desc: cat.desc,
        price: cat.from,
        rating: 4.5,
        tag: cat.tag
      }, 'category');
    });
  }

  /* ── Car Entry: Normal cars 5-vehicle listing ── */
  function renderCarNormalCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    var NORMALS = [
      { label:'Mini – 4 Seater',        dir:'mini-4-seater',        price:3000, desc:'Compact & stylishly decorated — ideal for intimate wedding entrances.' },
      { label:'Prime SUV – 6 Seater',   dir:'prime-suv-6-seater',   price:5000, desc:'Spacious Premium SUV adorned with fresh flowers for a grand arrival.' },
      { label:'SUV – 6 Seater',         dir:'suv-6-seater',         price:4500, desc:'Bold, spacious and beautifully decorated for your wedding entry.' },
      { label:'Prime Sedan – 4 Seater', dir:'prime-sedan-4-seater', price:3500, desc:'Classic sedan elegance — timeless and perfectly decorated.' },
      { label:'Prime Plus – 4 Seater',  dir:'prime-plus-4-seater',  price:4000, desc:'Enhanced premium decoration for an elevated wedding car experience.' },
    ];
    NORMALS.forEach(function(car) {
      appendReferenceCard(grid, {
        title: car.label,
        href: '../services/car-entry/normal-cars/' + car.dir + '/index.html',
        img: '../uploads/services/car-entry-normal.jpg',
        desc: car.desc,
        price: car.price,
        rating: 4.5,
        tag: 'Decorated wedding car'
      }, 'package');
    });
  }

  /* ── Car Entry: Luxury brand listing ── */
  function renderCarLuxuryBrandsCards(grid) {
    if (!grid) return;
    grid.innerHTML = '';
    [
      { name:'BMW',           type:'car-entry-bmw',       from:13000, desc:'7 Series, X3, X1, M4, 3 Series, 2 Series and M2 — available for your wedding.', tag:'BMW luxury fleet' },
      { name:'Rolls-Royce',   type:'car-entry-rolls',     from:50000, desc:'White & Black Rolls-Royce — the ultimate symbol of wedding luxury.', tag:'Rolls-Royce fleet' },
      { name:'Mercedes-Benz', type:'car-entry-mercedes',  from:14000, desc:'C-Class, E-Class, A-Class and GLC — executive elegance for your arrival.', tag:'Mercedes-Benz fleet' },
      { name:'Audi',          type:'car-entry-audi',      from:14000, desc:'Q3, Q5, Q7, Q8, A4 and A6 — dynamic German luxury for your wedding.', tag:'Audi luxury fleet' },
    ].forEach(function(brand) {
      appendReferenceCard(grid, {
        title: brand.name,
        href: 'services.html?type=' + brand.type,
        img: '../uploads/services/car-entry-luxury.jpg',
        desc: brand.desc,
        price: brand.from,
        rating: 4.5,
        tag: brand.tag
      }, 'category');
    });
  }

  /* ── Car Entry: Brand model listings ── */
  function renderCarBrandModels(grid, brand) {
    if (!grid) return;
    grid.innerHTML = '';
    var BRAND_MODELS = {
      'bmw': [
        { label:'7 Series', dir:'7-series', price:25000, desc:'Flagship BMW — the ultimate in luxury and prestige.' },
        { label:'X3',       dir:'x3',       price:18000, desc:'Luxury SUV — commanding presence with premium comfort.' },
        { label:'X1',       dir:'x1',       price:15000, desc:'Compact luxury SUV — sophisticated and dynamic.' },
        { label:'M4',       dir:'m4',       price:22000, desc:'High-performance M-series — sporty and exhilarating.' },
        { label:'3 Series', dir:'3-series', price:15000, desc:'Classic BMW elegance — refined and prestigious.' },
        { label:'2 Series', dir:'2-series', price:13000, desc:'Compact luxury — stylish and perfectly proportioned.' },
        { label:'M2',       dir:'m2',       price:20000, desc:'Sports performance — pure driving excitement.' },
      ],
      'rolls-royce': [
        { label:'White Rolls-Royce', dir:'white', price:50000, desc:'The ultimate wedding car — pure white royalty.' },
        { label:'Black Rolls-Royce', dir:'black', price:50000, desc:'Majestic black — commanding and breathtakingly grand.' },
      ],
      'mercedes': [
        { label:'C-Class',       dir:'c-class',       price:16000, desc:'Elegant executive sedan — a wedding classic.' },
        { label:'E-Class Sedan', dir:'e-class-sedan', price:20000, desc:'Premium executive luxury — refined and prestigious.' },
        { label:'A-Class',       dir:'a-class',       price:14000, desc:'Modern and dynamic — stylish compact luxury.' },
        { label:'GLC',           dir:'glc',           price:18000, desc:'Luxury SUV — commanding, sophisticated presence.' },
      ],
      'audi': [
        { label:'Q3', dir:'q3', price:15000, desc:'Compact luxury SUV — sporty Audi dynamics.' },
        { label:'Q5', dir:'q5', price:18000, desc:'Premium SUV — the perfect blend of style and space.' },
        { label:'Q7', dir:'q7', price:22000, desc:'Full-size luxury SUV — bold, spacious and grand.' },
        { label:'Q8', dir:'q8', price:25000, desc:'Flagship SUV — dramatic presence and supreme luxury.' },
        { label:'A4', dir:'a4', price:14000, desc:'Executive sedan — progressive design and elegance.' },
        { label:'A6', dir:'a6', price:18000, desc:'Business class — sophistication and premium refinement.' },
      ]
    };
    var models = BRAND_MODELS[brand] || [];
    var brandDir = brand;
    models.forEach(function(car) {
      appendReferenceCard(grid, {
        title: car.label,
        href: '../services/car-entry/luxury-cars/' + brandDir + '/' + car.dir + '/index.html',
        img: '../uploads/services/car-entry-luxury.jpg',
        desc: car.desc,
        price: car.price,
        rating: 4.5,
        tag: car.label + ' luxury car'
      }, 'package');
    });
  }


  function setYear() {
    const yr = document.getElementById('year');
    if (yr) yr.textContent = new Date().getFullYear();
  }

})();
