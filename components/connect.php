<?php

// Load configuration
if (file_exists(__DIR__ . '/config.php')) {
    include_once __DIR__ . '/config.php';
} else {
    // Fallback or error if config.php is missing
    die('Configuration file (config.php) missing. Please create it from config.php.example.');
}

$db_name = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
$user_name = DB_USER;
$user_password = DB_PASS;

try {
    $conn = new PDO($db_name, $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>