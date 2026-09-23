(function () {
  'use strict';
  var params = new URLSearchParams(window.location.search);
  var type = params.get('type');
  var allowedTypes = ['bridal', 'groom'];
  if (!allowedTypes.includes(type)) type = 'bridal';

  var isBridal = type === 'bridal';
  var title = (isBridal ? 'Bridal' : 'Groom') + ' Make Over Brochure';
  document.title = 'ELLCY | ' + title;
  document.getElementById('brochureTitle').textContent = title;
  document.getElementById('brochureIntro').textContent = isBridal
    ? 'Discover our consultation-led bridal styling journey for weddings, receptions and traditional Tamil ceremonies.'
    : 'Discover our consultation-led groom styling journey for weddings, receptions and traditional Tamil ceremonies.';
  var inclusions = isBridal
    ? ['Look consultation matched to your outfit and jewellery', 'Skin preparation, professional makeup and hair styling', 'Saree or outfit draping with finishing support', 'Event-ready touch-up guidance for a confident, lasting look']
    : ['Look consultation matched to your outfit and celebration', 'Skin preparation with professional grooming', 'Hair and beard styling for a camera-ready finish', 'Event-day finishing support for a polished, lasting look'];
  document.getElementById('coverTitle').textContent = isBridal ? 'Bridal Make Over' : 'Groom Make Over';
  document.getElementById('coverSubtitle').textContent = isBridal ? 'A timeless look for every Tamil wedding moment.' : 'A refined look for every Tamil wedding moment.';
  document.getElementById('experienceTitle').textContent = isBridal ? 'The Bridal Experience' : 'The Groom Experience';
  document.getElementById('experienceCopy').textContent = isBridal
    ? 'From Muhurtham to reception, your look is planned around your features, attire, jewellery and photography lighting.'
    : 'From ceremony to reception, your look is planned around your features, attire, venue and photography lighting.';
  var list = document.getElementById('brochureInclusions');
  inclusions.forEach(function (item) { var li = document.createElement('li'); li.textContent = item; list.appendChild(li); });
  document.getElementById('trialLink').href = '../request-for-call?service=' + encodeURIComponent('make-over-' + type);
  document.getElementById('year').textContent = new Date().getFullYear();
})();
