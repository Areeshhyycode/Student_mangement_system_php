<?php
require "../includes/auth_check.php";   // sirf logged-in user dekh sakta hai
require "../config/db.php";

// ---------- SEARCH ----------
$search = trim($_GET["q"] ?? "");

// ---------- PAGINATION ----------
$perPage = 5;                               // ek page pe kitne students
$page    = max(1, (int)($_GET["page"] ?? 1)); // current page (kam se kam 1)
$offset  = ($page - 1) * $perPage;          // kahan se rows lena shuru karo

// Agar search hai to LIKE condition banao
$where  = "";
$params = [];
$types  = "";
if ($search !== "") {
    $where  = "WHERE name LIKE ? OR email LIKE ? OR course LIKE ?";
    $like   = "%" . $search . "%";
    $params = [$like, $like, $like];
    $types  = "sss";
}

// ---------- TOTAL ROWS COUNT (pagination ke liye) ----------
$countSql  = "SELECT COUNT(*) AS total FROM students $where";
$countStmt = $conn->prepare($countSql);
if ($search !== "") {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows  = $countStmt->get_result()->fetch_assoc()["total"];
$totalPages = max(1, ceil($totalRows / $perPage));
$countStmt->close();

// ---------- ACTUAL ROWS (is page ke liye) ----------
$sql  = "SELECT * FROM students $where ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// search params + limit/offset bind karo
$allParams = $params;
$allParams[] = $perPage;
$allParams[] = $offset;
$allTypes    = $types . "ii";
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$students = $stmt->get_result();

include "../includes/header.php";
?>

<div class="topbar">
    <h2>Students</h2>
    <a href="create.php" class="btn">+ Add Student</a>
</div>

<form method="GET" class="search-box">
    <input type="text" name="q" placeholder="Search name, email, course..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn">Search</button>
    <?php if ($search !== ""): ?>
        <a href="index.php" class="btn btn-sm">Clear</a>
    <?php endif; ?>
</form>

<?php if (isset($_GET["msg"])): ?>
    <div class="alert alert-success mt"><?= htmlspecialchars($_GET["msg"]) ?></div>
<?php endif; ?>

<table class="mt">
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Course</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($students->num_rows === 0): ?>
            <tr><td colspan="6" class="text-center">No students found.</td></tr>
        <?php else: ?>
            <?php while ($row = $students->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if ($row["photo"]): ?>
                            <img src="/uploads/<?= htmlspecialchars($row["photo"]) ?>" class="avatar" alt="photo">
                        <?php else: ?>
                            <span>—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["course"]) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row["id"] ?>" class="btn btn-sm">Edit</a>
                        <a href="delete.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this student?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- PAGINATION LINKS -->
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"
               class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php
$stmt->close();
include "../includes/footer.php";
?>
