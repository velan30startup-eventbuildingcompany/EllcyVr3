/* ============================================================
   ELLCY Media Gallery — single hero image or video

   Loads the primary media selected in the PHP admin. When no
   managed media exists, the page's data-eg-fallback image is used.
   ============================================================ */
(function () {
  'use strict';

  function initGallery(el) {
    var category = el.getAttribute('data-eg-category');
    var serviceSlug = el.getAttribute('data-eg-service');
    var fallback = el.getAttribute('data-eg-fallback');
    var preferFallback = el.getAttribute('data-eg-prefer-fallback') === 'true';
    var apiBase = el.getAttribute('data-eg-api-base') || '../../';

    el.innerHTML = '<div class="eg-skeleton" aria-hidden="true"></div>';

    function pickOne(items) {
      if (!items || !items.length) return null;
      return items.find(function (m) { return !!m.is_primary; }) ||
        items.find(function (m) { return m.media_type === 'video'; }) ||
        items[0];
    }

    function render(items) {
      var media = pickOne(items);
      if (!media && fallback) {
        media = { media_type: 'image', path: fallback, alt: 'Service photo', _fallback: true };
      }
      if (!media) {
        el.innerHTML = '<div class="eg-empty"><i class="fa-solid fa-image"></i><span>Photo coming soon</span></div>';
        return;
      }

      var slide = document.createElement('div');
      slide.className = 'eg-slide eg-active-dsk eg-single';

      if (media.media_type === 'video') {
        if (media.video_provider === 'youtube' || media.video_provider === 'vimeo') {
          var iframe = document.createElement('iframe');
          iframe.src = toEmbedUrl(media.path, media.video_provider);
          iframe.title = media.alt || 'Service video';
          iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
          iframe.allowFullscreen = true;
          iframe.loading = 'lazy';
          slide.appendChild(iframe);
        } else {
          var video = document.createElement('video');
          video.src = media._fallback ? media.path : resolveUrl(media.path, apiBase);
          video.controls = true;
          video.playsInline = true;
          video.preload = 'metadata';
          if (media.thumbnail) video.poster = resolveUrl(media.thumbnail, apiBase);
          slide.appendChild(video);
        }
      } else {
        var img = document.createElement('img');
        img.src = media._fallback ? media.path : resolveUrl(media.path, apiBase);
        img.alt = media.alt || 'Service photo';
        img.loading = 'eager';
        slide.appendChild(img);
      }

      var wrap = document.createElement('div');
      wrap.className = 'eg-main-scroll eg-single-wrap';
      wrap.appendChild(slide);
      el.innerHTML = '';
      el.appendChild(wrap);
    }

    if (!category && !serviceSlug) {
      render(null);
      return;
    }

    var endpoint = serviceSlug
      ? apiBase + 'api/services/' + encodeURIComponent(serviceSlug)
      : apiBase + 'api/services?category=' + encodeURIComponent(category);
    fetch(endpoint, {
      headers: { Accept: 'application/json' }
    })
      .then(function (response) { return response.ok ? response.json() : { services: [] }; })
      .then(function (data) {
        if (preferFallback && fallback) {
          render([{ media_type: 'image', path: fallback, alt: el.getAttribute('data-eg-alt') || 'Service photo', _fallback: true }]);
          return;
        }
        var items = [];
        var services = data && data.service ? [data.service] : ((data && data.services) || []);
        services.forEach(function (service) {
          if (Array.isArray(service.images)) items = items.concat(service.images);
        });
        if (!items.length && services.length && services[0].image) {
          items.push({ media_type: 'image', path: services[0].image, alt: services[0].title || 'Service photo' });
        }
        render(items);
      })
      .catch(function () { render(null); });
  }

  function resolveUrl(path, apiBase) {
    if (!path) return '';
    if (/^(?:https?:)?\/\//i.test(path) || /^data:/i.test(path) || /^blob:/i.test(path)) return path;
    /* The PHP API already returns application-root paths such as
       /ellcy/uploads/services/photo.jpg. Keep them untouched. */
    if (path.charAt(0) === '/') return path;
    return apiBase + path;
  }

  function toEmbedUrl(url, provider) {
    if (provider === 'youtube') {
      var match = String(url || '').match(/(?:v=|youtu\.be\/|embed\/)([\w-]{6,})/);
      return 'https://www.youtube.com/embed/' + (match ? match[1] : '');
    }
    if (provider === 'vimeo') {
      var vimeoMatch = String(url || '').match(/vimeo\.com\/(\d+)/);
      return 'https://player.vimeo.com/video/' + (vimeoMatch ? vimeoMatch[1] : '');
    }
    return url;
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.eg-gallery').forEach(initGallery);
  });
})();
