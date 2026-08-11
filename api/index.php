<?php
/**
 * Vercel PHP function entry point.
 *
 * XAMPP/Apache continues to use the root front controller. Vercel invokes
 * this adapter and the existing application remains the routing authority.
 */
declare(strict_types=1);

require dirname(__DIR__) . '/index.php';
