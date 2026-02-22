<?php
// // Database configuration
// define('DB_HOST', 'sql201.infinityfree.com'); // From InfinityFree
// define('DB_USER', 'if0_41123391');    // Your DB username
// define('DB_PASS', 'VDVlAdM4zS');     // Your DB password
// define('DB_NAME', 'epiz_12345678_election');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password (usually empty for XAMPP)
define('DB_NAME', 'election_system');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>