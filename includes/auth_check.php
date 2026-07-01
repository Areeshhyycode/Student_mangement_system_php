<?php
// Ye file har protected page ke upar include hogi.
// Agar user logged in nahi hai to use login page pe bhej do.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: /auth/login.php");
    exit;
}
?>
