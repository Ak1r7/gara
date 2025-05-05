<?php
session_start();

// Șterge toate datele sesiunii
$_SESSION = array();

// Șterge cookie-ul sesiunii
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distruge sesiunea
session_destroy();

// Redirect către pagina de login
header("Location: ../auth/login.php");
exit;
?>