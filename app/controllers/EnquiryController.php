<?php
/**
 * ELLCY — EnquiryController
 * Handles the Stage Decoration & Light Decoration enquiry forms
 * (services/stage-decoration/index.html, services/light-decoration/index.html).
 * These are static HTML pages, so — same convention as
 * BookingController/RequestCallController — CSRF is not enforced
 * here (no PHP session exists on a static page), but rate limiting,
 * strict input validation and safe file-upload handling are.
 */
class EnquiryController {

    private const BUDGET_RANGES = [
        'below-25000', '25000-50000', '50000-100000', '100000-plus', 'not-sure'
    ];

    // POST /enquiry/stage-decoration
    public function stageDecoration(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed.']); return; }
        if (!Security::verifyCsrf()) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Your session expired. Reload the page and try again.']); return; }

        if (!Security::checkRateLimit('enq_stage', Security::getIp())) {
            echo json_encode(['success'=>false,'message'=>'Too many requests. Please try again later.']); return;
        }

        [$data, $error] = $this->readAndValidateCommonFields();
        if ($error) { echo json_encode(['success'=>false,'message'=>$error]); return; }

        $flowerType = $_POST['flower_type'] ?? '';
        $flowerType = in_array($flowerType, ['real','artificial'], true) ? $flowerType : null;

        [$imagePath, $uploadError] = $this->handleVenueImageUpload();
        if ($uploadError) { echo json_encode(['success'=>false,'message'=>$uploadError]); return; }

        try {
            Database::query(
                'INSERT INTO stage_decoration_enquiries
                 (customer_name, phone_number, email, event_date, budget_range, location, flower_type, venue_image, ip_address)
                 VALUES (?,?,?,?,?,?,?,?,?)',
                [
                    $data['name'], $data['phone'], $data['email'], $data['event_date'],
                    $data['budget_range'], $data['location'], $flowerType, $imagePath, Security::getIp(),
                ]
            );
        } catch (Exception $e) {
            error_log('Stage decoration enquiry insert failed: ' . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Something went wrong. Please try again.']); return;
        }
        echo json_encode(['success'=>true,'message'=>'Thank you! Your enquiry has been received — our team will contact you shortly.']);
    }

    // POST /enquiry/light-decoration
    public function lightDecoration(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed.']); return; }
        if (!Security::verifyCsrf()) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Your session expired. Reload the page and try again.']); return; }

        if (!Security::checkRateLimit('enq_light', Security::getIp())) {
            echo json_encode(['success'=>false,'message'=>'Too many requests. Please try again later.']); return;
        }

        [$data, $error] = $this->readAndValidateCommonFields();
        if ($error) { echo json_encode(['success'=>false,'message'=>$error]); return; }

        $archRequired = $_POST['arch_required'] ?? '';
        $archRequired = in_array($archRequired, ['yes','no'], true) ? $archRequired : null;

        [$imagePath, $uploadError] = $this->handleVenueImageUpload();
        if ($uploadError) { echo json_encode(['success'=>false,'message'=>$uploadError]); return; }

        try {
            Database::query(
                'INSERT INTO light_decoration_enquiries
                 (customer_name, phone_number, email, event_date, budget_range, location, arch_required, venue_image, ip_address)
                 VALUES (?,?,?,?,?,?,?,?,?)',
                [
                    $data['name'], $data['phone'], $data['email'], $data['event_date'],
                    $data['budget_range'], $data['location'], $archRequired, $imagePath, Security::getIp(),
                ]
            );
        } catch (Exception $e) {
            error_log('Light decoration enquiry insert failed: ' . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Something went wrong. Please try again.']); return;
        }
        echo json_encode(['success'=>true,'message'=>'Thank you! Your enquiry has been received — our team will contact you shortly.']);
    }

    // ── Shared field validation ──────────────────────────────────────
    private function readAndValidateCommonFields(): array {
        $name     = Security::sanitizeString($_POST['name'] ?? '', 100);
        $phone    = Security::sanitizePhone($_POST['phone'] ?? '');
        $emailRaw = trim($_POST['email'] ?? '');
        $email    = $emailRaw !== '' ? Security::sanitizeEmail($emailRaw) : '';
        $eventDate    = $_POST['event_date'] ?? '';
        $budgetRange  = Security::sanitizeString($_POST['budget_range'] ?? '', 40);
        $location     = Security::sanitizeString($_POST['location'] ?? '', 255);

        if (!$name) {
            return [[], 'Please enter your name.'];
        }
        if (!Security::validatePhone($phone)) {
            return [[], 'Please enter a valid mobile number.'];
        }
        if ($emailRaw !== '' && !$email) {
            return [[], 'Please enter a valid email address, or leave it blank.'];
        }
        if (!$location) {
            return [[], 'Event location is required.'];
        }
        if ($budgetRange && !in_array($budgetRange, self::BUDGET_RANGES, true)) {
            $budgetRange = null;
        }
        // Validate/normalise date (YYYY-MM-DD from <input type=date>)
        $validDate = null;
        if ($eventDate) {
            $d = DateTime::createFromFormat('Y-m-d', $eventDate);
            if ($d && $d->format('Y-m-d') === $eventDate) {
                $validDate = $eventDate;
            }
        }

        return [[
            'name'         => $name,
            'phone'        => '+91' . preg_replace('/[^0-9]/', '', $phone),
            'email'        => $email ?: null,
            'event_date'   => $validDate,
            'budget_range' => $budgetRange ?: null,
            'location'     => $location,
        ], null];
    }

    // ── Shared secure file upload ────────────────────────────────────
    // Returns [storedPath|null, errorMessage|null]
    private function handleVenueImageUpload(): array {
        if (empty($_FILES['venue_image']['name'])) return [null, null]; // optional field
        $file = $_FILES['venue_image'];

        $inspection = Security::inspectImageUpload($file, ENQUIRY_UPLOAD_MAX_SIZE, ENQUIRY_UPLOAD_ALLOWED_TYPES);
        if ($inspection['errors']) return [null, implode(' ', $inspection['errors'])];
        $ext = $inspection['extension'];

        if (!is_dir(ENQUIRY_UPLOAD_DIR)) {
            mkdir(ENQUIRY_UPLOAD_DIR, 0755, true);
        }
        // Random filename — never trust the client's original filename
        // (prevents path traversal / double-extension tricks like foo.php.jpg).
        $filename = 'venue_' . bin2hex(random_bytes(12)) . '.' . $ext;
        $dest     = ENQUIRY_UPLOAD_DIR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return [null, 'Could not save the uploaded image. Please try again.'];
        }
        return ['/uploads/enquiries/' . $filename, null];
    }
}
