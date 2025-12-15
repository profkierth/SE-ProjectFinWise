<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("
    SELECT IFNULL(SUM(t.amount),0) total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ? AND c.type = 'income'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$income = $stmt->get_result()->fetch_assoc()['total'];


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

   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
            padding-bottom: 80px;
        }

        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .profile-text h2 {
            margin: 0;
        }

       
        .notification {
            position: fixed;
            top: 40px;
            right: 40px;
            font-size: 30px;
            color: #10b3ad;
            z-index: 1100;
        }

        @keyframes bellPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        .notification i {
            animation: bellPulse 2s infinite;
        }

        
        .main-content {
            padding: 20px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }

        .summary-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
        }

        .amount {
            font-size: 20px;
            font-weight: bold;
        }

        .charts-grid {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .chart-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
        }

        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background: #10b3ad;

            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
        }

        .bottom-nav a {
            color: #eafafa;
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

        .bottom-nav a:hover {
            transform: translateY(-4px);
            color: #fff;
        }

        .bottom-nav a.active {
            color: #fff;
            transform: translateY(-6px);
        }
    </style>
</head>

<body>


<div class="notification">
    <i class="fa-solid fa-bell"></i>
</div>

<header class="top-bar">
    <div class="left-profile">
        <h2>WELCOME <?= strtoupper($_SESSION['fullname'] ?? $_SESSION['user']) ?> 👋</h2>
        <small>Good to see you again</small>
    </div>
</header>

<div class="main-content">

    <h2>Your Financial Overview</h2>

    <div class="summary-grid">
        <div class="summary-card">
            <h3>Total Balance</h3>
            <p class="amount">₱<?= number_format($balance,2) ?></p>
        </div>

        <div class="summary-card">
            <h3>Total Income</h3>
            <p class="amount">₱<?= number_format($income,2) ?></p>
        </div>

        <div class="summary-card">
            <h3>Total Expense</h3>
            <p class="amount">₱<?= number_format($expense,2) ?></p>
        </div>

        <div class="summary-card">
            <h3>Total Savings</h3>
            <p class="amount">₱<?= number_format($savings,2) ?></p>
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

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href="dashboard.php" class="active">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
    </a>

    <a href="analysis.php">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Analysis</span>
    </a>

    <a href="categories.php">
        <i class="fa-solid fa-tags"></i>
        <span>Categories</span>
    </a>

    <a href="transactions.php">
        <i class="fa-solid fa-wallet"></i>
        <span>Transactions</span>
    </a>

    <a href="profile.php">
        <i class="fa-solid fa-user"></i>
        <span>Profile</span>
    </a>
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
