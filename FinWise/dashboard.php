<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* TOTAL INCOME */
$stmt = $conn->prepare("
    SELECT IFNULL(SUM(t.amount),0) total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ? AND c.type = 'income'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$income = $stmt->get_result()->fetch_assoc()['total'];

/* TOTAL EXPENSE */
$stmt = $conn->prepare("
    SELECT IFNULL(SUM(t.amount),0) total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ? AND c.type = 'expense'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expense = $stmt->get_result()->fetch_assoc()['total'];


$balance = $income - $expense;
$savings = $balance > 0 ? $balance : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - FinWise</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<header class="top-bar">
    <div class="left-profile">
        <span class="wave">👋</span>
        <div class="profile-text">
            <h2>WELCOME <?= strtoupper($_SESSION['fullname'] ?? $_SESSION['user']) ?>!</h2>
            <small>Good to see you again</small>
        </div>
    </div>

    <nav class="nav-links">
        <a href="dashboard.php" class="active">Home</a>
        <a href="analysis.php">Analysis</a>
        <a href="notifications.php">🔔</a>
        <a href="transactions.php">Transactions</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="main-content">

<h2 class="section-title">Your Financial Overview</h2>

<div class="summary-grid">

    <div class="summary-card balance">
        <h3>Total Balance</h3>
        <p class="amount">₱<?= number_format($balance,2) ?></p>
        <small>as of today</small>
    </div>

    <div class="summary-card income">
        <h3>Total Income</h3>
        <p class="amount">₱<?= number_format($income,2) ?></p>
        <small>overall</small>
    </div>

    <div class="summary-card expense">
        <h3>Total Expense</h3>
        <p class="amount">₱<?= number_format($expense,2) ?></p>
        <small>overall</small>
    </div>

    <div class="summary-card savings">
        <h3>Total Savings</h3>
        <p class="amount">₱<?= number_format($savings,2) ?></p>
        <small>estimated</small>
    </div>

</div>

<div class="charts-grid">
    <div class="chart-box">
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-box">
        <canvas id="donutChart"></canvas>
    </div>
</div>

</div>

<script>
const income = <?= $income ?>;
const expense = <?= $expense ?>;
const savings = <?= $savings ?>;

new Chart(document.getElementById("barChart"), {
    type: "bar",
    data: {
        labels: ["Income", "Expense"],
        datasets: [{
            data: [income, expense],
            backgroundColor: ["#2ecc71", "#e74c3c"],
            borderRadius: 10
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById("donutChart"), {
    type: "doughnut",
    data: {
        labels: ["Savings", "Expenses"],
        datasets: [{
            data: [savings, expense],
            backgroundColor: ["#3498db", "#e74c3c"]
        }]
    },
    options: {
        cutout: "65%",
        plugins: { legend: { position: "bottom" } }
    }
});
</script>

</body>
</html>
