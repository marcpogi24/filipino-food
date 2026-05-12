<?php
session_start();

// 1. Burahin lahat ng session variables
$_SESSION = array();

// 2. Burahin ang session cookie mismo sa browser para siguradong logout
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Patayin ang session sa server
session_destroy();

// 4. I-redirect pabalik sa login page
header("Location: login.php");
exit();
?>