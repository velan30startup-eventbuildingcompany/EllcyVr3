// ============================================================
// catering-staff-calc.js — ELLCY Dynamic Staff Calculator
// Applies to the 9 Catering Boys / Welcome Girls leaf pages.
// Rewrites the Guest Count / Dish Count selects to the exact
// values from the client's source Excel sheets, replaces the
// old manual "Number of Staff" +/- control with a read-only,
// auto-calculated figure (Excel lookup via /api/catering-staff),
// and drives the price total from Guest Count (per-person rate)
// instead of the old, unrelated manual staff quantity.
//
// Expects on <body>:
//   data-catering-style="banana_leaf" | "buffet"
//   data-rate="<per-person rate for this page>"
// Silently does nothing on pages without these attributes.
// ============================================================
(function () {
  'use strict';

  var body = document.body;
  var STYLE = body.getAttribute('data-catering-style');
  var RATE  = parseInt(body.getAttribute('data-rate') || '0', 10);
  if (!STYLE || !RATE) return; // not a catering staff-calc page

  var GUEST_COUNTS = [50,100,150,200,250,300,350,400,450,500,550,600,650,700,750,800,850,900,950,1000];
  var DISH_BANDS   = [
    { value: '0-10',  label: '0 to 10 Dishes' },
    { value: '10-20', label: '10 to 20 Dishes' },
    { value: '20-30', label: '20 to 30 Dishes' },
    { value: '30-40', label: '30 to 40 Dishes' }
  ];

  var ROOT_PREFIX = (function () {
    var scripts = document.getElementsByTagName('script');
    for (var i = 0; i < scripts.length; i++) {
      var src = scripts[i].getAttribute('src') || '';
      if (/(^|\/)catering-staff-calc\.js(\?.*)?$/.test(src)) {
        return src.slice(0, src.indexOf('js/catering-staff-calc.js'));
      }
    }
    return '';
  })();

  function fmt(n) { return Number(n).toLocaleString('en-IN'); }

  function rebuildSelect(sel, options, placeholder) {
    if (!sel) return;
    var current = sel.value;
    sel.innerHTML = '<option value="">' + placeholder + '</option>' +
      options.map(function (o) {
        return '<option value="' + o.value + '">' + o.label + '</option>';
      }).join('');
    if (current) sel.value = current;
  }

  function replaceStaffCard(prefix) {
    // prefix is 'm' (desktop) or 'd' (mobile)
    var qtyCard = document.getElementById(prefix + 'Qty');
    var card = qtyCard ? qtyCard.closest('.bnc-qty-card') : null;
    if (!card) return null;
    card.innerHTML =
      '<div class="bnc-qty-header">' +
        '<label class="bnc-qty-label">Required Staff</label>' +
        '<div id="' + prefix + 'StaffReadout" style="font-weight:800;color:#6a1b9a;font-size:1rem;">' +
          '<span style="color:#999;font-weight:600;font-size:.8rem;">Select guest &amp; dish count</span>' +
        '</div>' +
      '</div>' +
      '<div class="bnc-total-row">' +
        '<span class="bnc-total-lbl">Total Amount</span>' +
        '<span class="bnc-total-val" id="' + prefix + 'Total">&#8377;0</span>' +
      '</div>';
    return card;
  }

  function updateTotal(prefix, guestCount) {
    var totalEl = document.getElementById(prefix + 'Total');
    if (!totalEl) return;
    if (!guestCount) { totalEl.innerHTML = '&#8377;0'; return; }
    totalEl.innerHTML = '&#8377;' + fmt(RATE) + ' &times; ' + guestCount + ' guests = <b>&#8377;' + fmt(RATE * guestCount) + '</b>';
  }

  function updateStaff(prefix) {
    var guestSel = document.getElementById(prefix + 'GuestCount');
    var dishSel  = document.getElementById(prefix + 'DishCount');
    var readout  = document.getElementById(prefix + 'StaffReadout');
    if (!guestSel || !dishSel || !readout) return;
    var guest = parseInt(guestSel.value || '0', 10);
    var dish  = dishSel.value;
    updateTotal(prefix, guest);
    if (!guest || !dish) {
      readout.innerHTML = '<span style="color:#999;font-weight:600;font-size:.8rem;">Select guest &amp; dish count</span>';
      return;
    }
    readout.innerHTML = '<span style="color:#999;font-weight:600;font-size:.8rem;">Calculating&hellip;</span>';
    fetch(ROOT_PREFIX + 'api/catering-staff?style=' + encodeURIComponent(STYLE) +
          '&guest_count=' + encodeURIComponent(guest) + '&dish_band=' + encodeURIComponent(dish))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          readout.innerHTML = data.workers + ' People';
        } else {
          readout.innerHTML = '<span style="color:#c0392b;font-size:.8rem;">' + (data.message || 'Unable to calculate') + '</span>';
        }
      })
      .catch(function () {
        readout.innerHTML = '<span style="color:#c0392b;font-size:.8rem;">Calculation unavailable</span>';
      });
  }

  function setupPrefix(prefix) {
    var guestSel = document.getElementById(prefix + 'GuestCount');
    var dishSel  = document.getElementById(prefix + 'DishCount');
    if (!guestSel || !dishSel) return;

    // Keep the approved order on both layouts: Guest Count, then Dish Count.
    var guestCard = guestSel.closest('.cat-count-card');
    var dishCard = dishSel.closest('.cat-count-card');
    var row = guestCard && guestCard.parentNode;
    if (row && dishCard && dishCard.parentNode === row) row.insertBefore(guestCard, dishCard);

    rebuildSelect(guestSel, GUEST_COUNTS.map(function (g) { return { value: g, label: g + ' Guests' }; }), 'Select guest count…');
    rebuildSelect(dishSel, DISH_BANDS, 'Select dish count…');
    replaceStaffCard(prefix);

    guestSel.addEventListener('change', function () { updateStaff(prefix); });
    dishSel.addEventListener('change', function () { updateStaff(prefix); });
  }

  function init() {
    setupPrefix('m');
    setupPrefix('d');

    // The primary CTA becomes an auth-gated "Book Now" (handled centrally
    // by cart.js's EllcyCart.buyNow once wired); we only need to make sure
    // the button reflects the currently selected guest/dish/staff choice.
    ['mBook', 'dBook'].forEach(function (id) {
      var btn = document.getElementById(id);
      if (!btn) return;
      btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Book Now';
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
