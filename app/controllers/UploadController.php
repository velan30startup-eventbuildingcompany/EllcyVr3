<?php
class UploadController {
    private const JEWELLERY_SERVICES = [
        'fake-jewellery-gold',
        'fake-jewellery-silver',
        'fake-jewellery-kundan',
    ];

    public function csrf(): void {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, max-age=0');
        echo json_encode(['success' => true, 'csrf_token' => Security::csrfToken()]);
    }

    public function jewelleryReference(): void {
        header('Content-Type: application/json; charset=UTF-8');
        Security::requireCsrf();
        if (!Security::checkRateLimit('jewellery_reference_upload', Security::getIp())) {
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Too many uploads. Please wait a minute.']);
            return;
        }

        $serviceSlug = Security::sanitizeString((string)($_POST['service_slug'] ?? ''), 80);
        if (!in_array($serviceSlug, self::JEWELLERY_SERVICES, true)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid jewellery service.']);
            return;
        }
        if (empty($_FILES['reference_image'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Choose a JPG, JPEG, PNG or WebP image.']);
            return;
        }

        try {
            Upload::purgeExpired();
            $stored = Upload::createJewelleryReference($_FILES['reference_image'], $serviceSlug);
            echo json_encode([
                'success' => true,
                'token' => $stored['token'],
                'preview_url' => Router::url($stored['path']),
            ]);
        } catch (InvalidArgumentException $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } catch (Throwable $e) {
            error_log('[ELLCY] jewellery reference upload failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'The image could not be saved. Please try again.']);
        }
    }

    public function removeJewelleryReference(): void {
        header('Content-Type: application/json; charset=UTF-8');
        Security::requireCsrf();
        $token = strtolower((string)($_POST['token'] ?? ''));
        echo json_encode(['success' => Upload::removeTemporary($token)]);
    }
}
