<?php
// Start the session to access session variables
session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session itself
session_destroy();

// 4. Redirect to the login page (or public homepage)
header("Location: admin.php"); // Assuming your login page is admin_login.php
exit;
?>