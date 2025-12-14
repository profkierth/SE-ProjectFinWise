<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}


/* ADD CATEGORY */
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['add'])) {

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die("Invalid request");
    }

    $name = trim($_POST['name']);
    $type = in_array($_POST['type'], ['income','expense']) ? $_POST['type'] : null;
    $icon = trim($_POST['icon']) ?: 'fa-tags';

    if ($name && $type) {
        $stmt = $conn->prepare(
            "INSERT INTO categories (user_id, name, type, icon)
             VALUES (?,?,?,?)"
        );
        $stmt->bind_param("isss", $user_id, $name, $type, $icon);
        $stmt->execute();
    }
}


/* DELETE CATEGORY */
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['delete'])) {

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die("Invalid request");
    }

    $id = intval($_POST['delete']);
    $stmt = $conn->prepare(
        "DELETE FROM categories WHERE id=? AND user_id=?"
    );
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}


/* FETCH CATEGORIES */
$stmt = $conn->prepare(
    "SELECT * FROM categories WHERE user_id=? ORDER BY type,name"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$categories = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Categories</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="gradient">

<div class="nav">
    <a href="dashboard.php">Home</a>
    <a href="transactions.php">Transactions</a>
    <a href="category.php" class="active">Categories</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<h2 class="page-title">Categories</h2>

<!-- ADD CATEGORY -->
<div class="card" style="max-width:420px;margin-top:20px;">
<h3>Add Category</h3>
<form method="POST">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
    <input type="hidden" name="add" value="1">

    <input type="text" name="name" required>
    <select name="type" required>
        <option value="income">Income</option>
        <option value="expense">Expense</option>
    </select>
    <input type="text" name="icon" placeholder="fa-tags">
    <button>Add Category</button>
</form>
</div>

<!-- CATEGORY LIST -->
<div class="tx-container">
<?php while($c=$categories->fetch_assoc()): ?>
<div class="tx-card">
    <div class="tx-icon <?= $c['type'] ?>">
        <i class="fa-solid <?= $c['icon'] ?>"></i>
    </div>

    <div class="tx-info">
        <h3><?= htmlspecialchars($c['name']) ?></h3>
        <small><?= ucfirst($c['type']) ?></small>
    </div>

    <form method="POST">
    <input type="hidden" name="delete" value="<?= $c['id'] ?>">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
    <button class="tx-delete">
        <i class="fa-solid fa-trash"></i>
    </button>
</form>

</div>
<?php endwhile; ?>
</div>

</body>
</html>
