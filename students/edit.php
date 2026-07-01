<?php
require "../includes/auth_check.php";
require "../config/db.php";

$errors = [];
$id = (int)($_GET["id"] ?? 0);

// pehle is student ko database se laao
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// agar student mila hi nahi to list pe wapas
if (!$student) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST["name"]);
    $email  = trim($_POST["email"]);
    $phone  = trim($_POST["phone"]);
    $course = trim($_POST["course"]);
    $photoName = $student["photo"];   // default: purani photo hi rakho

    // VALIDATION
    if ($name === "") {
        $errors[] = "Name is required.";
    }
    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }

    // Agar nayi photo upload ki gayi
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
        $file    = $_FILES["photo"];
        $allowed = ["jpg", "jpeg", "png", "gif"];
        $ext     = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Only JPG, PNG, GIF images are allowed.";
        } elseif ($file["size"] > 2 * 1024 * 1024) {
            $errors[] = "Image must be less than 2MB.";
        } else {
            $photoName = uniqid("std_", true) . "." . $ext;
            move_uploaded_file($file["tmp_name"], "../uploads/" . $photoName);

            // purani photo file delete kar do (space bachane ke liye)
            if ($student["photo"] && file_exists("../uploads/" . $student["photo"])) {
                unlink("../uploads/" . $student["photo"]);
            }
        }
    }

    // UPDATE
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE students SET name=?, email=?, phone=?, course=?, photo=? WHERE id=?"
        );
        $stmt->bind_param("sssssi", $name, $email, $phone, $course, $photoName, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php?msg=Student updated successfully");
        exit;
    }

    // agar error hai to form me user ka type kiya data dikhane ke liye update kar do
    $student["name"]   = $name;
    $student["email"]  = $email;
    $student["phone"]  = $phone;
    $student["course"] = $course;
}

include "../includes/header.php";
?>

<div class="card">
    <h2>Edit Student</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" value="<?= htmlspecialchars($student['email']) ?>">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>">
        </div>
        <div class="form-group">
            <label>Course</label>
            <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <?php if ($student["photo"]): ?>
                <div class="mt"><img src="/uploads/<?= htmlspecialchars($student['photo']) ?>" class="avatar"></div>
            <?php endif; ?>
            <input type="file" name="photo" accept="image/*">
            <small>Leave empty to keep current photo.</small>
        </div>
        <button type="submit" class="btn">Update</button>
        <a href="index.php" class="btn btn-sm">Cancel</a>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
