<?php
$page_title       = 'ELLCY | Complete Your Booking';
$meta_description = 'Complete your event service booking with ELLCY.';
$extra_css        = ['header2.css','booking.css'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    Security::requireCsrf();
    if (!Security::checkRateLimit('booking', Security::getIp())) {
        echo json_encode(['success'=>false,'message'=>'Too many requests.']); exit;
    }
    $name      = Security::sanitizeString($_POST['name'] ?? '', 100);
    $email     = Security::sanitizeEmail($_POST['email'] ?? '') ?: '';
    $phone     = Security::sanitizePhone($_POST['phone'] ?? '');
    $eventType = Security::sanitizeString($_POST['event_type'] ?? '', 100);
    $eventDate = $_POST['event_date'] ?? '';
    $eventVenue= Security::sanitizeString($_POST['venue'] ?? '', 300);
    $eventTime = Security::sanitizeString($_POST['event_time'] ?? '', 50);
    $guests    = Security::sanitizeInt($_POST['guest_count'] ?? 0, 0, 100000);
    $note      = Security::sanitizeString($_POST['note'] ?? '', 500);
    $itemsJson = $_POST['items_json'] ?? '[]';

    if (!$name || !Security::validatePhone($phone)) {
        echo json_encode(['success'=>false,'message'=>'Please fill all required fields correctly.']); exit;
    }

    $items    = json_decode($itemsJson, true) ?: [];
    $subtotal = array_sum(array_map(fn($i) => ($i['price']??0)*($i['qty']??1), $items));

    $id  = Order::create([
        'name'        => $name,
        'email'       => $email,
        'phone'       => '+91'.preg_replace('/[^0-9]/','',$phone),
        'event_type'  => $eventType,
        'event_date'  => $eventDate ?: null,
        'event_venue' => $eventVenue,
        'event_time'  => $eventTime,
        'guest_count' => $guests ?: null,
        'items'       => $items,
        'subtotal'    => $subtotal,
        'total'       => $subtotal,
        'note'        => $note,
    ]);

    $ref = Database::fetchOne('SELECT order_ref FROM orders WHERE id=?',[$id])['order_ref'] ?? '';
    echo json_encode(['success'=>true,'order_ref'=>$ref,'message'=>'Booking confirmed!']); exit;
}

require VIEWS_PATH . '/layouts/header.php';
?>

<header class="header new-header" role="banner">
  <button class="hdr-back-btn" onclick="_ellcySmartBack('<?= APP_URL ?>/cart')" aria-label="Go back">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i><span>Back</span>
  </button>
  <a class="logo" href="<?= APP_URL ?>/" aria-label="ELLCY Home">ELLCY</a>
  <a href="<?= APP_URL ?>/cart" class="cart-header-btn hdr-cart-right" aria-label="View cart">
    <i class="fa-solid fa-cart-shopping"></i>
    <span class="cart-badge" style="display:none">0</span>
  </a>
</header>

<div class="bk-page">
  <div class="bk-steps">
    <div class="bk-step active"><span class="bk-step-num">1</span> Your Cart</div>
    <div class="bk-step-line"></div>
    <div class="bk-step active"><span class="bk-step-num">2</span> Your Details</div>
    <div class="bk-step-line"></div>
    <div class="bk-step"><span class="bk-step-num">3</span> Confirmation</div>
  </div>

  <div class="bk-layout">
    <div class="bk-form-col">
      <div class="bk-section-card">
        <h2 class="bk-section-title">Event Details</h2>
        <form id="bkForm" novalidate>
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          <input type="hidden" id="bkItemsJson" name="items_json" value="">

          <div class="bk-row-2">
            <div class="bk-field">
              <label class="bk-label" for="bkName">Your Name <span class="bk-req">*</span></label>
              <input type="text" id="bkName" name="name" class="bk-input" placeholder="Full name" required/>
            </div>
            <div class="bk-field">
              <label class="bk-label" for="bkPhone">Mobile Number <span class="bk-req">*</span></label>
              <input type="tel" id="bkPhone" name="phone" class="bk-input" placeholder="98765 43210"
                     maxlength="10" inputmode="numeric" required/>
            </div>
          </div>
          <div class="bk-field">
            <label class="bk-label" for="bkEmail">Email Address <span style="font-weight:500;color:#888;font-size:.8rem">(optional)</span></label>
            <input type="email" id="bkEmail" name="email" class="bk-input" placeholder="your@email.com"/>
          </div>
          <div class="bk-row-2">
            <div class="bk-field">
              <label class="bk-label" for="bkEventType">Event Type <span class="bk-req">*</span></label>
              <select id="bkEventType" name="event_type" class="bk-select" required>
                <option value="">Select…</option>
                <option>Wedding</option>
                <option>Reception</option>
                <option>Birthday</option>
                <option>Engagement</option>
                <option>Corporate Event</option>
                <option>College Event</option>
                <option>House Warming</option>
                <option>Other</option>
              </select>
            </div>
            <div class="bk-field">
              <label class="bk-label" for="bkDate">Event Date</label>
              <input type="date" id="bkDate" name="event_date" class="bk-input"/>
            </div>
          </div>
          <div class="bk-row-2">
            <div class="bk-field">
              <label class="bk-label" for="bkVenue">Venue / Location</label>
              <input type="text" id="bkVenue" name="venue" class="bk-input" placeholder="Hall name, area…"/>
            </div>
            <div class="bk-field">
              <label class="bk-label" for="bkTime">Preferred Time</label>
              <select id="bkTime" name="event_time" class="bk-select">
                <option value="">Any time</option>
                <option>Morning (6 AM – 12 PM)</option>
                <option>Afternoon (12 PM – 4 PM)</option>
                <option>Evening (4 PM – 9 PM)</option>
                <option>Night (9 PM onwards)</option>
              </select>
            </div>
          </div>
          <div class="bk-field">
            <label class="bk-label" for="bkNote">Special Instructions <span style="font-weight:500;color:#888;font-size:.8rem">(optional)</span></label>
            <textarea id="bkNote" name="note" class="bk-textarea" maxlength="500"
                      placeholder="Any special requirements, themes or notes for our team…"></textarea>
          </div>

          <div id="bkErrorMsg" style="display:none;color:#e53e3e;font-size:.85rem;margin-bottom:12px;font-weight:600"></div>
          <button type="submit" class="bk-submit-btn" id="bkSubmitBtn">
            <i class="fa-solid fa-paper-plane"></i> Confirm Booking
          </button>
          <p class="bk-note">Our team will call you within <strong>2 hours</strong> to confirm your booking details and pricing.</p>
        </form>
      </div>
    </div>

    <aside class="bk-summary-col">
      <div class="bk-section-card">
        <h3 class="bk-section-title">Order Summary</h3>
        <div id="bkSummaryItems"></div>
        <div class="bk-summary-divider"></div>
        <div class="bk-summary-total">
          <span>Total</span>
          <span id="bkTotal">₹0</span>
        </div>
        <div class="bk-trust">
          <div class="bk-trust-item"><i class="fa-solid fa-shield-halved"></i> Secure booking</div>
          <div class="bk-trust-item"><i class="fa-solid fa-phone"></i> 2-hour callback</div>
        </div>
      </div>
    </aside>
  </div>
