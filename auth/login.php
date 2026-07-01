<?php
session_start();                 // session shuru karo (sabse upar, kuch bhi output se pehle)
require "../config/db.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validation
    if ($email === "" || $password === "") {
        $errors[] = "Email and password are required.";
    }

    if (empty($errors)) {
        // Email se user dhoondo
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Password check karo (hash se compare)
            if (password_verify($password, $user["password"])) {
                // Sahi! session me user ki info save karo
                $_SESSION["user_id"]   = $user["id"];
                $_SESSION["user_name"] = $user["name"];

                header("Location: /students/index.php");  // students page pe bhejo
                exit;
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
}

include "../includes/header.php";
?>

<div class="card">
    <h2>Login</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password">
        </div>
        <button type="submit" class="btn">Login</button>
    </form>

    <p class="mt">No account? <a href="register.php">Register here</a></p>
</div>

<?php include "../includes/footer.php"; ?>
