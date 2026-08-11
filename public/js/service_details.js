// service_details.js — Service Details page
// Updated: Redirects to the correct description page based on service slug
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const id     = parseInt(params.get('id')) || 0;
  const svc    = ALL_SERVICES.find(s => s.id === id);

  if (!svc) {
    document.body.innerHTML = '<div style="text-align:center;padding:60px"><h2>Service not found.</h2><a href="../index.html" style="color:#6b21a8">← Home</a></div>';
    return;
  }

  // Find category slug for this service
  const slug = Object.keys(SERVICES_DATA).find(k => SERVICES_DATA[k].some(s => s.id === id)) || '';

  // Redirect map: slug → correct description page
  const REDIRECT_MAP = {
    'dj':                  'dj-description.html',
    'bridal-groom-styling':'bridal-description.html',
    'mehandi':             'mehendi-description.html',
    'cake-decoration':     'cake-description.html',
    'catering-boys':       'catering-description.html',
    'fiction-character':   'fictional-description.html',
    'bike-stunt':          'stunts-description.html',
    'snacks-stalls':       'snacks-description.html',
    'bouncers':            'bouncer-description.html',
    'stage-decoration':    '../services/stage-decoration/index.html',
    'light-decoration':    '../services/light-decoration/index.html',
    'photography':         'photo-description.html',
    'chenda-melam':        'chenda-melam-description.html',
  };

  if (REDIRECT_MAP[slug]) {
    window.location.replace(REDIRECT_MAP[slug]);
    return;
  }

  // Fallback: render generic detail page for any remaining services
  document.title = 'ELLCY | ' + svc.title;
  document.getElementById('det-title').textContent        = svc.title;
  document.getElementById('det-price').textContent        = '₹' + Number(svc.base_price).toLocaleString('en-IN');
  document.getElementById('det-description').textContent  = svc.description;
  document.getElementById('mainImage').src                = svc.image;

  const bc = document.getElementById('bc-category');
  if (bc) { bc.textContent = LABEL_MAP[slug] || slug; bc.href = 'services.html?type=' + slug; }
  const bcs = document.getElementById('bc-service');
  if (bcs) bcs.textContent = svc.title;

  const thumbsWrap = document.getElementById('galleryThumbs');
  if (thumbsWrap) {
    [svc.image, svc.image].forEach((src, i) => {
      const btn = document.createElement('button');
      btn.className = 'thumb';
      btn.setAttribute('data-src', src);
      btn.innerHTML = `<img src="${src}" alt="thumb ${i+1}"/>`;
      thumbsWrap.appendChild(btn);
    });
  }

  const mainImg = document.getElementById('mainImage');
  document.querySelectorAll('.gallery-thumbs .thumb').forEach(btn => {
    btn.addEventListener('click', () => {
      mainImg.src = btn.getAttribute('data-src');
      document.querySelectorAll('.gallery-thumbs .thumb').forEach(t => t.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
      tab.classList.add('active');
      const target = document.getElementById(tab.dataset.tab);
      if (target) target.classList.remove('hidden');
      document.querySelector('.info-tabs')?.scrollIntoView({ behavior:'smooth', block:'start' });
    });
  });

  const bookBtn = document.getElementById('bookNowBtn');
  if (bookBtn) bookBtn.href = 'booking.html?id=' + svc.id;

  const topGrid = document.getElementById('topGrid');
  if (topGrid) {
    ALL_SERVICES.filter(s => s.id !== id).slice(0,4).forEach(s => {
      const div = document.createElement('div');
      div.className = 'top-card';
      div.innerHTML = `<a href="service_details.html?id=${s.id}">
        <img src="${s.image}" alt="${s.title}" loading="lazy"/>
        <div class="top-info">
          <strong>${s.title}</strong>
          <span class="top-price">₹${Number(s.base_price).toLocaleString('en-IN')}</span>
        </div></a>`;
      topGrid.appendChild(div);
    });
  }

  const yr = document.getElementById('year');
  if (yr) yr.textContent = new Date().getFullYear();
});
