<?php
require "../includes/auth_check.php";
require "../config/db.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST["name"]);
    $email  = trim($_POST["email"]);
    $phone  = trim($_POST["phone"]);
    $course = trim($_POST["course"]);
    $photoName = null;   // agar photo nahi di to null rahega

    // ---------- VALIDATION ----------
    if ($name === "") {
        $errors[] = "Name is required.";
    }
    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }

    // ---------- FILE UPLOAD ----------
    // agar user ne photo choose ki hai (error 0 = successfully uploaded to temp)
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
        $file    = $_FILES["photo"];
        $allowed = ["jpg", "jpeg", "png", "gif"];
        $ext     = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Only JPG, PNG, GIF images are allowed.";
        } elseif ($file["size"] > 2 * 1024 * 1024) {   // 2 MB limit
            $errors[] = "Image must be less than 2MB.";
        } else {
            // unique naam banao taake do files clash na karein
            $photoName = uniqid("std_", true) . "." . $ext;
            move_uploaded_file($file["tmp_name"], "../uploads/" . $photoName);
        }
    }

    // ---------- INSERT ----------
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO students (name, email, phone, course, photo) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $name, $email, $phone, $course, $photoName);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php?msg=Student added successfully");
        exit;
    }
}

include "../includes/header.php";
?>

<div class="card">
    <h2>Add Student</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- photo upload ke liye enctype ZAROORI hai -->
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Course</label>
            <input type="text" name="course" value="<?= htmlspecialchars($_POST['course'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="photo" accept="image/*">
        </div>
        <button type="submit" class="btn">Save</button>
        <a href="index.php" class="btn btn-sm">Cancel</a>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
