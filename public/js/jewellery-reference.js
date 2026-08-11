/* Optional jewellery design/colour reference upload. */
(function () {
  'use strict';

  function appRoot() {
    var marker = window.location.pathname.indexOf('/services/');
    return marker >= 0 ? window.location.pathname.slice(0, marker + 1) : '/';
  }

  function init() {
    var input = document.getElementById('jewelleryReferenceInput');
    var preview = document.getElementById('jewelleryReferencePreview');
    var status = document.getElementById('jewelleryReferenceStatus');
    if (!input || !preview || !status) return;

    var service = window.ELLCY_JEWELLERY_SERVICE || '';
    var storageKey = 'ellcy_jewellery_reference_' + service;
    var token = '';
    try { token = localStorage.getItem(storageKey) || ''; } catch (ignore) {}
    if (/^[a-f0-9]{64}$/.test(token)) window.ELLCY_JEWELLERY_REFERENCE_TOKEN = token;

    function showStatus(message, kind) {
      status.textContent = message;
      status.className = 'jewellery-reference-status' + (kind ? ' is-' + kind : '');
    }

    function updateCallLinks() {
      if (!window.ELLCY_JEWELLERY_REFERENCE_TOKEN) return;
      document.querySelectorAll('.sd-btn-call').forEach(function (link) {
        var url = new URL(link.href, window.location.href);
        url.searchParams.set('reference_token', window.ELLCY_JEWELLERY_REFERENCE_TOKEN);
        link.href = url.toString();
      });
    }

    function renderPreview(url) {
      preview.hidden = false;
      preview.innerHTML = '';
      var image = document.createElement('img');
      image.src = url;
      image.alt = 'Selected jewellery reference';
      var remove = document.createElement('button');
      remove.type = 'button';
      remove.textContent = 'Remove image';
      remove.addEventListener('click', removeUpload);
      preview.appendChild(image);
      preview.appendChild(remove);
    }

    function csrfToken() {
      return fetch(appRoot() + 'api/csrf', { headers: { Accept: 'application/json' } })
        .then(function (response) { return response.json(); })
        .then(function (data) { return data.csrf_token || ''; });
    }

    function removeUpload() {
      var current = window.ELLCY_JEWELLERY_REFERENCE_TOKEN || '';
      preview.hidden = true;
      preview.innerHTML = '';
      input.value = '';
      window.ELLCY_JEWELLERY_REFERENCE_TOKEN = '';
      try { localStorage.removeItem(storageKey); } catch (ignore) {}
      showStatus('Reference removed. You can continue without an image.', 'success');
      if (!current) return;
      csrfToken().then(function (csrf) {
        return fetch(appRoot() + 'api/uploads/jewellery-reference/remove', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'csrf_token=' + encodeURIComponent(csrf) + '&token=' + encodeURIComponent(current)
        });
      }).catch(function () {});
    }

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) return;
      if (!/^image\/(?:jpeg|png|webp)$/.test(file.type)) {
        input.value = '';
        showStatus('Choose a JPG, PNG or WebP image.', 'error');
        return;
      }
      if (file.size > 6 * 1024 * 1024) {
        input.value = '';
        showStatus('The image must be smaller than 6 MB.', 'error');
        return;
      }

      showStatus('Uploading your optional reference…', '');
      csrfToken().then(function (csrf) {
        var body = new FormData();
        body.append('csrf_token', csrf);
        body.append('service_slug', service);
        body.append('reference_image', file);
        return fetch(appRoot() + 'api/uploads/jewellery-reference', { method: 'POST', body: body });
      }).then(function (response) {
        return response.json().then(function (data) { return { ok: response.ok, data: data }; });
      }).then(function (result) {
        if (!result.ok || !result.data.success) throw new Error(result.data.message || 'Upload failed.');
        window.ELLCY_JEWELLERY_REFERENCE_TOKEN = result.data.token;
        try { localStorage.setItem(storageKey, result.data.token); } catch (ignore) {}
        renderPreview(result.data.preview_url);
        updateCallLinks();
        showStatus('Reference added. Our team will use it as an optional matching guide.', 'success');
      }).catch(function (error) {
        input.value = '';
        showStatus(error.message || 'The image could not be uploaded. Please try again.', 'error');
      });
    });

    updateCallLinks();
  }

  document.addEventListener('DOMContentLoaded', init);
})();
