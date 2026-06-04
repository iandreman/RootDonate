<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Only POST allowed']);
    exit;
}

// Read JSON from frontend
$data = json_decode(file_get_contents('php://input'), true);

echo json_encode([
    'status' => 'success',
    'message' => 'Test donation received!',
    'donation' => [
        'id' => 'TEST-' . rand(10000, 99999),
        'amount' => $data['amount'] ?? 0,
        'name' => $data['name'] ?? 'Anonymous'
    ]
]);