<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}


$check = $conn->prepare("SELECT id FROM users WHERE id=?");
$check->bind_param("i", $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    session_destroy();
    header("Location: index.php");
    exit;
}


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


if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['delete'])) {

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die("Invalid request");
    }

    $id = (int) $_POST['delete'];
    $stmt = $conn->prepare(
        "DELETE FROM categories WHERE id=? AND user_id=?"
    );
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}


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

<div class="top-header">
    <h2 class="page-title">Categories</h2>
    <a href="notification.php" class="notif-btn">
        <i class="fa-solid fa-bell"></i>
        <span class="notif-dot"></span>
    </a>
</div>

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

<div class="tx-container">
<?php while ($c = $categories->fetch_assoc()): ?>
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

<div class="bottom-nav">
    <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>Home</span></a>
    <a href="transactions.php"><i class="fa-solid fa-wallet"></i><span>Transactions</span></a>
    <a href="analysis.php"><i class="fa-solid fa-chart-pie"></i><span>Analysis</span></a>
    <a href="profile.php"><i class="fa-solid fa-user"></i><span>Profile</span></a>
</div>

</body>
</html>
