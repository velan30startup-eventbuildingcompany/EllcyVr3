<?php
/**
 * ELLCY — Boot file
 * Include this at the top of every PHP page.
 */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/helpers/Security.php';

Security::startSession();
Security::setHeaders();

// Load site settings from DB (with fallback defaults)
function ellcy_settings(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    try {
        $rows  = Database::fetchAll('SELECT setting_key, setting_val FROM site_settings');
        $cache = array_column($rows, 'setting_val', 'setting_key');
    } catch (Exception $e) {
        $cache = [];
    }
    return $cache;
}

function setting(string $key, string $default = ''): string {
    return ellcy_settings()[$key] ?? $default;
}
