<?php
$logFile = "/var/log/rathole.log";

// Handle AJAX requests for log content only
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: text/plain; charset=UTF-8');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        if (empty($logContent)) {
            echo "Log file is empty.";
        } else {
            echo $logContent;
        }
    } else {
        echo "Log file not found.";
    }
    exit;
}

// Regular page load
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    if (empty($logContent)) {
        $logContent = "Log file is empty.";
    }
} else {
    $logContent = "Log file not found.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>rathole live logs</title>
    <style>
      body { margin: 0; padding: 0; font-family: monospace; }
      .log-content {
        padding: 10px;
        border-radius: 4px;
        font-family: monospace;
        white-space: pre-wrap;
        height: 100vh;
        overflow-y: auto;
        border: 1px solid #555;
        box-sizing: border-box;
        overflow-anchor: none;
      }
    </style>
</head>
<body>
    <div class="log-content" id="logContent"><?= $logContent ?></div>

    <script>
        let stickToBottom = true;

        function distanceFromBottom(el) {
            return el.scrollHeight - el.scrollTop - el.clientHeight;
        }

        function updateLogs() {
            const logContainer = document.getElementById('logContent');
            const offsetFromBottom = distanceFromBottom(logContainer);

            fetch('?ajax=1', { cache: 'no-store' })
                .then(response => response.text())
                .then(data => {
                    if (logContainer.textContent === data) return;

                    logContainer.textContent = data;

                    if (stickToBottom) {
                        logContainer.scrollTop = logContainer.scrollHeight;
                    }
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const logContainer = document.getElementById('logContent');
            // Start at bottom
            logContainer.scrollTop = logContainer.scrollHeight;
            stickToBottom = true;

            // Toggle auto-scroll based on whether user is at the bottom
            logContainer.addEventListener('scroll', () => {
                stickToBottom = distanceFromBottom(logContainer) <= 3;
            });
        });

        // Update logs every second
        setInterval(updateLogs, 1000);
    </script>
</body>
</html>
