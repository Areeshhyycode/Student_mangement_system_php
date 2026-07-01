<?php
// session ko safely start karo (agar pehle se start nahi hai)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/index.php" class="brand">🎓 SMS</a>
        <div class="nav-links">
            <?php if ($loggedIn): ?>
                <a href="/students/index.php">Students</a>
                <span>Hi, <?= htmlspecialchars($_SESSION["user_name"]) ?></span>
                <a href="/auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="/auth/register.php">Register</a>
                <a href="/auth/login.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container">
