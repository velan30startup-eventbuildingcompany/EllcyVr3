<?php
/**
 * ELLCY — BookingController
 * Tries to serve existing pages/booking.html first.
 * Falls back to PHP view when HTML is not present.
 * The PHP view also handles the AJAX POST to store bookings in MySQL.
 */
class BookingController {
    public function show(): void {
        // ── Server-side auth gate ────────────────────────────────────
        // Browsing is always open; only booking itself requires login.
        // Preserve the exact URL (including query string, e.g. ?mode=buynow)
        // so the user returns to finish their booking after logging in.
        if (empty($_SESSION['user_id'])) {
            $returnTo = $_SERVER['REQUEST_URI'] ?? '/booking';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success'       => false,
                    'requires_login'=> true,
                    'login_url'     => Router::url('login') . '?return_to=' . urlencode($returnTo),
                    'message'       => 'Please log in to complete your booking.',
                ]);
                exit;
            }
            Router::redirect('login?return_to=' . urlencode($returnTo));
        }

        // Handle AJAX booking POST (stores to MySQL)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            // CSRF optional — static HTML booking page
            if (!Security::verifyCsrf()) {
                http_response_code(403);
                echo json_encode(['success'=>false,'message'=>'Your session expired. Reload the page and try again.']); exit;
            }
            if (!Security::checkRateLimit('booking', Security::getIp())) {
                http_response_code(429);
                echo json_encode(['success'=>false,'message'=>'Too many requests.']); exit;
            }
            $name       = Security::sanitizeString($_POST['name'] ?? '', 100);
            $email      = Security::sanitizeEmail($_POST['email'] ?? '') ?: '';
            $phone      = Security::sanitizePhone($_POST['phone'] ?? '');
            $eventType  = Security::sanitizeString($_POST['event_type'] ?? '', 100);
            $eventDate  = $_POST['event_date'] ?? '';
            $eventVenue = Security::sanitizeString($_POST['venue'] ?? '', 300);
            $eventTime  = Security::sanitizeString($_POST['event_time'] ?? '', 50);
            $guests     = Security::sanitizeInt($_POST['guest_count'] ?? 0, 0, 100000);
            $note       = Security::sanitizeString($_POST['note'] ?? '', 500);
            $itemsJson  = $_POST['items_json'] ?? '[]';

            if (!$name || !Security::validatePhone($phone) || (($_POST['email'] ?? '') !== '' && !$email)) {
                echo json_encode(['success'=>false,'message'=>'Please fill all required fields.']); exit;
            }
            if ($eventDate !== '') {
                $parsed = DateTime::createFromFormat('Y-m-d', $eventDate);
                if (!$parsed || $parsed->format('Y-m-d') !== $eventDate) {
                    echo json_encode(['success'=>false,'message'=>'Choose a valid event date.']); exit;
                }
            }

            [$venueImages, $uploadError] = $this->handleVenueImages();
            if ($uploadError) { echo json_encode(['success'=>false,'message'=>$uploadError]); exit; }

            $items = $this->normaliseItems(json_decode($itemsJson, true));
            if (!$items) {
                echo json_encode(['success'=>false,'message'=>'Your booking does not contain any valid services.']); exit;
            }
            $referenceTokens = [];
            foreach ($items as &$item) {
                if (!empty($item['reference_upload_token'])) $referenceTokens[] = $item['reference_upload_token'];
                unset($item['reference_upload_token'], $item['reference_preview_url']);
            }
            unset($item);
            $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));
            $id = Order::create([
                'user_id'     => $_SESSION['user_id'],
                'name'        => $name, 'email' => $email,
                'phone'       => '+91'.preg_replace('/[^0-9]/','',$phone),
                'event_type'  => $eventType, 'event_date' => $eventDate ?: null,
                'event_venue' => $eventVenue, 'event_venue_images' => $venueImages,
                'event_time'  => $eventTime,
                'guest_count' => $guests ?: null, 'items' => $items,
                'subtotal'    => $subtotal, 'total' => $subtotal, 'note' => $note,
            ]);
            Upload::attachToOrder($referenceTokens, $id, (int)$_SESSION['user_id']);
            $ref = Database::fetchOne('SELECT order_ref FROM orders WHERE id=?',[$id])['order_ref'] ?? '';
            echo json_encode(['success'=>true,'order_ref'=>$ref,'message'=>'Booking confirmed!']); exit;
        }
        // Serve existing HTML page
        if (LegacyPage::render('pages', 'booking.html')) { return; }
        // Fallback to PHP view
        $settings = [];
        require VIEWS_PATH . '/pages/booking.php';
    }

    // ── Up to 4 Event Location reference photos (optional) ───────────
    // Mirrors EnquiryController's single-image upload pattern: real
    // mime-type sniffing (never trusts the client's Content-Type or
    // filename), random filenames, capped size. Returns
    // [jsonArrayOfPaths|null, errorMessage|null].
    private function normaliseItems(mixed $decoded): array {
        if (!is_array($decoded)) return [];
        $items = [];
        foreach (array_slice($decoded, 0, 50) as $raw) {
            if (!is_array($raw)) continue;
            $title = Security::sanitizeString((string)($raw['title'] ?? $raw['id'] ?? ''), 180);
            $id = Security::sanitizeString((string)($raw['id'] ?? $raw['uid'] ?? ''), 220);
            if ($title === '' || $id === '') continue;
            $qty = Security::sanitizeInt($raw['qty'] ?? 1, 1, 50);
            $price = round(max(0, min(10000000, (float)($raw['price'] ?? 0))), 2);
            $packageSlug = Security::sanitizeString((string)($raw['package_slug'] ?? ''), 120);
            if ($packageSlug !== '') {
                $package = Database::fetchOne(
                    "SELECT p.slug,p.label,p.price,s.slug AS service_slug
                     FROM service_packages p JOIN services s ON s.id=p.service_id
                     WHERE p.slug=? AND p.status='active' AND s.status='active' LIMIT 1",
                    [$packageSlug]
                );
                if (!$package) continue;
                $title = (string)$package['label'];
                $price = (float)$package['price'];
            }
            $item = [
                'id' => $id, 'title' => $title, 'price' => $price, 'qty' => $qty,
                'package' => Security::sanitizeString((string)($raw['package'] ?? ''), 120),
                'package_slug' => $packageSlug,
                'slot' => Security::sanitizeString((string)($raw['slot'] ?? ''), 80),
            ];
            $token = strtolower((string)($raw['reference_upload_token'] ?? ''));
            if (preg_match('/^[a-f0-9]{64}$/', $token)) $item['reference_upload_token'] = $token;
            $preview = (string)($raw['reference_preview_url'] ?? '');
            if (str_contains($preview, '/uploads/references/')) $item['reference_preview_url'] = $preview;
            $items[] = $item;
        }
        return $items;
    }

    private function handleVenueImages(): array {
        if (empty($_FILES['venue_images'])) return [null, null];

        $files = $_FILES['venue_images'];
        $count = is_array($files['name'] ?? null) ? count($files['name']) : 0;
        if ($count === 0) return [null, null];
        if ($count > 4) {
            return [null, 'You can upload a maximum of 4 event location photos.'];
        }

        $dir = ROOT_PATH . '/uploads/venues/';
        if (!is_dir($dir)) { mkdir($dir, 0755, true); }

        $stored = [];

        for ($i = 0; $i < $count; $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                return [null, 'There was a problem uploading one of your photos. Please try again.'];
            }
            $singleFile = [
                'name'=>$files['name'][$i], 'tmp_name'=>$files['tmp_name'][$i],
                'size'=>$files['size'][$i], 'error'=>$files['error'][$i],
            ];
            $inspection = Security::inspectImageUpload(
                $singleFile, ENQUIRY_UPLOAD_MAX_SIZE, ['image/jpeg','image/png','image/webp','image/gif']
            );
            if ($inspection['errors']) return [null, implode(' ', $inspection['errors'])];
            $ext = $inspection['extension'];
            $filename = 'venue_' . bin2hex(random_bytes(12)) . '.' . $ext;
            if (!move_uploaded_file($files['tmp_name'][$i], $dir . $filename)) {
                return [null, 'Could not save one of the uploaded photos. Please try again.'];
            }
            $stored[] = '/uploads/venues/' . $filename;
        }

        return [$stored ? json_encode($stored) : null, null];
    }
}

