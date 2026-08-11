<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>ELLCY | Login</title>
  <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE) ?>/css/style.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
  <style>
    body{background:#f4e9ff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;font-family:'Segoe UI',system-ui,sans-serif;margin:0}
    .auth-card{background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(106,27,154,.15);padding:44px 36px;width:100%;max-width:400px}
    .auth-logo{font-size:1.9rem;font-weight:900;color:#6a1b9a;letter-spacing:-.04em;text-align:center;margin-bottom:4px;text-decoration:none;display:block}
    .auth-sub{text-align:center;color:#888;font-size:.85rem;margin-bottom:32px}
    .auth-field{margin-bottom:16px}
    .auth-label{display:block;font-weight:700;font-size:.8rem;color:#1a1a2e;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    .auth-input{width:100%;padding:12px 14px;border:1.5px solid #e0d5f0;border-radius:10px;background:#fafafa;font-size:.94rem;font-family:inherit;outline:none;transition:border-color .18s,box-shadow .18s}
    .auth-input:focus{border-color:#6a1b9a;box-shadow:0 0 0 3px rgba(106,27,154,.1);background:#fff}
    .auth-btn{width:100%;padding:13px;background:#6a1b9a;color:#fff;border:none;border-radius:10px;font-family:inherit;font-size:1rem;font-weight:800;cursor:pointer;margin-top:6px;box-shadow:0 4px 16px rgba(106,27,154,.3)}
    .auth-btn:hover{background:#5c1690}
    .auth-error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:18px}
    .auth-footer{text-align:center;margin-top:22px;font-size:.85rem;color:#555}
    .auth-footer a{color:#6a1b9a;font-weight:700;text-decoration:none}
    .auth-back{display:block;text-align:center;margin-top:14px;font-size:.8rem;color:#999;text-decoration:none}

    /* ── Login method tabs ─────────────────────────────────── */
    .auth-tabs{display:flex;border:1.5px solid #e0d5f0;border-radius:10px;overflow:hidden;margin-bottom:24px}
    .auth-tab{flex:1;padding:10px 8px;text-align:center;font-size:.82rem;font-weight:700;color:#888;background:#fafafa;border:none;cursor:pointer;font-family:inherit}
    .auth-tab.active{background:#6a1b9a;color:#fff}
    .auth-panel{display:none}
    .auth-panel.active{display:block}

    /* ── Phone/OTP flow ─────────────────────────────────────── */
    .otp-msg{font-size:.82rem;border-radius:8px;padding:9px 12px;margin-bottom:14px;display:none}
    .otp-msg.show{display:block}
    .otp-msg.error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}
    .otp-msg.info{background:#eef4ff;border:1px solid #bcd4ff;color:#1e429f}
    .otp-msg.info a, .otp-msg.error a{color:inherit;font-weight:800;text-decoration:underline}
    .otp-resend{text-align:center;margin-top:14px;font-size:.82rem;color:#888}
    .otp-resend button{background:none;border:none;color:#6a1b9a;font-weight:800;cursor:pointer;font-family:inherit;font-size:.82rem;padding:0}
    .otp-resend button:disabled{color:#bbb;cursor:not-allowed}
    .auth-input.otp-code{letter-spacing:.5em;text-align:center;font-size:1.15rem;font-weight:700}
    .otp-change-number{display:block;text-align:center;margin-top:10px;font-size:.8rem;color:#999;text-decoration:none;background:none;border:none;cursor:pointer;font-family:inherit}

    @media (max-width:480px){
      body{align-items:flex-start;padding:12px}
      .auth-card{margin-top:16px;padding:30px 20px;border-radius:16px}
      .auth-sub{margin-bottom:24px}
      .auth-tabs{margin-bottom:20px}
      .auth-tab{padding:10px 5px;font-size:.76rem}
    }
  </style>
</head>
<body>
<div class="auth-card">
  <a class="auth-logo" href="<?= htmlspecialchars(APP_BASE) ?>/">ELLCY</a>
  <div class="auth-sub">Log in to complete your booking</div>

  <?php if (!empty($error)): ?>
  <div class="auth-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="auth-tabs" role="tablist">
    <button type="button" class="auth-tab active" id="tabEmailBtn" role="tab" aria-selected="true">Email &amp; Password</button>
    <button type="button" class="auth-tab" id="tabPhoneBtn" role="tab" aria-selected="false">Phone (OTP)</button>
  </div>

  <!-- ── Email & Password panel ─────────────────────────────── -->
  <div class="auth-panel active" id="panelEmail" role="tabpanel">
    <form method="POST" action="">
      <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
      <input type="hidden" name="return_to" value="<?= htmlspecialchars($return_to) ?>">
      <div class="auth-field">
        <label class="auth-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="auth-input" required autocomplete="email"/>
      </div>
      <div class="auth-field">
        <label class="auth-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="auth-input" required autocomplete="current-password"/>
        <div style="text-align:right;margin-top:6px;">
          <a href="<?= htmlspecialchars(APP_BASE) ?>/forgot-password" style="font-size:.8rem;color:#6a1b9a;font-weight:700;text-decoration:none;">Forgot password?</a>
        </div>
      </div>
      <button type="submit" class="auth-btn"><i class="fa-solid fa-right-to-bracket"></i> Log In</button>
    </form>
  </div>

  <!-- ── Phone (OTP) panel ──────────────────────────────────── -->
  <div class="auth-panel" id="panelPhone" role="tabpanel">
    <div class="otp-msg" id="otpMsg"></div>

    <!-- Step 1: enter phone number -->
    <div id="otpStepPhone">
      <div class="auth-field">
        <label class="auth-label" for="otpPhone">Phone Number</label>
        <input type="tel" id="otpPhone" class="auth-input" placeholder="10-digit mobile number" required autocomplete="tel" inputmode="numeric" maxlength="10"/>
      </div>
      <button type="button" class="auth-btn" id="otpSendBtn"><i class="fa-solid fa-comment-sms"></i> Send OTP</button>
    </div>

    <!-- Step 2: enter the 6-digit code (hidden until an OTP is sent) -->
    <div id="otpStepCode" style="display:none">
      <div class="auth-field">
        <label class="auth-label" for="otpCode">6-Digit Code</label>
        <input type="text" id="otpCode" class="auth-input otp-code" placeholder="••••••" required inputmode="numeric" maxlength="6" autocomplete="one-time-code"/>
      </div>
      <button type="button" class="auth-btn" id="otpVerifyBtn"><i class="fa-solid fa-shield-check"></i> Verify &amp; Log In</button>

      <div class="otp-resend">
        Didn't get the code?
        <button type="button" id="otpResendBtn" disabled>Resend OTP<span id="otpResendTimer"></span></button>
      </div>
      <button type="button" class="otp-change-number" id="otpChangeNumber">
        <i class="fa-solid fa-arrow-left"></i> Use a different number
      </button>
    </div>
  </div>

  <div class="auth-footer">
    New to ELLCY?
    <a href="<?= htmlspecialchars(APP_BASE) ?>/register?return_to=<?= urlencode($return_to) ?>">Create an account</a>
  </div>
  <a class="auth-back" href="<?= htmlspecialchars(APP_BASE) ?>/"><i class="fa-solid fa-arrow-left"></i> Back to browsing</a>
</div>
<script>
(function () {
  'use strict';
  var APP_BASE  = <?= json_encode(APP_BASE) ?>;
  var CSRF      = <?= json_encode(Security::csrfToken()) ?>;
  var RETURN_TO = <?= json_encode($return_to) ?>;

  /* ── Tabs ─────────────────────────────────────────────────── */
  var tabEmailBtn = document.getElementById('tabEmailBtn');
  var tabPhoneBtn = document.getElementById('tabPhoneBtn');
  var panelEmail  = document.getElementById('panelEmail');
  var panelPhone  = document.getElementById('panelPhone');

  function showTab(which) {
    var isEmail = which === 'email';
    tabEmailBtn.classList.toggle('active', isEmail);
    tabPhoneBtn.classList.toggle('active', !isEmail);
    tabEmailBtn.setAttribute('aria-selected', isEmail ? 'true' : 'false');
    tabPhoneBtn.setAttribute('aria-selected', isEmail ? 'false' : 'true');
    panelEmail.classList.toggle('active', isEmail);
    panelPhone.classList.toggle('active', !isEmail);
  }
  tabEmailBtn.addEventListener('click', function () { showTab('email'); });
  tabPhoneBtn.addEventListener('click', function () { showTab('phone'); });

  /* ── Phone/OTP flow ───────────────────────────────────────── */
  var otpMsg        = document.getElementById('otpMsg');
  var stepPhone      = document.getElementById('otpStepPhone');
  var stepCode        = document.getElementById('otpStepCode');
  var phoneInput      = document.getElementById('otpPhone');
  var codeInput        = document.getElementById('otpCode');
  var sendBtn      = document.getElementById('otpSendBtn');
  var verifyBtn    = document.getElementById('otpVerifyBtn');
  var resendBtn    = document.getElementById('otpResendBtn');
  var resendTimerEl = document.getElementById('otpResendTimer');
  var changeNumberBtn = document.getElementById('otpChangeNumber');

  var resendTimer = null;
  var RESEND_COOLDOWN = 30; // seconds — mirrors OtpLogin::RESEND_COOLDOWN server-side

  function showMsg(text, type) {
    otpMsg.innerHTML = text;
    otpMsg.className = 'otp-msg show ' + (type || 'error');
  }
  function hideMsg() {
    otpMsg.className = 'otp-msg';
    otpMsg.innerHTML = '';
  }

  function startResendCooldown(seconds) {
    resendBtn.disabled = true;
    var remaining = seconds || RESEND_COOLDOWN;
    clearInterval(resendTimer);
    function tick() {
      resendTimerEl.textContent = remaining > 0 ? ' (' + remaining + 's)' : '';
      if (remaining <= 0) {
        clearInterval(resendTimer);
        resendBtn.disabled = false;
        resendTimerEl.textContent = '';
      }
      remaining--;
    }
    tick();
    resendTimer = setInterval(tick, 1000);
  }

  function post(url, body) {
    return fetch(APP_BASE + url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body,
      credentials: 'same-origin',
    }).then(function (r) { return r.json(); });
  }

  function sendOtp() {
    var phone = phoneInput.value.trim();
    if (!/^\d{10}$/.test(phone.replace(/\D/g, '').slice(-10))) {
      showMsg('Please enter a valid 10-digit mobile number.', 'error');
      return;
    }
    sendBtn.disabled = true;
    sendBtn.textContent = 'Sending…';
    hideMsg();

    post('/api/auth/otp/send', 'csrf_token=' + encodeURIComponent(CSRF) + '&phone=' + encodeURIComponent(phone))
      .then(function (data) {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fa-solid fa-comment-sms"></i> Send OTP';

        if (!data.ok) {
          if (data.noAccount) {
            showMsg(data.error + ' <a href="' + APP_BASE + '/register?return_to=' + encodeURIComponent(RETURN_TO) + '">Create an account</a>', 'error');
          } else {
            showMsg(data.error || 'Something went wrong. Please try again.', 'error');
            if (typeof data.retryAfter === 'number' && data.retryAfter > 0) {
              stepPhone.style.display = 'none';
              stepCode.style.display = '';
              startResendCooldown(data.retryAfter);
            }
          }
          return;
        }

        var msg = data.message || 'A 6-digit code has been sent to your phone.';
        if (data.devOtp) {
          // Only ever present while APP_DEBUG is true (see AuthController::sendPhoneOtp) —
          // no real SMS gateway is configured yet, so this is how you test the flow locally.
          msg += '<br><strong style="letter-spacing:2px">DEV MODE — code: ' + data.devOtp + '</strong>';
          codeInput.value = data.devOtp;
        }
        showMsg(msg, 'info');
        stepPhone.style.display = 'none';
        stepCode.style.display = '';
        codeInput.value = '';
        codeInput.focus();
        startResendCooldown(RESEND_COOLDOWN);
      })
      .catch(function () {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fa-solid fa-comment-sms"></i> Send OTP';
        showMsg('Network error. Please try again.', 'error');
      });
  }

  function verifyOtp() {
    var phone = phoneInput.value.trim();
    var code  = codeInput.value.trim();
    if (!/^\d{6}$/.test(code)) {
      showMsg('Please enter the 6-digit code.', 'error');
      return;
    }
    verifyBtn.disabled = true;
    verifyBtn.textContent = 'Verifying…';

    post('/api/auth/otp/verify', 'csrf_token=' + encodeURIComponent(CSRF) + '&phone=' + encodeURIComponent(phone) + '&otp=' + encodeURIComponent(code) + '&return_to=' + encodeURIComponent(RETURN_TO))
      .then(function (data) {
        verifyBtn.disabled = false;
        verifyBtn.innerHTML = '<i class="fa-solid fa-shield-check"></i> Verify &amp; Log In';

        if (!data.ok) {
          if (data.noAccount) {
            showMsg(data.error + ' <a href="' + APP_BASE + '/register?return_to=' + encodeURIComponent(RETURN_TO) + '">Create an account</a>', 'error');
            return;
          }
          showMsg(data.error || 'Incorrect code.', 'error');
          if (data.showResend) {
            // Locked out or expired — the code is dead, force a resend
            // rather than letting them keep guessing against it.
            resendBtn.disabled = false;
            resendTimerEl.textContent = '';
            clearInterval(resendTimer);
          }
          return;
        }

        showMsg('Logged in — redirecting…', 'info');
        window.location.href = data.redirect || (APP_BASE + '/');
      })
      .catch(function () {
        verifyBtn.disabled = false;
        verifyBtn.innerHTML = '<i class="fa-solid fa-shield-check"></i> Verify &amp; Log In';
        showMsg('Network error. Please try again.', 'error');
      });
  }

  sendBtn.addEventListener('click', sendOtp);
  verifyBtn.addEventListener('click', verifyOtp);
  resendBtn.addEventListener('click', function () {
    hideMsg();
    sendOtp();
  });
  changeNumberBtn.addEventListener('click', function () {
    clearInterval(resendTimer);
    stepCode.style.display = 'none';
    stepPhone.style.display = '';
    hideMsg();
    phoneInput.focus();
  });
  codeInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') verifyOtp();
  });
  phoneInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') sendOtp();
  });
})();
</script>
</body>
</html>
