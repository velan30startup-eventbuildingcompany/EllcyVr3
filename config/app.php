<?php
/**
 * ELLCY — Application Configuration
 * Auto-detects install location — works at root OR in a subfolder (e.g. /ellcy)
 */

// ── Environment ─────────────────────────────────────────────────────
// Production can be forced with ELLCY_APP_ENV=production. Localhost defaults
// to development so Secure cookies are not issued over plain HTTP.
$serverName = strtolower((string)($_SERVER['SERVER_NAME'] ?? 'localhost'));
$localHosts = ['localhost', '127.0.0.1', '::1'];
$configuredEnv = strtolower((string)(getenv('ELLCY_APP_ENV') ?: ''));
$appEnv = in_array($configuredEnv, ['production', 'development', 'testing'], true)
    ? $configuredEnv
    : (in_array($serverName, $localHosts, true) ? 'development' : 'production');
define('APP_ENV', $appEnv);
define('APP_DEBUG', APP_ENV !== 'production');

function ellcy_is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') return true;
    if ((string)($_SERVER['SERVER_PORT'] ?? '') === '443') return true;
    $trustProxy = getenv('ELLCY_TRUST_PROXY') === '1' || getenv('VERCEL') === '1';
    return $trustProxy
        && strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}
define('HTTPS_ACTIVE', ellcy_is_https());

// ── Auto-detect base URL (works for localhost/ellcy AND localhost/) ──
$isVercel = getenv('VERCEL') === '1';
$scriptDir = $isVercel
    ? ''
    : rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'))), '/');
if ($scriptDir === '.' || $scriptDir === '/') $scriptDir = '';
define('APP_BASE', $scriptDir);   // e.g. "/ellcy" or ""
$configuredOrigin = rtrim((string)(getenv('ELLCY_APP_ORIGIN') ?: ''), '/');
if ($configuredOrigin !== '' && !preg_match('#^https?://[a-z0-9.-]+(?::\d+)?$#i', $configuredOrigin)) {
    $configuredOrigin = '';
}
if ($configuredOrigin === '') {
    if (APP_ENV === 'production') {
        $vercelHost = (string)(getenv('VERCEL_PROJECT_PRODUCTION_URL') ?: getenv('VERCEL_URL') ?: '');
        $configuredOrigin = $isVercel && preg_match('#^[a-z0-9.-]+$#i', $vercelHost)
            ? 'https://' . $vercelHost
            : 'https://ellcy.in';
    } else {
        $port = (string)($_SERVER['SERVER_PORT'] ?? '80');
        $host = in_array($serverName, $localHosts, true) ? ($serverName === '::1' ? '[::1]' : $serverName) : 'localhost';
        $portPart = (($port === '80' && !HTTPS_ACTIVE) || ($port === '443' && HTTPS_ACTIVE)) ? '' : ':' . (int)$port;
        $configuredOrigin = (HTTPS_ACTIVE ? 'https' : 'http') . '://' . $host . $portPart;
    }
}
define('APP_ORIGIN', $configuredOrigin);
define('APP_URL', APP_ORIGIN . $scriptDir);

// ── Paths ───────────────────────────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEWS_PATH',   ROOT_PATH . '/app/views');
define('LOG_PATH',     STORAGE_PATH . '/logs');

// ── Security ────────────────────────────────────────────────────────
define('SESSION_LIFETIME',    7200);
define('CSRF_TOKEN_LENGTH',   32);
define('RATE_LIMIT_WINDOW',   60);
define('RATE_LIMIT_REQUESTS', 30);

// ── Mail (used by Forgot Password) ───────────────────────────────────
define('MAIL_FROM',      'no-reply@ellcy.in');
define('MAIL_FROM_NAME', 'ELLCY');

// ── Pagination ──────────────────────────────────────────────────────
define('ITEMS_PER_PAGE', 20);

// ── Upload settings ─────────────────────────────────────────────────
define('UPLOAD_MAX_SIZE',      5 * 1024 * 1024);
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg','image/png','image/webp','image/gif']);
define('UPLOAD_DIR',           ROOT_PATH . '/uploads/services/');
define('ENQUIRY_UPLOAD_MAX_SIZE', 4 * 1024 * 1024); // 4 MB — venue reference photos
define('ENQUIRY_UPLOAD_ALLOWED_TYPES', ['image/jpeg','image/png','image/webp']);
define('ENQUIRY_UPLOAD_DIR',   ROOT_PATH . '/uploads/enquiries/');
define('REFERENCE_UPLOAD_MAX_SIZE', 4 * 1024 * 1024);
define('REFERENCE_UPLOAD_DIR', ROOT_PATH . '/uploads/references/');

// ── Error handling ──────────────────────────────────────────────────
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ── Auto-loader ─────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    foreach ([
        ROOT_PATH . '/app/models/',
        ROOT_PATH . '/app/controllers/',
        ROOT_PATH . '/app/helpers/',
        ROOT_PATH . '/app/middleware/',
    ] as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});
