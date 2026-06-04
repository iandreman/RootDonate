<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $log = [
        'received_at' => date('c'),
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR']
    ];
    
    // Append to log file
    $logFile = 'root-live-logs.json';
    $logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
    $logs[] = $log;
    
    // Keep last 300 entries
    if (count($logs) > 300) array_shift($logs);
    
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Received by Root'
    ]);
} else {
    echo json_encode(['status' => 'ok', 'message' => 'Root Live Sync Receiver Ready']);
}
?>
