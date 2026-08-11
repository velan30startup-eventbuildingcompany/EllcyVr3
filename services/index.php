<?php
/**
 * PHP entry point for /services/.
 *
 * The services folder also contains the static package-detail pages. On
 * XAMPP, Apache therefore treats /services as a physical directory before
 * the root router can handle it. Forward this directory URL to the normal
 * application entry point while preserving the root application base path.
 */
$applicationRoot = dirname(__DIR__);
$requestScript = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/services/index.php'));
$applicationBase = rtrim(str_replace('\\', '/', dirname(dirname($requestScript))), '/');

$_SERVER['SCRIPT_NAME'] = ($applicationBase === '' ? '' : $applicationBase) . '/index.php';
require $applicationRoot . '/index.php';
