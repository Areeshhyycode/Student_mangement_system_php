<?php
session_start();      // session kholo
session_destroy();    // saari session data mita do (logout)
header("Location: /auth/login.php");
exit;
?>
