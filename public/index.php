<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// The shared host rewrites the public portal into /JBI/public internally.
// Keep generated URLs and signed verification links on the public domain root.
if (in_array(strtolower($_SERVER['HTTP_HOST'] ?? ''), ['portal.jbiuniversity.com', 'www.portal.jbiuniversity.com'], true)) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['REQUEST_URI'] = preg_replace('#^/JBI/public(?=/|\\?|$)#', '', $_SERVER['REQUEST_URI'] ?? '/') ?: '/';
}

$app->handleRequest(Request::capture());
