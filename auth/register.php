<?php
require "../config/db.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Input lo aur extra spaces hatao
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirm_password"];

    // VALIDATION
    if ($name === "") {
        $errors[] = "Name is required.";
    }
    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Email pehle se to nahi hai?
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered.";
        }
        $stmt->close();
    }

    // Sab sahi? to insert karo
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed);

        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}

include "../includes/header.php";
?>

<div class="card">
    <h2>Create Account</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <a href="login.php" class="btn">Go to Login</a>
    <?php else: ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password">
            </div>
            <button type="submit" class="btn">Register</button>
        </form>

        <p class="mt">Already have an account? <a href="login.php">Login here</a></p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
