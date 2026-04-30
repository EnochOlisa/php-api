<?php
//Session Configuration. Will be included at the very top of your entry points.

// Force strict session security settings in the PHP ini
ini_set('session.use_only_cookies', 1); // Prevents session ID passing in URLs
ini_set('session.use_strict_mode', 1);   // Prevents the use of uninitialized session IDs

// Define our cookie policy, 'Lax' or 'Strict' is required to prevent CSRF.
$session_options = [
    'lifetime' => 3600,             // 1 hour, adjust as needed
    'path'     => '/',               // Available across the whole domain
    'domain'   => '',                // Leave empty for current domain
    'secure'   => true,              // Only send over HTTPS (Set to false if testing on localhost without SSL)
    'httponly' => true,              // Prevents JavaScript from reading the session ID
    'samesite' => 'Lax'              // Protects against Cross-Site Request Forgery
];

// Apply the settings
session_set_cookie_params($session_options);

// Set a custom session name (optional, but hides that you're using PHP)
session_name('__Secure-API-SID');

//Start the session safely
function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
?>