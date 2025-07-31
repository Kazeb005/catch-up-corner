<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'catch_up_corner');

// Start session with basic settings
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>