/**
 * ELLCY — CartController
 */
class CartController {
    public function show(): void {
        if (LegacyPage::render('pages', 'cart.html')) { return; }
        require VIEWS_PATH . '/pages/cart.php';
    }
}

/**
 * ELLCY — RequestCallController
 */
class RequestCallController {
    public function show(): void {
        // Handle AJAX POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            // CSRF is optional here — static HTML page cannot generate PHP sessions
            // Rate limiting still applies
            if (!Security::verifyCsrf()) {
                http_response_code(403);
                echo json_encode(['success'=>false,'message'=>'Your session expired. Reload the page and try again.']); exit;
            }
            if (!Security::checkRateLimit('rfc', Security::getIp())) {
                http_response_code(429);
                echo json_encode(['success'=>false,'message'=>'Too many requests.']); exit;
            }
            $phone    = Security::sanitizePhone($_POST['phone'] ?? '');
            $service  = Security::sanitizeString($_POST['service'] ?? '', 200);
            $bestTime = Security::sanitizeString($_POST['best_time'] ?? '', 50);
            $note     = Security::sanitizeString($_POST['note'] ?? '', 300);
            if (!Security::validatePhone($phone)) {
                echo json_encode(['success'=>false,'message'=>'Invalid phone number.']); exit;
            }
            if (empty($service)) {
                echo json_encode(['success'=>false,'message'=>'Please select a service.']); exit;
            }
            $requestId = RequestCall::create([
                'phone'     => '+91'.preg_replace('/[^0-9]/','',$phone),
                'service'   => $service, 'best_time' => $bestTime,
                'note'      => $note,    'ip'        => Security::getIp(),
            ]);
            $referenceToken = strtolower((string)($_POST['reference_token'] ?? ''));
            if ($referenceToken !== '') Upload::attachToRequest($referenceToken, $requestId);
            echo json_encode(['success'=>true,'message'=>'Request received!']); exit;
        }
        // Serve existing request-for-call.html
        if (LegacyPage::render('pages', 'request-for-call.html')) { return; }
        // Fallback to PHP view
        require VIEWS_PATH . '/pages/request-for-call.php';
    }
}
