<?php
/**
 * ELLCY — Front Controller
 * Handles PHP routes (admin, booking, RFC, search).
 * All other requests (HTML pages, CSS, JS, images) are served
 * directly by Apache via .htaccess — index.php is only called when
 * no real file matches the URL.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/Security.php';
require_once __DIR__ . '/app/helpers/Router.php';

Security::startSession();
Security::setHeaders();
header('X-ELLCY-Front-Controller: index.php');

require_once __DIR__ . '/app/models/Service.php';
require_once __DIR__ . '/app/models/Category.php';
require_once __DIR__ . '/app/models/Order.php';
require_once __DIR__ . '/app/models/RequestCall.php';
require_once __DIR__ . '/app/models/User.php';
require_once __DIR__ . '/app/models/OtpLogin.php';
require_once __DIR__ . '/app/models/LoginHistory.php';
require_once __DIR__ . '/app/models/Upload.php';
require_once __DIR__ . '/app/helpers/Sms.php';
require_once __DIR__ . '/app/models/CateringStaffCalculator.php';
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/ServiceController.php';
require_once __DIR__ . '/app/controllers/BookingController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';
require_once __DIR__ . '/app/controllers/EnquiryController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/UploadController.php';
require_once __DIR__ . '/app/controllers/VendorController.php';

// ── Maintenance mode ────────────────────────────────────────────────
$maintenance = (string)(getenv('ELLCY_MAINTENANCE') ?: '0');
if ($maintenance === '1' && !str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin')) {
        http_response_code(503);
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Maintenance — ELLCY</title>
        <style>body{font-family:system-ui;display:flex;align-items:center;justify-content:center;
        min-height:100vh;margin:0;background:#f4e9ff;text-align:center}
        h1{color:#6a1b9a;font-size:2rem}p{color:#555;margin-top:10px}</style></head>
        <body><div><h1>ELLCY</h1><p>We\'re performing scheduled maintenance.<br>
        We\'ll be back shortly.</p></div></body></html>';
    exit;
}

// ── Router ──────────────────────────────────────────────────────────
$router = new Router(APP_BASE);

// ── HOME: serve index.html directly ─────────────────────────────────
$renderPhpHome = static fn() => (new HomeController())->index();
$router->get('/', $renderPhpHome);
$router->get('/index.html', static fn() => Router::redirect('/'));

// Legacy page templates request /js/*.js. Keep one active JavaScript tree in
// /public/js while serving those compatibility URLs through PHP.
$router->get('/js/*', function (string $file): void {
    if (!preg_match('/^[a-zA-Z0-9._-]+\.js$/', $file)) {
        http_response_code(404);
        return;
    }

    $publicJsRoot = realpath(ROOT_PATH . '/public/js');
    $assetPath = realpath(ROOT_PATH . '/public/js/' . $file);
    if ($publicJsRoot === false || $assetPath === false ||
        !str_starts_with(str_replace('\\', '/', $assetPath), rtrim(str_replace('\\', '/', $publicJsRoot), '/') . '/')) {
        http_response_code(404);
        return;
    }

    header('Content-Type: application/javascript; charset=UTF-8');
    header('Cache-Control: no-cache');
    readfile($assetPath);
});

$serveLegacyHtml = static fn(string $directory, string $relativeFile): bool =>
    LegacyPage::render($directory, $relativeFile);

$redirectWithQuery = static function (string $path): void {
    $query = http_build_query($_GET);
    header('Location: ' . Router::url($path) . ($query !== '' ? '?' . $query : ''), true, 302);
};

// Templates still contain a few root-relative legacy *.html links. Route each
// one back to its canonical PHP endpoint so navigation works identically on
// Apache and Vercel while the templates are migrated incrementally.
$rootLegacyRouteMap = [
    '/services.html' => '/services',
    '/category.html' => '/category',
    '/cart.html' => '/cart',
    '/booking.html' => '/booking',
    '/request-for-call.html' => '/request-for-call',
    '/enquiry.html' => '/enquiry',
    '/success.html' => '/success',
    '/service_details.html' => '/service-details',
    '/service-description.html' => '/service-description',
];
foreach ($rootLegacyRouteMap as $legacyRoute => $canonicalRoute) {
    $router->get($legacyRoute, static function () use ($redirectWithQuery, $canonicalRoute): void {
        $redirectWithQuery($canonicalRoute);
    });
}

/* Compatibility for links cached from the XAMPP /ellcy installation. The
   production site is mounted at the domain root, so strip that old prefix and
   preserve the query string instead of showing a 404. */
