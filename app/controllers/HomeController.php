<?php
/**
 * ELLCY — HomeController
 */
class HomeController {
    public function index(): void {
        // The homepage must never wait for the remote database. Contact
        // details have safe template defaults and can be refreshed on the
        // database-backed pages when required.
        $settings = [];
        $extra_js = ['script.js'];
        require VIEWS_PATH . '/pages/home.php';
    }
}
