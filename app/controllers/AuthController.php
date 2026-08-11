<?php
/**
 * ELLCY — AuthController
 * Public customer authentication (separate from /admin auth).
 * Guests can browse the entire site freely; only Book Now is gated.
 */
class AuthController {

    public function registerPage(): void {
        if (!empty($_SESSION['user_id'])) { Router::redirect($this->returnTo()); }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            if (!Security::checkRateLimit('register', Security::getIp())) {
                $error = 'Too many attempts. Please wait a minute and try again.';
            } else {
                $name  = Security::sanitizeString($_POST['name'] ?? '', 100);
                $email = Security::sanitizeEmail($_POST['email'] ?? '');
                $phone = Security::sanitizePhone($_POST['phone'] ?? '');
                $pass  = (string)($_POST['password'] ?? '');

                if (!$name || !$email || !Security::validatePhone($phone)) {
                    $error = 'Please fill in a valid name, email and phone number.';
                } elseif (strlen($pass) < 8) {
                    $error = 'Password must be at least 8 characters.';
                } elseif (User::emailExists($email)) {
                    $error = 'An account with this email already exists.';
                } else {
                    $id = User::create($name, $email, '+91'.Security::normalizePhone($phone), $pass);
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $id;
                    $_SESSION['user_name'] = $name;
                    Router::redirect($this->returnTo());
                }
            }
        }
        $return_to = $this->returnTo();
        require VIEWS_PATH . '/pages/register.php';
    }

    public function loginPage(): void {
        if (!empty($_SESSION['user_id'])) { Router::redirect($this->returnTo()); }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            if (!Security::checkRateLimit('user_login', Security::getIp())) {
                $error = 'Too many login attempts. Please wait 60 seconds.';
            } else {
                $email = Security::sanitizeEmail($_POST['email'] ?? '') ?: '';
                $pass  = (string)($_POST['password'] ?? '');
                $user  = $email ? User::authenticate($email, $pass) : null;
                if ($user) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    Database::query('UPDATE users SET last_login=NOW() WHERE id=?', [$user['id']]);
                    LoginHistory::log((int)$user['id'], $email, 'password', true);
                    Router::redirect($this->returnTo());
                } else {
                    $existing = $email ? User::findByEmail($email) : null;
                    LoginHistory::log($existing ? (int)$existing['id'] : null, $email, 'password', false);
                    $error = $existing
                        ? 'Invalid email or password.'
                        : 'No account found with this email. Please create an account first to continue.';
                    sleep(1);
                }
            }
        }
        $return_to = $this->returnTo();
        require VIEWS_PATH . '/pages/login.php';
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool)$params['secure'], (bool)$params['httponly']);
        }
        session_destroy();
        Router::redirect('/');
    }

    // ── Forgot Password: step 1 — request a reset link ───────────
    public function forgotPasswordPage(): void {
        $error = '';
        $email = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            if (!Security::checkRateLimit('forgot_password', Security::getIp())) {
                $error = 'Too many attempts. Please wait a minute and try again.';
            } else {
                $email = Security::sanitizeEmail($_POST['email'] ?? '') ?: '';
                $user  = $email ? User::findByEmail($email) : null;
                if ($user && $user['status'] === 'active') {
                    $code = User::createPasswordResetCode((int)$user['id']);
                    Mailer::send(
                        $user['email'],
                        'Your ELLCY password reset code',
                        '<p>Hi ' . Security::e($user['name']) . ',</p>' .
                        '<p>Your ELLCY password reset code is:</p>' .
                        '<p style="font-size:28px;font-weight:800;letter-spacing:6px;color:#6a1b9a;margin:12px 0">' . Security::e($code) . '</p>' .
                        '<p>This code expires in 10 minutes. Enter it on the ELLCY website to set a new password.</p>' .
                        '<p>If you did not request this, you can safely ignore this email.</p>'
                    );
                }
                // Always move to the same next step whether or not the
                // email exists — prevents attackers from using this form
                // to enumerate registered accounts. If it doesn't exist,
                // the next step's code entry will simply never succeed.
                Router::redirect('reset-password?email=' . urlencode($email));
                return;
            }
        }
        require VIEWS_PATH . '/pages/forgot-password.php';
    }

    // ── Forgot Password: step 2 — enter the emailed 6-digit code + a new password ──
    public function resetPasswordPage(): void {
        $error = '';
        $done  = false;
        $email = Security::sanitizeEmail($_GET['email'] ?? $_POST['email'] ?? '') ?: '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            $code = preg_replace('/[^0-9]/', '', (string)($_POST['code'] ?? ''));
            if (!Security::checkRateLimit('reset_password', Security::getIp())) {
                $error = 'Too many attempts. Please wait a minute and try again.';
            } elseif (strlen($code) !== 6) {
                $error = 'Please enter the 6-digit code we emailed you.';
            } else {
                $valid = $email !== '' ? User::findByValidResetCode($email, $code) : null;
                if (!$valid) {
                    $error = 'That code is incorrect or has expired. Please request a new one.';
                } else {
                    $pass    = (string)($_POST['password'] ?? '');
                    $confirm = (string)($_POST['password_confirm'] ?? '');
                    if (strlen($pass) < 8) {
                        $error = 'Password must be at least 8 characters.';
                    } elseif ($pass !== $confirm) {
                        $error = 'Passwords do not match.';
                    } else {
                        User::updatePassword((int)$valid['id'], $pass);
                        User::consumePasswordReset((int)$valid['reset_id']);
                        $done = true;
                    }
                }
            }
        }
        require VIEWS_PATH . '/pages/reset-password.php';
    }

    // ── Account Settings / My Profile (requires login) ───────────
    public function accountPage(): void {
        if (empty($_SESSION['user_id'])) {
            Router::redirect('login?return_to=' . urlencode('/account'));
        }
        $user  = User::findById((int)$_SESSION['user_id']);
        $error = '';
        $saved = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            $action = $_POST['action'] ?? 'profile';
            if ($action === 'password') {
                $current = (string)($_POST['current_password'] ?? '');
                $new     = (string)($_POST['new_password'] ?? '');
                if (!$user || !Security::verifyPassword($current, (string)($user['password_hash'] ?? ''))) {
                    $error = 'Your current password is incorrect.';
                } elseif (strlen($new) < 8) {
                    $error = 'New password must be at least 8 characters.';
                } else {
                    User::updatePassword((int)$user['id'], $new);
                    $saved = true;
                }
            } else {
                $name    = Security::sanitizeString($_POST['name'] ?? '', 100);
                $phone   = Security::sanitizePhone($_POST['phone'] ?? '');
                $address = Security::sanitizeString($_POST['address'] ?? '', 255);
                if (!$name || !Security::validatePhone($phone)) {
                    $error = 'Please enter a valid name and phone number.';
                } else {
                    User::updateProfile((int)$user['id'], $name, '+91'.Security::normalizePhone($phone), $address);
                    $_SESSION['user_name'] = $name;
                    $saved = true;
                }
            }
            $user = User::findById((int)$_SESSION['user_id']);
        }
        $orders = Order::getByUserId((int)$_SESSION['user_id']);
        require VIEWS_PATH . '/pages/account.php';
    }

    // ── Phone login, step 1: send an OTP ────────────────────────
    // Deliberately does NOT create an account or send a code for a
    // phone number with no existing account — it tells the person
    // to create an account first instead. This is the one place
    // that decision gets made, so both the "send" and "verify"
    // steps stay consistent.
    public function sendPhoneOtp(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); return; }
        Security::requireCsrf();

        try {
            $ip    = Security::getIp();
            $phone = Security::sanitizePhone($_POST['phone'] ?? '');

            if (!Security::checkRateLimit('otp_send', $ip)) {
                echo json_encode(['ok'=>false,'error'=>'Too many requests. Please wait a minute and try again.']);
                return;
            }
            if (!Security::validatePhone($phone)) {
                echo json_encode(['ok'=>false,'error'=>'Please enter a valid 10-digit mobile number.']);
                return;
            }

            $normalized = Security::normalizePhone($phone);
            $user = User::findByPhone($normalized);

            if (!$user) {
                // No account for this number — log the attempt (user_id
                // NULL marks it as "identifier never matched an account")
                // and send them to Create Account instead of an OTP.
                LoginHistory::log(null, $normalized, 'otp', false);
                echo json_encode([
                    'ok'    => false,
                    'error' => 'No account found with this phone number. Please create an account first to continue.',
                    'noAccount' => true,
                ]);
                return;
            }
            if ($user['status'] !== 'active') {
                echo json_encode(['ok'=>false,'error'=>'This account is not active. Please contact support.']);
                return;
            }

            $code = OtpLogin::create($normalized, $ip);
            if ($code === null) {
                $wait = OtpLogin::secondsUntilResend($normalized);
                echo json_encode(['ok'=>false,'error'=>'Please wait a moment before requesting another code.','retryAfter'=>$wait]);
                return;
            }

            Sms::sendOtp($normalized, $code);
            $response = ['ok'=>true,'message'=>'A 6-digit code has been sent to your phone.'];
            // DEV-ONLY: no real SMS gateway is wired up yet (see
            // app/helpers/Sms.php) — Sms::sendOtp() currently just writes
            // the code to the PHP error log, which a person testing on
            // their own phone has no way to see. While APP_ENV is not
            // 'production', hand the code back directly so the login page
            // can display it on-screen. This never happens in production
            // (APP_DEBUG is false there) and disappears automatically the
            // moment a real gateway is wired in and this dev block is
            // removed from Sms.php.
            if (APP_DEBUG) { $response['devOtp'] = $code; }
            echo json_encode($response);
        } catch (\Throwable $e) {
            // A raw PHP/DB error here (most commonly: the otp_logins /
            // login_history tables don't exist yet because
            // sql/production_update_v7_phone_otp_login.sql hasn't been
            // run) would otherwise print HTML into what the frontend
            // expects to be JSON — fetch's r.json() then throws, and
            // the client shows a misleading "Network error" even
            // though the server received the request just fine.
            error_log('[ELLCY] sendPhoneOtp failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok'=>false,'error'=>'Something went wrong sending your code. Please try again in a moment, or use Email &amp; Password instead.']);
        }
    }

    // ── Phone login, step 2: verify the OTP ─────────────────────
    public function verifyPhoneOtp(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); return; }
        Security::requireCsrf();

        try {
            $ip    = Security::getIp();
            $phone = Security::sanitizePhone($_POST['phone'] ?? '');
            $code  = Security::sanitizeString($_POST['otp'] ?? '', 6);

            if (!Security::checkRateLimit('otp_verify', $ip)) {
                echo json_encode(['ok'=>false,'error'=>'Too many attempts. Please wait a minute and try again.']);
                return;
            }
            if (!Security::validatePhone($phone) || !preg_match('/^\d{6}$/', $code)) {
                echo json_encode(['ok'=>false,'error'=>'Please enter the 6-digit code.']);
                return;
            }

            $normalized = Security::normalizePhone($phone);
            $user = User::findByPhone($normalized);
            if (!$user) {
                echo json_encode(['ok'=>false,'error'=>'No account found with this phone number. Please create an account first to continue.','noAccount'=>true]);
                return;
            }

            $result = OtpLogin::verify($normalized, $code);

            if (!$result['ok']) {
                LoginHistory::log((int)$user['id'], $normalized, 'otp', false);
                $messages = [
                    'expired' => 'That code has expired. Please request a new one.',
                    'locked'  => 'Too many incorrect attempts. Please request a new code.',
                    'invalid' => 'Incorrect code. ' . $result['attemptsLeft'] . ' attempt(s) left.',
                ];
                echo json_encode([
                    'ok'            => false,
                    'error'         => $messages[$result['reason']] ?? 'Incorrect code.',
                    'showResend'    => in_array($result['reason'], ['expired', 'locked'], true),
                    'attemptsLeft'  => $result['attemptsLeft'],
                ]);
                return;
            }

            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            Database::query('UPDATE users SET last_login=NOW() WHERE id=?', [$user['id']]);
            LoginHistory::log((int)$user['id'], $normalized, 'otp', true);

            echo json_encode(['ok'=>true, 'redirect' => Router::url($this->returnTo())]);
        } catch (\Throwable $e) {
            error_log('[ELLCY] verifyPhoneOtp failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok'=>false,'error'=>'Something went wrong verifying your code. Please try again in a moment, or use Email &amp; Password instead.']);
        }
    }

    // ── Session-check API used by the floating login avatar (js/cart.js) ──
    public function me(): void {
        header('Content-Type: application/json');
        if (!empty($_SESSION['user_id'])) {
            echo json_encode([
                'logged_in' => true,
                'name'      => $_SESSION['user_name'] ?? 'Account',
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
    }

    // ── Shared helper: where auth should send the user afterwards ──
    private function returnTo(): string {
        $rt = $_POST['return_to'] ?? $_GET['return_to'] ?? '';
        // Only allow internal relative paths — never an open redirect.
        if (!is_string($rt) || $rt === '' || preg_match('/[\x00-\x1F\x7F\\\\]/', $rt)) {
            return '/';
        }

        $parts = parse_url($rt);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
            return '/';
        }
        $path = $parts['path'] ?? '/';
        if (!str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return '/';
        }
        $decodedPath = rawurldecode($path);
        if (str_contains($decodedPath, '..') || str_contains($decodedPath, '\\')) {
            return '/';
        }

        // The incoming value is the browser's raw absolute path
        // (window.location.pathname from auth.js, or $_SERVER['REQUEST_URI']
        // from BookingController's login gate) — when the app is installed
        // in a subfolder (e.g. APP_BASE === '/ellcy'), that path ALREADY
        // includes the subfolder. Router::url()/APP_URL will add APP_BASE
        // again when we redirect, so strip one copy here — otherwise the
        // user lands on a doubled "/ellcy/ellcy/index.html" 404 after
        // registering or logging in.
        if (APP_BASE !== '' && ($path === APP_BASE || str_starts_with($path, APP_BASE . '/'))) {
            $path = substr($path, strlen(APP_BASE));
        }

        $path = $path !== '' ? $path : '/';
        if (in_array(rtrim($path, '/'), ['/login', '/register', '/logout'], true)) {
            return '/';
        }
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';
        return $path . $query;
    }
}
