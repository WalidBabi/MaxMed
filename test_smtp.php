<?php
$fp = fsockopen('localhost', 25, $errno, $errstr, 30);
if (!$fp) {
    echo "ERROR: $errno - $errstr\n";
} else {
    $response = fgets($fp, 515);
    echo "Server response: " . $response . "\n";
    
    // Send HELO command
    fwrite($fp, "HELO localhost\r\n");
    $response = fgets($fp, 515);
    echo "HELO response: " . $response . "\n";
    
    fclose($fp);
}
?> 