<?php
echo "PHP Version: " . phpversion() . "\n";
echo "GD Extension Loaded: " . (extension_loaded('gd') ? 'YES' : 'NO') . "\n";

if (extension_loaded('gd')) {
    $gdInfo = gd_info();
    echo "GD Version: " . $gdInfo['GD Version'] . "\n";
    echo "JPEG Support: " . ($gdInfo['JPEG Support'] ? 'YES' : 'NO') . "\n";
    echo "PNG Support: " . ($gdInfo['PNG Support'] ? 'YES' : 'NO') . "\n";
    echo "GIF Support: " . ($gdInfo['GIF Read Support'] ? 'YES' : 'NO') . "\n";
} else {
    echo "GD extension is not loaded!\n";
}

// Test specific functions DomPDF uses
echo "imagecreatefromjpeg function exists: " . (function_exists('imagecreatefromjpeg') ? 'YES' : 'NO') . "\n";
echo "imagecreatefrompng function exists: " . (function_exists('imagecreatefrompng') ? 'YES' : 'NO') . "\n";
echo "getimagesize function exists: " . (function_exists('getimagesize') ? 'YES' : 'NO') . "\n";
?> 