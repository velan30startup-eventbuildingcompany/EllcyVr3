(function () {
  'use strict';

  function localIso(date) {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
  }

  var minimum = new Date();
  minimum.setHours(0, 0, 0, 0);
  minimum.setDate(minimum.getDate() + 2);
  var minimumValue = localIso(minimum);
  var minimumLabel = minimum.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });

  function validate(input) {
    if (!input.value || input.value >= minimumValue) {
      input.setCustomValidity('');
      return true;
    }
    input.setCustomValidity('Choose an event date from ' + minimumLabel + ' onwards.');
    return false;
  }

  function applyPolicy(root) {
    (root || document).querySelectorAll('input[type="date"]').forEach(function (input) {
      input.min = minimumValue;
      if (input.dataset.ellcyDatePolicy === 'ready') return;
      input.dataset.ellcyDatePolicy = 'ready';
      input.addEventListener('input', function () { validate(input); });
      input.addEventListener('change', function () { validate(input); });
    });
  }

  document.addEventListener('DOMContentLoaded', function () { applyPolicy(document); });
  document.addEventListener('submit', function (event) {
    applyPolicy(event.target);
    var invalid = Array.from(event.target.querySelectorAll('input[type="date"]')).find(function (input) {
      return !validate(input);
    });
    if (invalid) {
      event.preventDefault();
      event.stopImmediatePropagation();
      invalid.reportValidity();
      invalid.focus();
    }
  }, true);
})();
