(function(){
  'use strict';
  var DJ_SURCHARGE=9999, includeDj=false;
  function money(n){return '₹'+Number(n).toLocaleString('en-IN');}
  function basePrice(){var el=document.querySelector('.cm-price-val');if(!el)return 0;var n=parseInt((el.dataset.ellcyBase||el.textContent).replace(/[^0-9]/g,''),10)||0;el.dataset.ellcyBase=String(n);return n;}
  function refresh(){
    var base=basePrice();
    document.querySelectorAll('.cm-price-val').forEach(function(el){if(!el.dataset.ellcyBase)el.dataset.ellcyBase=String(parseInt(el.textContent.replace(/[^0-9]/g,''),10)||base);el.textContent=money(Number(el.dataset.ellcyBase)+(includeDj?DJ_SURCHARGE:0));});
    document.querySelectorAll('.ellcy-dj-filter__button').forEach(function(btn){var active=(btn.dataset.dj==='include')===includeDj;btn.classList.toggle('is-active',active);btn.setAttribute('aria-pressed',String(active));});
  }
  function build(){var box=document.createElement('div');box.className='ellcy-dj-filter';box.innerHTML='<span class="ellcy-dj-filter__label">Music setup</span><div class="ellcy-dj-filter__options"><button type="button" class="ellcy-dj-filter__button is-active" data-dj="without" aria-pressed="true">Without DJ</button><button type="button" class="ellcy-dj-filter__button" data-dj="include" aria-pressed="false">Include DJ (+₹9,999)</button></div>';box.addEventListener('click',function(e){var b=e.target.closest('[data-dj]');if(!b)return;includeDj=b.dataset.dj==='include';refresh();});return box;}
  document.querySelectorAll('.cm-desktop-price-block,.cm-price-block').forEach(function(price){var host=price.parentNode;if(!host||host.querySelector('.ellcy-dj-filter'))return;host.insertBefore(build(),price);});
  if(window.EllcyCart&&typeof window.EllcyCart.add==='function'){var original=window.EllcyCart.add.bind(window.EllcyCart);window.EllcyCart.add=function(item){if(includeDj&&item&&String(item.slug||'').indexOf('dancer')!==-1){item=Object.assign({},item,{uid:String(item.uid||item.id)+'-with-dj',id:String(item.id||item.uid)+'-with-dj',price:Number(item.price||0)+DJ_SURCHARGE,package:String(item.package||'Dance package')+' + DJ',title:String(item.title||'Dance package')+' + DJ'});}return original(item);};}
  refresh();
})();