if (APP_BASE === '') {
    $router->get('/ellcy', static fn() => Router::redirect('/'));
    $router->get('/ellcy/*', static function (string $path) use ($redirectWithQuery): void {
        $path = trim(str_replace('\\', '/', rawurldecode($path)), '/');
        if ($path === '' || str_contains($path, '..') || str_contains($path, "\0")) {
            http_response_code($path === '' ? 302 : 400);
            if ($path === '') Router::redirect('/');
            return;
        }
        $redirectWithQuery('/' . $path);
    });
}

// ── SERVICES listing: serve pages/services.html ─────────────────────
$router->get('/services', function () use ($serveLegacyHtml) {
    $type = Security::sanitizeString($_GET['type'] ?? '', 80);
    $musicPhpTypes = [
        'music-performers', 'musical-band', 'chenda-melam', 'band-set',
        'melam-set', 'nadhaswaram-thavil', 'nadhaswaram-reception',
        'nadhaswaram-marriage',
    ];
    if (in_array($type, $musicPhpTypes, true)) {
        (new ServiceController())->listing();
        return;
    }
    if (!$serveLegacyHtml('pages', 'services.html')) {
        (new ServiceController())->listing();
    }
});

// Clean service-detail routes are resolved by PHP. Legacy /index.html URLs
// redirect to the equivalent canonical directory URL.
$router->get('/services/*', function (string $path) use ($serveLegacyHtml, $redirectWithQuery): void {
    $path = rawurldecode(trim(str_replace('\\', '/', $path), '/'));
    if ($path === 'index.php') {
        $redirectWithQuery('/services');
        return;
    }
    if (str_contains($path, '..') || str_contains($path, "\0")) {
        http_response_code(400);
        return;
    }

    if (preg_match('#^(.*?)/?index\.html$#i', $path, $match)) {
        $cleanPath = trim($match[1], '/');
        $redirectWithQuery('/services' . ($cleanPath !== '' ? '/' . $cleanPath . '/' : ''));
        return;
    }

    $requestPath = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '');
    if (!str_ends_with(strtolower($path), '.html') && !str_ends_with($requestPath, '/')) {
        $redirectWithQuery('/services/' . trim($path, '/') . '/');
        return;
    }

    $file = str_ends_with(strtolower($path), '.html')
        ? $path
        : rtrim($path, '/') . '/index.html';
    $serveLegacyHtml('services', $file);
});

// ── CART: serve pages/cart.html ─────────────────────────────────────
$router->get('/cart', function () use ($serveLegacyHtml) {
    if (!$serveLegacyHtml('pages', 'cart.html')) {
        (new CartController())->show();
    }
});

// ── BOOKING: PHP form handler ────────────────────────────────────────
$router->any('/booking', function () {
    (new BookingController())->show();
});
$router->any('/vendor-signup', fn() => (new VendorController())->signup());

// ── REQUEST FOR CALL: PHP form handler ──────────────────────────────
$router->any('/request-for-call', function () {
    (new RequestCallController())->show();
});

// ── SEARCH: JSON API ─────────────────────────────────────────────────
$router->get('/search', function () {
    (new ServiceController())->search();
});

// ── CUSTOMER AUTH (separate from /admin auth) ────────────────────────
$router->any('/register', fn() => (new AuthController())->registerPage());
$router->any('/login',    fn() => (new AuthController())->loginPage());
$router->get('/logout',   fn() => (new AuthController())->logout());
$router->get('/api/auth/me', fn() => (new AuthController())->me());
$router->get('/api/csrf', fn() => (new UploadController())->csrf());
$router->post('/api/auth/otp/send',   fn() => (new AuthController())->sendPhoneOtp());
$router->post('/api/auth/otp/verify', fn() => (new AuthController())->verifyPhoneOtp());
$router->post('/api/uploads/jewellery-reference', fn() => (new UploadController())->jewelleryReference());
$router->post('/api/uploads/jewellery-reference/remove', fn() => (new UploadController())->removeJewelleryReference());
$router->any('/forgot-password', fn() => (new AuthController())->forgotPasswordPage());
$router->any('/reset-password',  fn() => (new AuthController())->resetPasswordPage());
$router->any('/account',         fn() => (new AuthController())->accountPage());

// ── CATERING STAFF CALCULATION (Excel-sourced lookup) ────────────────
$router->get('/api/catering-staff', fn() => (new ServiceController())->apiCateringStaff());

// ── PUBLIC DATA API: powers the DB-driven frontend (js/data.js) ──────
$router->get('/api/categories',      fn() => (new ServiceController())->apiCategories());
$router->get('/api/services',        fn() => (new ServiceController())->apiServices());
$router->get('/api/services/:slug',  fn(string $slug) => (new ServiceController())->apiServiceDetail($slug));

// ── Decoration enquiry forms (Stage Decoration / Light Decoration) ───
$router->post('/enquiry/stage-decoration', fn() => (new EnquiryController())->stageDecoration());
$router->post('/enquiry/light-decoration', fn() => (new EnquiryController())->lightDecoration());

