<?php
require "config/db.php";
include "includes/header.php";   // navbar laata hai
?>

<div class="card text-center">
    <h1>🎓 Student Management System</h1>
    <p class="mt">Welcome! Please login or create an account.</p>
    <div class="mt">
        <a href="/auth/login.php" class="btn">Login</a>
        <a href="/auth/register.php" class="btn">Register</a>
    </div>
</div>

<?php include "includes/footer.php"; ?>
