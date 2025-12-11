<?php 
session_start();
if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - FinWise</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<!-- TOP BAR -->
<header class="top-bar">
    <div class="left-profile">
        <span class="wave">👋</span>
        <div class="profile-text">
            <h2>WELCOME <?php echo strtoupper($_SESSION['fullname']); ?>!</h2>
            <small>Good to see you again</small>
        </div>
    </div>

    <nav class="nav-links">
        <a href="dashboard.php" class="active">Home</a>
        <a href="analysis.php">Analysis</a>
        <a href="notifications.php" class="icon-link">
            <span class="notif-icon">🔔</span>
        </a>
        <a href="transaction.php">Transaction</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<!-- MAIN CONTENT -->
<div class="main-content">

    <h2 class="section-title">Your Financial Overview</h2>

    <div class="summary-grid">

        <div class="summary-card balance">
            <h3>Total Balance</h3>
            <p class="amount">₱5000</p>
            <small>as of today</small>
        </div>

        <div class="summary-card income">
            <h3>Total Income</h3>
            <p class="amount">₱8000</p>
            <small>monthly</small>
        </div>

        <div class="summary-card expense">
            <h3>Total Expense</h3>
            <p class="amount">₱3000</p>
            <small>monthly</small>
        </div>

        <div class="summary-card savings">
            <h3>Total Savings</h3>
            <p class="amount">₱2000</p>
            <small>estimation</small>
        </div>

    </div>

    <!-- CHARTS SECTION -->
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
// BAR CHART
new Chart(document.getElementById("barChart"), {
    type: "bar",
    data: {
        labels: ["Income", "Expense"],
        datasets: [{
            data: [8000, 3000],
            backgroundColor: ["#2ecc71", "#e74c3c"],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

// DONUT CHART
new Chart(document.getElementById("donutChart"), {
    type: "doughnut",
    data: {
        labels: ["Savings", "Expenses", "Others"],
        datasets: [{
            data: [2000, 3000, 1000],
            backgroundColor: ["#3498db", "#e74c3c", "#f1c40f"]
        }]
    },
    options: {
        responsive: true,
        cutout: "65%",
        plugins: { legend: { position: "bottom" } }
    }
});
</script>

</body>
</html>
