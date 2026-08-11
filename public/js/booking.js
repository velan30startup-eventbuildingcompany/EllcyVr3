// booking.js — Unified Booking Page (client-side)
// SECURITY: Price is NEVER read from URL params.
// It is resolved from SERVICES_DATA (the canonical data source) using pkg + service keys.
// A user cannot manipulate the URL to change the price displayed or submitted.
document.addEventListener('DOMContentLoaded', () => {
  const params      = new URLSearchParams(window.location.search);
  const serviceKey  = params.get('service') || '';
  const pkgKey      = params.get('pkg')     || '';
  const pkgLabel    = params.get('label')   || '';
  const slot        = params.get('slot')    || '';
  // NOTE: params.get('price') is intentionally IGNORED.
  // Price is always resolved from SERVICES_DATA below.

  /* ── Resolve canonical price from data (not URL) ─────────── */
  function resolvePrice(serviceKey, pkgKey) {
    if (typeof SERVICES_DATA === 'undefined') return 0;
    const packages = SERVICES_DATA[serviceKey] || [];
    if (!packages.length) return 0;
    // Match by pkg key (converted from label) or fall back to first item
    const match = pkgKey
      ? packages.find(p =>
          String(p.id) === pkgKey ||
          (p.title || '').toLowerCase().replace(/[^a-z0-9]+/g, '-') === pkgKey
        )
      : null;
    const item = match || packages[0];
    return item ? (item.base_price || 0) : 0;
  }

  /* ── Also resolve from cart (most accurate for multi-pkg) ── */
  function resolveFromCart(serviceKey, pkgLabel) {
    try {
      const cart = JSON.parse(localStorage.getItem('ellcy_cart') || '[]');
      const item = cart.find(i =>
        i.slug === serviceKey && (!pkgLabel || i.package === pkgLabel || i.title.includes(pkgLabel))
      );
      return item ? (item.price || 0) : 0;
    } catch { return 0; }
  }

  const LABEL_FRIENDLY = (typeof LABEL_MAP !== 'undefined' && serviceKey)
    ? (LABEL_MAP[serviceKey] || serviceKey)
    : serviceKey;

  // Resolve price: prefer cart (has exact pkg match), fall back to data lookup
  let resolvedPrice = resolveFromCart(serviceKey, pkgLabel);
  if (!resolvedPrice) resolvedPrice = resolvePrice(serviceKey, pkgKey);

  /* ── Populate sidebar service card ─────────────────────────── */
  const imgEl   = document.getElementById('svc-img');
  const titleEl = document.getElementById('svc-title');
  const descEl  = document.getElementById('svc-desc');
  const priceEl = document.getElementById('svc-price');

  if (imgEl)   imgEl.src           = '../uploads/services/' + serviceKey.replace(/[^a-z0-9-]/g, '') + '.png';
  if (titleEl) titleEl.textContent = LABEL_FRIENDLY + (pkgLabel ? ' — ' + pkgLabel : '');
  if (descEl)  descEl.textContent  = slot ? 'Preferred slot: ' + slot : 'Professional event service';
  if (priceEl) priceEl.textContent = resolvedPrice > 0
    ? '₹' + Number(resolvedPrice).toLocaleString('en-IN') : '';

  /* ── Pre-fill notes (price from resolved source, never URL) ── */
  const notesEl = document.getElementById('notes');
  if (notesEl) {
    const parts = [];
    if (LABEL_FRIENDLY) parts.push('Service: ' + LABEL_FRIENDLY);
    if (pkgLabel)       parts.push('Package: ' + pkgLabel);
    if (slot)           parts.push('Slot: '    + slot);
    if (resolvedPrice)  parts.push('Price: ₹' + Number(resolvedPrice).toLocaleString('en-IN'));
    notesEl.value = parts.join(' | ');
  }

  /* ── Min date: day after tomorrow ──────────────────────────── */
  const minDate = new Date();
  minDate.setDate(minDate.getDate() + 2);
  const minStr  = minDate.toISOString().split('T')[0];
  const dateInput = document.getElementById('event_date');
  if (dateInput) dateInput.setAttribute('min', minStr);

  /* ── Toast helper ───────────────────────────────────────────── */
  const toast = document.getElementById('toast');
  function showToast(msg, ok = true) {
    toast.textContent = msg;
    toast.className   = 'toast ' + (ok ? 'toast-success' : 'toast-error');
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3500);
  }

  /* ── Validation ─────────────────────────────────────────────── */
  function validate() {
    const name  = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const type  = document.getElementById('event_type').value;
    const date  = document.getElementById('event_date').value;
    const venue = document.getElementById('venue').value.trim();
    if (!name || !phone || !type || !date || !venue)
      return { ok: false, msg: 'Please complete all required fields.' };
    if (phone.replace(/\D/g, '').length < 9)
      return { ok: false, msg: 'Please enter a valid phone number.' };
    if (new Date(date) < minDate)
      return { ok: false, msg: 'Please select a date at least 2 days from today.' };
    return { ok: true };
  }

  /* ── Modal ──────────────────────────────────────────────────── */
  const modal   = document.getElementById('confirmModal');
  const openBtn = document.getElementById('openConfirm');
  const closeBtn= document.getElementById('modalClose');
  const editBtn = document.getElementById('editBooking');
  const payBtn  = document.getElementById('proceedPay');

  function openModal()  { modal.setAttribute('aria-hidden','false'); modal.style.display='flex'; }
  function closeModal() { modal.setAttribute('aria-hidden','true');  modal.style.display='none'; }

  openBtn?.addEventListener('click', () => {
    const v = validate();
    if (!v.ok) { showToast(v.msg, false); return; }
    openModal();
  });
  closeBtn?.addEventListener('click', closeModal);
  editBtn?.addEventListener('click',  closeModal);
  modal?.addEventListener('click', e => { if (e.target === modal) closeModal(); });

  payBtn?.addEventListener('click', () => {
    closeModal();
    showToast('Booking confirmed! Our team will contact you shortly.', true);
    setTimeout(() => { window.location.href = 'success.html'; }, 1500);
  });
});
