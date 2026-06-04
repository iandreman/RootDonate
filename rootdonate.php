<?php
// ==================== DEBUG VERSION ====================
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'status' => 'success',
    'message' => 'PHP is working! 🎉',
    'debug' => [
        'php_version' => PHP_VERSION,
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
        'post_data' => file_get_contents('php://input')
    ]
]);