// ── PAGES pass-through (pages/xxx.html) ─────────────────────────────
// Hardened against path traversal: the resolved real path must stay
// inside the /pages directory, no matter what the request URI contains.
$cleanLegacyPages = [
    '/category' => 'category.html',
    '/enquiry' => 'enquiry.html',
    '/success' => 'success.html',
    '/service-details' => 'service_details.html',
    '/service-description' => 'service-description.html',
];
foreach ($cleanLegacyPages as $route => $file) {
    $router->get($route, static function () use ($serveLegacyHtml, $file): void {
        $serveLegacyHtml('pages', $file);
    });
}

// Keep the plural URL used by older navigation and shared links working.
// `/category` remains canonical because that is the existing PHP endpoint.
$router->get('/categories', static function (): void {
    $query = http_build_query($_GET);
    header('Location: ' . Router::url('/category') . ($query !== '' ? '?' . $query : ''), true, 301);
});

$router->get('/pages/*', function (string $path) use ($serveLegacyHtml, $redirectWithQuery): void {
    $path = rawurldecode(trim(str_replace('\\', '/', $path), '/'));
    $cleanRouteMap = [
        'services.html' => '/services',
        'cart.html' => '/cart',
        'booking.html' => '/booking',
        'request-for-call.html' => '/request-for-call',
        'category.html' => '/category',
        'enquiry.html' => '/enquiry',
        'success.html' => '/success',
        'service_details.html' => '/service-details',
        'service-description.html' => '/service-description',
    ];

    if (isset($cleanRouteMap[$path])) {
        $redirectWithQuery($cleanRouteMap[$path]);
        return;
    }

    $file = str_ends_with(strtolower($path), '.html') ? $path : $path . '.html';
    $serveLegacyHtml('pages', $file);
});

        // realpath() resolves symlinks/.. — confirm the result is
        // still inside pages/ before ever reading it.

// ── ADMIN ROUTES ─────────────────────────────────────────────────────
$router->any('/admin/login',    fn() => (new AdminController())->loginPage());
$router->get('/admin/index.php', static fn() => Router::redirect('/admin'));
$router->get('/admin/logout',   fn() => (new AdminController())->logout());
$router->get('/admin',          fn() => (new AdminController())->dashboard());
$router->get('/admin/dashboard',fn() => (new AdminController())->dashboard());

// Services CRUD
$router->get('/admin/services',              fn() => (new AdminController())->servicesList());
$router->any('/admin/services/create',       fn() => (new AdminController())->serviceCreate());
$router->any('/admin/services/edit/:id',     fn(string $id) => (new AdminController())->serviceEdit($id));
$router->post('/admin/services/delete/:id',  fn(string $id) => (new AdminController())->serviceDelete($id));
$router->get('/admin/categories',            fn() => (new AdminController())->categoriesList());
$router->any('/admin/categories/create',     fn() => (new AdminController())->categoryCreate());
$router->any('/admin/categories/edit/:id',   fn(string $id) => (new AdminController())->categoryEdit($id));
$router->post('/admin/categories/delete/:id',fn(string $id) => (new AdminController())->categoryDelete($id));
$router->get('/admin/users',                 fn() => (new AdminController())->usersList());
$router->post('/admin/users/status/:id',     fn(string $id) => (new AdminController())->userSetStatus($id));
$router->post('/admin/services/gallery/add/:id',      fn(string $id) => (new AdminController())->serviceGalleryAdd($id));
$router->post('/admin/services/gallery/primary/:imgId', fn(string $imgId) => (new AdminController())->serviceGallerySetPrimary($imgId));
$router->post('/admin/services/gallery/reorder/:imgId', fn(string $imgId) => (new AdminController())->serviceGalleryReorder($imgId));
$router->post('/admin/services/gallery/delete/:imgId', fn(string $imgId) => (new AdminController())->serviceGalleryDelete($imgId));

// Bookings
$router->get('/admin/bookings',                fn() => (new AdminController())->bookingsList());
$router->post('/admin/bookings/update/:id',    fn(string $id) => (new AdminController())->bookingUpdate($id));

// Call Requests
$router->get('/admin/requests',               fn() => (new AdminController())->requestsList());
$router->post('/admin/requests/update/:id',   fn(string $id) => (new AdminController())->requestUpdate($id));
$router->get('/admin/decoration-enquiries',                fn() => (new AdminController())->decorationEnquiries());
$router->post('/admin/decoration-enquiries/update/:type/:id', fn(string $type, string $id) => (new AdminController())->decorationEnquiryUpdate($type, $id));

// Settings
$router->any('/admin/settings', fn() => (new AdminController())->settings());

// ── DISPATCH ─────────────────────────────────────────────────────────
$router->dispatch();
