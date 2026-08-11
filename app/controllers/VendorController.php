<?php
declare(strict_types=1);

final class VendorController {
    private const CATEGORIES = ['Catering','Entertainment','Photography','Decoration','Music Performers','Guest Services','Other'];

    public function signup(): void {
        $success = '';
        $error = '';
        $values = ['business_name'=>'','contact_name'=>'','email'=>'','phone'=>'','service_category'=>'','city'=>'Chennai','website'=>'','note'=>''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['company_url'])) {
                $success = 'Application received.';
            } elseif (!Security::verifyCsrf()) {
                http_response_code(403);
                $error = 'Your session expired. Reload the page and try again.';
            } elseif (!Security::checkRateLimit('vendor_signup', Security::getIp())) {
                http_response_code(429);
                $error = 'Too many attempts. Please wait a minute and try again.';
            } else {
                foreach (array_keys($values) as $field) $values[$field] = Security::sanitizeString($_POST[$field] ?? '', $field === 'note' ? 800 : 180);
                $values['email'] = Security::sanitizeEmail($_POST['email'] ?? '') ?: '';
                $values['phone'] = Security::sanitizePhone($_POST['phone'] ?? '');
                if ($values['business_name'] === '' || $values['contact_name'] === '' || $values['email'] === '' || !Security::validatePhone($values['phone'])) {
                    $error = 'Enter the business name, contact person, valid email and valid phone number.';
                } elseif (!in_array($values['service_category'], self::CATEGORIES, true)) {
                    $error = 'Choose a valid service category.';
                } elseif ($values['website'] !== '' && !filter_var($values['website'], FILTER_VALIDATE_URL)) {
                    $error = 'Enter a complete website URL, including https://, or leave it blank.';
                } else {
                    try {
                        Database::query('INSERT INTO vendor_applications (business_name,contact_name,email,phone,service_category,city,website,note,ip_hash) VALUES (?,?,?,?,?,?,?,?,?)', [
                            $values['business_name'], $values['contact_name'], $values['email'], $values['phone'], $values['service_category'], $values['city'], $values['website'] ?: null, $values['note'] ?: null, hash('sha256', Security::getIp()),
                        ]);
                        $success = 'Application received. Our vendor team will contact you after verification.';
                        foreach ($values as $key => $_) $values[$key] = $key === 'city' ? 'Chennai' : '';
                    } catch (Throwable $exception) {
                        error_log('Vendor signup failed: ' . $exception->getMessage());
                        $error = 'We could not save the application right now. Please try again shortly.';
                    }
                }
            }
        }

        $categories = self::CATEGORIES;
        require VIEWS_PATH . '/pages/vendor_signup.php';
    }
}
