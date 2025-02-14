<?php
// Error reporting - disable in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Constants for rate limiting
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 minutes in seconds

// Get admin password from environment variable with fallback
$adminPassword = getenv('ADMIN_PASSWORD');
if (!$adminPassword) {
    error_log("Warning: ADMIN_PASSWORD environment variable not set, using default");
    $adminPassword = "SetSomethingStrongHere";
}

// Database configuration with restricted permissions
$dbfile = __DIR__ . '/visitor_signin.db';
$dbPermissions = 0600; // Read/write for owner only

// Ensure database file has correct permissions
if (file_exists($dbfile)) {
    chmod($dbfile, $dbPermissions);
}

// Function to validate and sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to validate visitor name
function validate_visitor_name($name) {
    return !empty($name) && strlen($name) <= 100 && preg_match('/^[a-zA-Z0-9\s\-\'\.]+$/', $name);
}

// Function to validate contact number
function validate_contact($contact) {
    return !empty($contact) && strlen($contact) <= 20 && preg_match('/^[0-9\+\-\(\)\s]+$/', $contact);
}
?>
