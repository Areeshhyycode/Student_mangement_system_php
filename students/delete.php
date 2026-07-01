<?php
require "../includes/auth_check.php";
require "../config/db.php";

$id = (int)($_GET["id"] ?? 0);

// pehle student laao (taake uski photo file bhi delete kar sakein)
$stmt = $conn->prepare("SELECT photo FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($student) {
    // photo file bhi disk se hata do
    if ($student["photo"] && file_exists("../uploads/" . $student["photo"])) {
        unlink("../uploads/" . $student["photo"]);
    }

    // ab database se row delete karo
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php?msg=Student deleted successfully");
exit;
?>
