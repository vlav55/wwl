<?php
include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
//chdir("../d/1000/");
chdir("../d/2338935559/"); //BERMUDA
include "init.inc.php";



// Configuration
$botToken = $tg_bog_msg;

// Function to validate Telegram Mini App
function isTelegramMiniApp($initData, $botToken) {
    if (empty($initData)) {
        return false;
    }
    
    parse_str($initData, $data);
    $hash = $data['hash'] ?? '';
    unset($data['hash']);
    
    ksort($data);
    $dataCheckArr = [];
    foreach ($data as $key => $value) {
        $dataCheckArr[] = $key . '=' . $value;
    }
    $dataCheckString = implode("\n", $dataCheckArr);
    
    $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
    $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);
    
    return hash_equals($calculatedHash, $hash);
}

// Check if data was sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $initData = $_POST['initData'] ?? '';
    
    if (isTelegramMiniApp($initData, $botToken)) {
        parse_str($initData, $data);
        $user = json_decode($data['user'] ?? '{}', true);
        
        echo "<h2>✅ Running in Telegram Mini App</h2>";
        echo "<p><strong>User ID:</strong> " . ($user['id'] ?? 'N/A') . "</p>";
        echo "<p><strong>Username:</strong> @" . ($user['username'] ?? 'N/A') . "</p>";
        echo "<p><strong>Name:</strong> " . ($user['first_name'] ?? 'N/A') . "</p>";
    } else {
        echo "<h2>❌ NOT running in Telegram Mini App</h2>";
        echo "<p>Invalid or missing initialization data</p>";
        echo "<p>InitData received: " . htmlspecialchars(substr($initData, 0, 100)) . "...</p>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Telegram Mini App Detector</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
</head>
<body>
    <h1>Telegram Mini App Detector</h1>
    <div id="result">Checking...</div>

    <script>
        // Wait for Telegram WebApp to be ready
        setTimeout(() => {
            const tg = window.Telegram.WebApp;
            
            // Expand the app
            tg.expand();
            
            const initData = tg.initData;
            
            console.log('InitData:', initData); // Debug
            
            if (!initData) {
                document.getElementById('result').innerHTML = '<h2>❌ NOT in Telegram Mini App</h2><p>No initData available</p>';
                return;
            }
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'initData=' + encodeURIComponent(initData)
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('result').innerHTML = data;
            })
            .catch(error => {
                document.getElementById('result').innerHTML = '<h2>❌ Error</h2><p>' + error + '</p>';
            });
        }, 500);
    </script>
</body>
</html>
