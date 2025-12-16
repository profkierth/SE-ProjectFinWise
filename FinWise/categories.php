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
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) die("Invalid request");

    $name = trim($_POST['name']);
    $type = in_array($_POST['type'], ['income','expense']) ? $_POST['type'] : null;
    $icon = trim($_POST['icon']) ?: 'fa-tags';

    if ($name && $type) {
        $stmt = $conn->prepare(
            "INSERT INTO categories (user_id, name, type, icon) VALUES (?,?,?,?)"
        );
        $stmt->bind_param("isss", $user_id, $name, $type, $icon);
        $stmt->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['delete'])) {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) die("Invalid request");

    $id = (int) $_POST['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT * FROM categories WHERE user_id=? ORDER BY type,name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$categories = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories - FinWise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(180deg, #0fb9b1, #0a7f86);
            min-height: 100vh;
            padding-bottom: 80px;
        }
        .top-header {
            position: sticky;
            top: 0;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 18px 20px;
            z-index: 1000;
        }

        .page-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .notif-btn {
            position: absolute;
            right: 20px;
            font-size: 28px;
            color: #fff;
            cursor: pointer;
        }

        .notif-dot {
            position: absolute;
            top: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background: red;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        @keyframes bellPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        .notif-btn i {
            animation: bellPulse 2s infinite;
        }

      
        .card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            margin: 20px auto;
            max-width: 420px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin-top: 0;
        }

        .card input, .card select, .card button {
            width: 100%;
            padding: 12px 10px;
            margin: 8px 0;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }

        .card button {
            background: #0fb9b1;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .card button:hover {
            background: #0a7f86;
        }

        .tx-container {
            max-width: 420px;
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .tx-card {
            background: #fff;
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.1);
            justify-content: space-between;
        }

        .tx-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .tx-icon.income {
            background: #2ecc71;
        }

        .tx-icon.expense {
            background: #e74c3c;
        }

        .tx-info h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .tx-info small {
            color: #777;
        }

        .tx-delete {
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #e74c3c;
        }

        .tx-delete:hover {
            transform: scale(1.1);
        }

        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background: #ffffffff;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
        }

        .bottom-nav a {
            color: #555;
            text-decoration: none;
            font-size: 11px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.25s ease;
        }

        .bottom-nav a i {
            font-size: 20px;
        }

        .bottom-nav a:hover, .bottom-nav a.active {
            transform: translateY(-4px);
            color: #fff;
        }

    </style>
</head>

<body>

<div class="top-header">
    <h2 class="page-title">Categories</h2>
    <a href="notification.php" class="notif-btn">
        <i class="fa-solid fa-bell"></i>
        <span class="notif-dot"></span>
    </a>
</div>

<div class="card">
<h3>Add Category</h3>

<form method="POST">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
    <input type="hidden" name="add" value="1">

    <input type="text" name="name" required placeholder="Category Name">
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
