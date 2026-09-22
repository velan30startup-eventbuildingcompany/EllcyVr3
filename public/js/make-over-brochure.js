(function () {
  'use strict';
  var params = new URLSearchParams(window.location.search);
  var type = params.get('type');
  var tier = params.get('tier');
  var allowedTypes = ['bridal', 'groom'];
  var allowedTiers = ['silver', 'gold', 'platinum', 'diamond'];
  if (!allowedTypes.includes(type)) type = 'bridal';
  if (!allowedTiers.includes(tier)) tier = 'silver';

  var isBridal = type === 'bridal';
  var title = (isBridal ? 'Bridal' : 'Groom') + ' Make Over Brochure';
  document.title = 'ELLCY | ' + title;
  document.getElementById('brochureTitle').textContent = title;
  document.getElementById('brochureIntro').textContent = isBridal
    ? 'Compare bridal make over standards, from essential event styling to a complete luxury transformation.'
    : 'Compare groom make over standards, from essential grooming to a complete luxury event-ready finish.';

  var prices = isBridal ? [12000, 18000, 25000, 35000] : [6000, 9000, 13000, 18000];
  var inclusions = isBridal ? [
    ['Professional makeup','Hair styling','Saree draping'],
    ['HD makeup','Enhanced hair styling','Draping and touch-up'],
    ['Premium skin preparation','Advanced makeup and hair','Accessories coordination'],
    ['Trial consultation','Luxury makeup and styling','Extended touch-up support']
  ] : [
    ['Professional grooming','Hair styling','Event-ready finish'],
    ['Skin preparation','Hair and beard styling','Camera-ready finish'],
    ['Personal consultation','Premium groom styling','Long-wear finish'],
    ['Trial consultation','Luxury groom styling','Extended touch-up support']
  ];
  var names = ['Silver','Gold','Platinum','Diamond'];
  var grid = document.getElementById('standardsGrid');
  names.forEach(function (name, index) {
    var key = name.toLowerCase();
    var article = document.createElement('article');
    article.className = 'standard-card' + (tier === key ? ' selected' : '');
    if (tier === key) {
      var badge = document.createElement('span');
      badge.className = 'selected-label';
      badge.textContent = 'Selected';
      article.appendChild(badge);
    }
    var heading = document.createElement('h3');
    heading.textContent = name + ' Standard';
    var price = document.createElement('p');
    price.className = 'standard-price';
    price.textContent = '₹' + prices[index].toLocaleString('en-IN');
    var list = document.createElement('ul');
    inclusions[index].forEach(function (item) {
      var li = document.createElement('li'); li.textContent = item; list.appendChild(li);
    });
    var link = document.createElement('a');
    link.className = 'standard-link';
    link.href = '../services/make-over/' + type + '/' + key + '/';
    link.textContent = 'View ' + name + ' details →';
    article.appendChild(heading); article.appendChild(price); article.appendChild(list); article.appendChild(link);
    grid.appendChild(article);
  });
  document.getElementById('trialLink').href = '../request-for-call?service=' + encodeURIComponent('make-over-' + type + '-' + tier);
  document.getElementById('year').textContent = new Date().getFullYear();
})();
