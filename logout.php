<?php
session_start();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
//This step deletes the session cookie from the browser

session_destroy();
//Same as always — send the user away and immediately stop PHP execution with exit.

header('Location: login.php');
exit;


?>