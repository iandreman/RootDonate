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
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    require_once __DIR__ . '/../../vendor/autoload.php';

    use App\RootDonate;

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['amount']) || $input['amount'] < 1) {
        throw new Exception('Invalid donation amount');
    }

    $rootDonate = new RootDonate();
    $result = $rootDonate->handle($input);   // ← pass data instead of reading inside

    echo json_encode($result);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
