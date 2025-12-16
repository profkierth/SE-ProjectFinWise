<?php
session_start();
require 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {

    $category_id = (int) $_POST['category_id'];
    $amount = (float) $_POST['amount'];

    $check = $conn->prepare("SELECT id FROM balances WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$exists) {
        $stmt = $conn->prepare("INSERT INTO balances (user_id, amount) VALUES (?, 0)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }


    if ($category_id > 0 && $amount > 0) {

        
        $cat = $conn->prepare("
            SELECT name, type 
            FROM categories 
            WHERE id = ? AND user_id = ?
        ");
        $cat->bind_param("ii", $category_id, $user_id);
        $cat->execute();
        $category = $cat->get_result()->fetch_assoc();
        $cat->close();

        if (!$category) {
            header("Location: transactions.php");
            exit;
        }

        
        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, category_id, amount)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iid", $user_id, $category_id, $amount);
        $stmt->execute();
        $stmt->close();

        if ($category['type'] === 'income') {
        $bal = $conn->prepare("UPDATE balances SET amount = amount + ? WHERE user_id = ?");
        $bal->bind_param("di", $amount, $user_id);
        $bal->execute();
        $bal->close();
        } elseif ($category['type'] === 'expense') {
        $bal = $conn->prepare("UPDATE balances SET amount = amount - ? WHERE user_id = ?");
        $bal->bind_param("di", $amount, $user_id);
        $bal->execute();
        $bal->close();
        }




        $type  = $category['type']; 
        $title = ucfirst($type) . " Added";

        $message = ($type === 'income')
            ? "You added income of ₱" . number_format($amount, 2) . " under " . $category['name']
            : "You added expense of ₱" . number_format($amount, 2) . " for " . $category['name'];

        $notif = $conn->prepare("
            INSERT INTO notifications (user_id, type, title, message)
            VALUES (?, ?, ?, ?)
        ");
        $notif->bind_param("isss", $user_id, $type, $title, $message);
        $notif->execute();
        $notif->close();

        header("Location: transactions.php");
        exit;
    }
}

$stmt = $conn->prepare("
    SELECT 
        COALESCE(SUM(CASE WHEN c.type='income' THEN t.amount END),0) AS income,
        COALESCE(SUM(CASE WHEN c.type='expense' THEN t.amount END),0) AS expense
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totals = $stmt->get_result()->fetch_assoc();
$stmt->close();

$income  = $totals['income'];
$expense = $totals['expense'];

$stmt = $conn->prepare("
    SELECT IFNULL(SUM(amount),0) AS total
    FROM balances
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$balance = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();


$stmt = $conn->prepare("
    SELECT 
        t.id,
        t.amount,
        t.created_at,
        c.name AS category,
        c.type
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
    ORDER BY t.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$transactions = $stmt->get_result();


$stmt = $conn->prepare("
    SELECT id, name, type
    FROM categories
    WHERE user_id = ?
    ORDER BY type, name
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$categories = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Transactions</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body.gradient {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #008080, #003f3f);
    min-height: 100vh;
    color: white;
}

.page-title {
    text-align: center;
    margin: 25px 0;
    font-size: 28px;
}
.bottom-nav { 
    position: fixed; bottom: 0; left: 0; 
    width: 100%; background: #ffffff; 
    display: flex; 
    justify-content: space-around; 
    padding: 12px 0; 
    box-shadow: 0 -5px 20px rgba(0,0,0,.15); 
    z-index: 100; 
} 
.bottom-nav a { 
    text-decoration: none; 
    color: #555; 
    font-size: 13px; 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 4px; 
    transition: .3s; 
}
 .bottom-nav a i { 
    font-size: 18px; 
}
.bottom-nav a.active,
.bottom-nav a:hover { 
    color: #1abc9c; 
    transform: translateY(-3px); }

.summary {
    display: flex;
    justify-content: space-around;
    margin: 20px auto;
    max-width: 600px;
    background: rgba(255,255,255,0.15);
    padding: 15px;
    border-radius: 15px;
    backdrop-filter: blur(6px);
}

.summary p {
    margin: 0;
    font-weight: 600;
}


.tx-top {
    display: flex;
    justify-content: center;
    margin: 25px 0;
}

.add-form {
    display: flex;
    gap: 10px;
    background: rgba(255,255,255,0.15);
    padding: 15px;
    border-radius: 15px;
    backdrop-filter: blur(6px);
}

.add-form select,
.add-form input {
    padding: 10px;
    border-radius: 8px;
    border: none;
    outline: none;
}

.add-btn {
    background: #00c2c2;
    border: none;
    color: #003f3f;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
}

.add-btn:hover {
    background: #00e0e0;
}


.tx-container {
    max-width: 600px;
    margin: auto;
    padding-bottom: 50px;
}

.tx-card {
    background: rgba(255,255,255,0.18);
    border-radius: 15px;
    padding: 15px 18px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    backdrop-filter: blur(6px);
    transition: 0.2s;
}

.tx-card:hover {
    transform: translateY(-3px);
    background: rgba(255,255,255,0.25);
}

.tx-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.tx-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}

.tx-icon.income {
    background: #2ecc71;
    color: #004d25;
}

.tx-icon.expense {
    background: #e74c3c;
    color: #4d0000;
}

.tx-card h4 {
    margin: 0;
    font-size: 16px;
}

.tx-card small {
    opacity: 0.8;
}
</style>
</head>

<body class="gradient">

<h2 class="page-title">Transactions</h2>


<div class="summary">
    <p>Income<br>₱<?= number_format($income,2) ?></p>
    <p>Expense<br>₱<?= number_format($expense,2) ?></p>
    <p><strong>Balance<br>₱<?= number_format($balance,2) ?></strong></p>
</div>

<div class="tx-top">
<form method="POST" class="add-form">
    <select name="category_id" required>
        <option value="">Select category</option>
        <?php while($c = $categories->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>">
                <?= ucfirst($c['type']) ?> - <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <input type="number" step="0.01" name="amount" placeholder="Amount" required>

    <button type="submit" name="add" class="add-btn">
        <i class="fa-solid fa-plus"></i>
    </button>
</form>
</div>


<div class="tx-container">

<?php while($t = $transactions->fetch_assoc()): ?>
<div class="tx-card">

    <div class="tx-left">
        <div class="tx-icon <?= $t['type'] ?>">
            <?= $t['type'] === 'income' ? '↑' : '↓' ?>
        </div>

        <div>
            <h4><?= htmlspecialchars($t['category']) ?></h4>
            <small><?= date("M d, Y", strtotime($t['created_at'])) ?></small>
        </div>
    </div>

    <strong>₱<?= number_format($t['amount'],2) ?></strong>

</div>
<?php endwhile; ?>

<?php if($transactions->num_rows === 0): ?>
<p style="text-align:center;">No transactions yet</p>
<?php endif; ?>

</div>

<div class="bottom-nav"> 
    <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>Home</span></a> 
    <a href="transactions.php" class="active"><i class="fa-solid fa-wallet"></i><span>Transactions</span></a> 
    <a href="analysis.php"><i class="fa-solid fa-chart-pie"></i><span>Analysis</span></a> 
    <a href="categories.php"><i class="fa-solid fa-tags"></i><span>Categories</span></a> 
    <a href="profile.php"><i class="fa-solid fa-user"></i><span>Profile</span></a> </div>
</body>
</html>
