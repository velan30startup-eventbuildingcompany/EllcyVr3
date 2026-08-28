/* Connects the legacy Catering Boys pages to Admin-managed service data. */
(function () {
  'use strict';

  var body = document.body;
  var serviceSlug = body.getAttribute('data-admin-service');
  if (!serviceSlug) return;

  function appRoot() {
    var marker = window.location.pathname.indexOf('/services/');
    return marker >= 0 ? window.location.pathname.slice(0, marker + 1) : '/';
  }

  function formatPrice(value) {
    return Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
  }

  function sortedMedia(service) {
    return (service.images || []).slice().sort(function (a, b) {
      return Number(b.is_primary || 0) - Number(a.is_primary || 0) ||
        Number(a.sort_order || 0) - Number(b.sort_order || 0);
    });
  }

  function youtubeId(url) {
    var match = String(url || '').match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([^?&#/]+)/i);
    return match ? match[1] : '';
  }

  function vimeoId(url) {
    var match = String(url || '').match(/vimeo\.com\/(?:video\/)?(\d+)/i);
    return match ? match[1] : '';
  }

  function replaceMedia(node, media, fallback, alt) {
    if (!node) return;
    var path = media && media.path ? media.path : fallback;
    var type = media && media.media_type === 'video' ? 'video' : 'image';
    var replacement;

    if (type === 'video' && media.video_provider) {
      var videoId = media.video_provider === 'youtube' ? youtubeId(path) : vimeoId(path);
      if (videoId) {
        replacement = document.createElement('iframe');
        replacement.src = media.video_provider === 'youtube'
          ? 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(videoId)
          : 'https://player.vimeo.com/video/' + encodeURIComponent(videoId);
        replacement.title = alt;
        replacement.loading = 'lazy';
        replacement.allow = 'accelerometer; autoplay; encrypted-media; picture-in-picture';
        replacement.allowFullscreen = true;
      }
    } else if (type === 'video') {
      replacement = document.createElement('video');
      replacement.src = path;
      replacement.controls = true;
      replacement.preload = 'metadata';
      replacement.playsInline = true;
      if (media.thumbnail) replacement.poster = media.thumbnail;
      replacement.setAttribute('aria-label', alt);
    }

    if (!replacement) {
      replacement = document.createElement('img');
      replacement.src = path || fallback;
      replacement.alt = alt;
      replacement.loading = node.classList.contains('bnc-mosaic-main') ? 'eager' : 'lazy';
    }

    replacement.className = node.className + ' catering-admin-media';
    node.replaceWith(replacement);
  }

  function applyListing(service) {
    var media = sortedMedia(service)[0] || null;
    var preview = media && media.media_type === 'image'
      ? media.path
      : ((media && media.thumbnail) || service.image);
    document.querySelectorAll('.style-card img').forEach(function (img) {
      if (preview) img.src = preview;
      img.classList.add('style-card-media');
    });
  }

  function applyDetail(service) {
    var media = sortedMedia(service);
    var primary = media[0] || null;
    var images = media.filter(function (item) { return item.media_type !== 'video'; });
    var packageKey = body.getAttribute('data-admin-package') || '';
    var selectedPackage = (service.packages || []).find(function (item) {
      return item.pkg_key === packageKey || item.slug === packageKey;
    }) || null;
    var rate = Number(selectedPackage && selectedPackage.price ? selectedPackage.price : service.price || 0);

    if (rate > 0) {
      body.setAttribute('data-rate', String(rate));
      document.querySelectorAll('.bnc-rate-price').forEach(function (node) {
        node.textContent = '₹' + formatPrice(rate);
      });
      document.querySelectorAll('.bnc-rate-tag').forEach(function (node) {
        node.innerHTML = '₹' + formatPrice(rate) + ' <span>/ staff member</span>';
      });
    }

    var description = (selectedPackage && selectedPackage.description) || service.description || service.short_description;
    var overview = document.querySelector('#tabOverview p:first-child');
    if (overview && description) overview.textContent = description;
    var pricingCopy = document.querySelector('#tabOverview p:nth-child(2)');
    if (pricingCopy && rate > 0) {
      pricingCopy.textContent = 'Priced at ₹' + formatPrice(rate) + ' per required catering staff member. The staff count is calculated from your guest count and dish count.';
    }

    var alt = service.title || 'Catering Boys service';
    replaceMedia(document.querySelector('.bnc-mosaic-main'), primary, service.image, alt);
    replaceMedia(document.querySelector('.bnc-mobile-hero img, .bnc-mobile-hero video, .bnc-mobile-hero iframe'), primary, service.image, alt);
    replaceMedia(document.querySelector('.bnc-mosaic-t1'), images[0] || null, service.image, alt);
    replaceMedia(document.querySelector('.bnc-mosaic-t2'), images[1] || images[0] || null, service.image, alt);

    document.dispatchEvent(new CustomEvent('ellcy:catering-admin-data', {
      detail: { service: service, package: selectedPackage, rate: rate }
    }));
  }

  fetch(appRoot() + 'api/services/' + encodeURIComponent(serviceSlug), {
    credentials: 'same-origin',
    cache: 'no-store',
    headers: { Accept: 'application/json' }
  }).then(function (response) {
    if (!response.ok) throw new Error('Service data is unavailable.');
    return response.json();
  }).then(function (payload) {
    if (!payload || !payload.service) return;
    if (body.getAttribute('data-admin-view') === 'listing') applyListing(payload.service);
    else applyDetail(payload.service);
  }).catch(function (error) {
    console.warn('ELLCY catering admin data:', error.message);
  });
})();
