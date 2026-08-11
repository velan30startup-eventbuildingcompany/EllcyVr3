<?php
/**
 * ELLCY — Security Helper
 * Handles CSRF, XSS escaping, input sanitization, rate limiting.
 */
class Security {

    // ── Output escaping ─────────────────────────────────────────────
    public static function e(mixed $val): string {
        return htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    // ── CSRF token ──────────────────────────────────────────────────
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string {
        return '<input type="hidden" name="csrf_token" value="' . self::csrfToken() . '">';
    }

    public static function verifyCsrf(): bool {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || empty($_SESSION['csrf_token'])) return false;
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function requireCsrf(): void {
        if (!self::verifyCsrf()) {
            http_response_code(403);
            die(json_encode(['success' => false, 'message' => 'Invalid request token.']));
        }
    }

    // ── Input sanitization ──────────────────────────────────────────
    public static function sanitizeString(string $input, int $maxLen = 500): string {
        $clean = strip_tags(trim($input));
        $clean = preg_replace('/[<>\'"\/\\\\]/', '', $clean);
        return mb_substr($clean, 0, $maxLen);
    }

    public static function sanitizePhone(string $phone): string {
        return preg_replace('/[^0-9+\-\s]/', '', trim($phone));
    }

    public static function sanitizeEmail(string $email): string|false {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }

    public static function sanitizeInt(mixed $val, int $min = 0, int $max = PHP_INT_MAX): int {
        $int = filter_var($val, FILTER_VALIDATE_INT);
        if ($int === false) return $min;
        return max($min, min($max, (int)$int));
    }

    // ── Normalize an Indian mobile number to its bare 10-digit form.
    //    Returns '' if the input doesn't resolve to a valid number.
    //    Shared by validatePhone() and phone-OTP login lookups so a
    //    user typing "+91 98765 43210", "919876543210" or
    //    "9876543210" always resolves to the same stored value. ──
    public static function normalizePhone(string $phone): string {
        // Keep only a leading '+' and digits.
        $digits = preg_replace('/[^\d+]/', '', trim($phone));
        $hasPlus = str_starts_with($digits, '+');
        $digitsOnly = ltrim($digits, '+');

        // A bare 10-digit number is used as-is — do NOT strip a
        // country-code prefix here, since a real number can validly
        // start with "91" (e.g. 9123456789). Only strip +91/91/0
        // when extra digits beyond the 10-digit number are present.
        if (!$hasPlus && strlen($digitsOnly) === 10) {
            $clean = $digitsOnly;
        } elseif ($hasPlus && str_starts_with($digitsOnly, '91') && strlen($digitsOnly) === 12) {
            $clean = substr($digitsOnly, 2);
        } elseif (!$hasPlus && str_starts_with($digitsOnly, '91') && strlen($digitsOnly) === 12) {
            $clean = substr($digitsOnly, 2);
        } elseif (!$hasPlus && str_starts_with($digitsOnly, '0') && strlen($digitsOnly) === 11) {
            $clean = substr($digitsOnly, 1);
        } else {
            $clean = $digitsOnly;
        }

        return preg_match('/^[6-9]\d{9}$/', $clean) ? $clean : '';
    }

    // ── Validate Indian mobile number ──────────────────────────────
    public static function validatePhone(string $phone): bool {
        return self::normalizePhone($phone) !== '';
    }

    // ── Rate limiting ───────────────────────────────────────────────
    public static function checkRateLimit(string $action, string $ip = ''): bool {
        if (!$ip) $ip = self::getIp();
        try {
            require_once ROOT_PATH . '/config/database.php';
            $db = Database::getInstance();

            // Clean expired
            $db->prepare('DELETE FROM rate_limits WHERE window_end < NOW()')->execute();

            $row = Database::fetchOne(
                'SELECT attempts, window_end FROM rate_limits WHERE ip_address=? AND action=? AND window_end > NOW()',
                [$ip, $action]
            );

            if (!$row) {
                $windowEnd = date('Y-m-d H:i:s', time() + RATE_LIMIT_WINDOW);
                Database::query(
                    'INSERT INTO rate_limits (ip_address, action, attempts, window_end) VALUES (?,?,1,?)',
                    [$ip, $action, $windowEnd]
                );
                return true;
            }

            if ((int)$row['attempts'] >= RATE_LIMIT_REQUESTS) {
                return false;  // Too many requests
            }

            Database::query(
                'UPDATE rate_limits SET attempts=attempts+1 WHERE ip_address=? AND action=?',
                [$ip, $action]
            );
            return true;
        } catch (Exception $e) {
            // Keep abuse protection active during a temporary DB outage.
            $key = hash('sha256', $action . '|' . $ip);
            $now = time();
            $bucket = $_SESSION['_rate_fallback'][$key] ?? ['start' => $now, 'attempts' => 0];
            if (($now - (int)$bucket['start']) >= RATE_LIMIT_WINDOW) $bucket = ['start' => $now, 'attempts' => 0];
            $bucket['attempts']++;
            $_SESSION['_rate_fallback'][$key] = $bucket;
            return $bucket['attempts'] <= RATE_LIMIT_REQUESTS;
        }
    }

    // ── Security headers ────────────────────────────────────────────
    public static function setHeaders(): void {
        if (headers_sent()) return;
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Permitted-Cross-Domain-Policies: none');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        header("Content-Security-Policy: default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; "
            . "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; "
            . "img-src 'self' data: blob: https:; "
            . "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com data:; "
            . "connect-src 'self'; "
            . "media-src 'self' blob:; "
            . "frame-src https://www.youtube-nocookie.com https://www.youtube.com https://player.vimeo.com; "
            . "frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none'");
        if (HTTPS_ACTIVE) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    // ── Session management ──────────────────────────────────────────
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.cookie_secure', HTTPS_ACTIVE ? '1' : '0');
            ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);
            session_name('ELLCYSESSID');
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => APP_BASE !== '' ? APP_BASE . '/' : '/',
                'secure' => HTTPS_ACTIVE,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
        // Regenerate session ID periodically
        if (empty($_SESSION['last_regen']) || (time() - $_SESSION['last_regen']) > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regen'] = time();
        }
    }

    // ── IP address ──────────────────────────────────────────────────
    public static function getIp(): string {
        $trustedProxy = getenv('ELLCY_TRUST_PROXY') === '1' || getenv('VERCEL') === '1';
        $keys = $trustedProxy ? ['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] : ['REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '0.0.0.0';
    }

    // ── Password ────────────────────────────────────────────────────
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    // ── File upload validation ──────────────────────────────────────
    public static function inspectImageUpload(
        array $file,
        int $maxSize = UPLOAD_MAX_SIZE,
        array $allowedMimes = UPLOAD_ALLOWED_TYPES
    ): array {
        $result = ['errors' => [], 'mime' => null, 'extension' => null];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $result['errors'][] = 'The upload did not complete successfully.';
            return $result;
        }
        if (!isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
            $result['errors'][] = 'Invalid upload source.';
            return $result;
        }
        if ((int)$file['size'] <= 0 || (int)$file['size'] > $maxSize) {
            $result['errors'][] = 'File is too large.';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
        if ($finfo) finfo_close($finfo);
        $mime = is_string($mime) ? strtolower($mime) : '';
        $map = [
            'image/jpeg' => ['extension' => 'jpg', 'client' => ['jpg', 'jpeg']],
            'image/png'  => ['extension' => 'png', 'client' => ['png']],
            'image/webp' => ['extension' => 'webp', 'client' => ['webp']],
            'image/gif'  => ['extension' => 'gif', 'client' => ['gif']],
        ];
        $clientExt = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!isset($map[$mime]) || !in_array($mime, $allowedMimes, true)) {
            $allowedLabels = in_array('image/gif', $allowedMimes, true) ? 'JPG, JPEG, PNG, WebP and GIF' : 'JPG, JPEG, PNG and WebP';
            $result['errors'][] = 'Invalid file type. Only ' . $allowedLabels . ' images are allowed.';
        } elseif (!in_array($clientExt, $map[$mime]['client'], true)) {
            $result['errors'][] = 'The filename extension does not match the image type.';
        } elseif (@getimagesize($file['tmp_name']) === false) {
            $result['errors'][] = 'The uploaded file is not a valid image.';
        } else {
            $result['mime'] = $mime;
            $result['extension'] = $map[$mime]['extension'];
        }
        return $result;
    }

    public static function validateUpload(array $file): array {
        return self::inspectImageUpload($file)['errors'];
    }
}
