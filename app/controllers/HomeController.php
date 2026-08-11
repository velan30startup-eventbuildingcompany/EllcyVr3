<?php
/**
 * ELLCY — HomeController
 */
class HomeController {
    public function index(): void {
        $settings = $this->getSettings();
        $extra_js = ['script.js'];
        require VIEWS_PATH . '/pages/home.php';
    }

    private function getSettings(): array {
        try {
            $rows = Database::fetchAll('SELECT setting_key, setting_val FROM site_settings');
            return array_column($rows, 'setting_val', 'setting_key');
        } catch (Exception $e) { return []; }
    }
}
