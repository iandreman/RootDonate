<?php
// public/api/rootdonate.php

declare(strict_types=1);

// CORS + Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php'; // Composer
// require_once __DIR__ . '/../../src/RootDonate.php'; // without Composer

use App\RootDonate;

$rootDonate = new RootDonate();
$rootDonate->handle();