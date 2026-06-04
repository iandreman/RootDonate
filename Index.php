<?php
// ====================== PHP HANDLER (Top) ======================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $log = [
        'received_at' => date('c'),
        'data' => $data ?? [],
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $logFile = 'root-live-logs.json';
    $logs = [];
    
    if (file_exists($logFile)) {
        $existing = json_decode(file_get_contents($logFile), true);
        if (is_array($existing)) $logs = $existing;
    }
    
    $logs[] = $log;
    
    // Keep only last 300 entries
    if (count($logs) > 300) {
        $logs = array_slice($logs, -300);
    }
    
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Received by Root',
        'timestamp' => date('c')
    ]);
    exit; // Important: stop here for POST requests
}

// If it's a GET request → serve the HTML dashboard
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Root • Live Time</title>
    <style>
        :root {
            --primary: #00ffaa;
            --bg: #0a0a0a;
            --card: #111111;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--primary);
            text-align: center;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            overflow: hidden;
        }
        
        h1 {
            font-size: 2.1rem;
            margin: 20px 0 8px;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        
        #time {
            font-size: 6.8rem;
            font-weight: 700;
            letter-spacing: 6px;
            margin: 10px 0 20px;
            text-shadow: 0 0 40px rgba(0, 255, 170, 0.5);
        }
        
        .status {
            margin: 15px 0;
            font-size: 1.05rem;
            min-height: 32px;
        }
        
        .log {
            width: 100%;
            max-width: 620px;
            background: var(--card);
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            text-align: left;
            font-family: 'SF Mono', Monaco, monospace;
            font-size: 0.9rem;
            max-height: 420px;
            overflow-y: auto;
            border: 1px solid rgba(0, 255, 170, 0.15);
        }
        
        .success { color: #00ffaa; }
        .error   { color: #ff4444; }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.65; }
        }
    </style>
</head>
<body>
    <h1>🌐 ROOT LIVE SYNC</h1>
    <div id="time">00:00:00</div>
    <p>Syncing real-time data • Every 5 seconds</p>
    
    <div class="status" id="status">Initializing connection...</div>
    <div class="log" id="log"></div>

    <script>
        const LOG_LIMIT = 30;
        let sendInterval = null;
        
        // Make sure this points to the same file
        const AI_ENDPOINT = window.location.href.split('?')[0]; // Current page

        function updateClock() {
            const now = new Date();
            document.getElementById('time').textContent = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function logMessage(msg, type = 'info') {
            const logDiv = document.getElementById('log');
            const entry = document.createElement('div');
            const ts = new Date().toLocaleTimeString('en-GB', { hour12: false });
            
            entry.className = type;
            entry.textContent = `[${ts}] ${msg}`;
            
            logDiv.appendChild(entry);
            if (logDiv.children.length > LOG_LIMIT) {
                logDiv.removeChild(logDiv.children[0]);
            }
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        async function sendToAI() {
            const now = new Date();
            const payload = {
                timestamp: now.toISOString(),
                local_time: now.toLocaleString(),
                unix: Math.floor(now.getTime() / 1000),
                device: "iOS WebApp",
                source: "root-live-sync"
            };

            try {
                const response = await fetch(AI_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                document.getElementById('status').innerHTML = 
                    `✅ Synced <strong>${now.toLocaleTimeString()}</strong>`;
                document.getElementById('status').classList.add('pulse');
                
                logMessage(`✓ Sent successfully`, 'success');

            } catch (err) {
                document.getElementById('status').innerHTML = `❌ ${err.message}`;
                document.getElementById('status').classList.remove('pulse');
                logMessage(`Failed: ${err.message}`, 'error');
                console.error(err);
            }
        }

        function startSync() {
            updateClock();
            setInterval(updateClock, 1000);

            logMessage("🌐 Root Live Sync started", 'success');
            
            sendToAI();                    // immediate
            sendInterval = setInterval(sendToAI, 5000);
        }

        window.onload = startSync;
    </script>
</body>
</html>