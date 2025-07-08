<?php
// Simple log management script
$logFile = 'storage/logs/laravel.log';

if (isset($argv[1])) {
    switch ($argv[1]) {
        case 'clear':
            file_put_contents($logFile, '');
            echo "Log file cleared.\n";
            break;
        case 'view':
            if (file_exists($logFile)) {
                $content = file_get_contents($logFile);
                // Convert to UTF-8 for better readability
                $content = mb_convert_encoding($content, 'UTF-8', 'auto');
                echo $content;
            } else {
                echo "Log file not found.\n";
            }
            break;
        case 'tail':
            if (file_exists($logFile)) {
                $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $count = isset($argv[2]) ? intval($argv[2]) : 10;
                $lastLines = array_slice($lines, -$count);
                foreach ($lastLines as $line) {
                    echo $line . "\n";
                }
            } else {
                echo "Log file not found.\n";
            }
            break;
        default:
            echo "Usage: php clear_logs.php [clear|view|tail] [number_of_lines]\n";
    }
} else {
    echo "Usage: php clear_logs.php [clear|view|tail] [number_of_lines]\n";
    echo "Examples:\n";
    echo "  php clear_logs.php clear      - Clear the log file\n";
    echo "  php clear_logs.php view       - View entire log file\n";
    echo "  php clear_logs.php tail 20    - View last 20 lines\n";
} 