<?php
// Constants for the application

// Base path untuk file system (digunakan untuk require/include)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__FILE__)));
}

// Root path untuk URL di browser (digunakan untuk link dan assets)
if (!defined('ROOT_PATH')) {
    // Deteksi base URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = '/voting-system';
    define('ROOT_PATH', $baseUrl);
}