</div>

<!-- Success modal -->
<div id="bkSuccessModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:20px;padding:40px 32px;max-width:420px;width:90%;text-align:center">
    <div style="width:72px;height:72px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:#059669;font-size:2rem">
      <i class="fa-solid fa-circle-check"></i>
    </div>
    <h2 style="font-size:1.3rem;font-weight:800;color:#1a1a2e;margin-bottom:8px">Booking Confirmed!</h2>
    <p style="color:#666;font-size:.9rem;margin-bottom:6px">Your booking reference is:</p>
    <p id="bkOrderRef" style="font-size:1.2rem;font-weight:800;color:#6a1b9a;margin-bottom:16px"></p>
    <p style="color:#666;font-size:.9rem;margin-bottom:28px">Our team will call you within <strong>2 hours</strong> to confirm your slot and pricing.</p>
    <a href="<?= APP_URL ?>/" style="display:inline-flex;align-items:center;gap:8px;background:#6a1b9a;color:#fff;padding:12px 28px;border-radius:10px;font-weight:700;text-decoration:none">
      <i class="fa-solid fa-house"></i> Back to Home
    </a>
  </div>
</div>

<script>
function _ellcySmartBack(u){if(window.history.length>1&&document.referrer&&document.referrer.includes(window.location.hostname)){window.history.back();}else{window.location.href=u;}}

// Pre-fill summary from cart
document.addEventListener('DOMContentLoaded',function(){
  var items=[]; try{items=JSON.parse(localStorage.getItem('ellcy_cart')||'[]');}catch(e){}
  document.getElementById('bkItemsJson').value=JSON.stringify(items);
  var box=document.getElementById('bkSummaryItems');
  var total=0;
  if(!items.length){box.innerHTML='<p style="color:#888;font-size:.85rem">Your cart is empty. <a href="<?= APP_URL ?>/category?type=wedding">Browse services</a></p>';return;}
  items.forEach(function(i){
    var amt=(i.price||0)*(i.qty||1);
    total+=amt;
    var div=document.createElement('div');
    div.className='bk-summary-row';
    div.innerHTML='<span>'+i.title+(i.qty>1?' × '+i.qty:'')+'</span><span>₹'+amt.toLocaleString('en-IN')+'</span>';
    box.appendChild(div);
  });
  document.getElementById('bkTotal').textContent='₹'+total.toLocaleString('en-IN');
});

document.getElementById('bkPhone').addEventListener('input',function(){this.value=this.value.replace(/\D/g,'').slice(0,10);});

document.getElementById('bkForm').addEventListener('submit',function(e){
  e.preventDefault();
  var err=document.getElementById('bkErrorMsg');
  err.style.display='none';
  var name=document.getElementById('bkName').value.trim();
  var phone=document.getElementById('bkPhone').value.trim();
  var etype=document.getElementById('bkEventType').value;
  if(!name){err.textContent='Please enter your name.';err.style.display='';return;}
  if(!/^[6-9][0-9]{9}$/.test(phone)){err.textContent='Please enter a valid 10-digit mobile number.';err.style.display='';return;}
  if(!etype){err.textContent='Please select the event type.';err.style.display='';return;}

  var btn=document.getElementById('bkSubmitBtn');
  btn.disabled=true;
  btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Processing…';

  var fd=new FormData(this);
  fetch(window.location.href,{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(d){
      if(d.success){
        localStorage.removeItem('ellcy_cart');
        document.getElementById('bkOrderRef').textContent=d.order_ref;
        document.getElementById('bkSuccessModal').style.display='flex';
      } else {
        err.textContent=d.message||'Something went wrong.';
        err.style.display='';
        btn.disabled=false;
        btn.innerHTML='<i class="fa-solid fa-paper-plane"></i> Confirm Booking';
      }
    })
    .catch(function(){
      err.textContent='Network error. Please try again.';
      err.style.display='';
      btn.disabled=false;
      btn.innerHTML='<i class="fa-solid fa-paper-plane"></i> Confirm Booking';
    });
});
</script>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>
