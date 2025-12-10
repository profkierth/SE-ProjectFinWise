<?php
session_start();
if(!isset($_SESSION['user'])){ header('Location:index.php'); exit; }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - FinWise</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="gradient">

<div class="nav">
    <a href="dashboard.php" class="active">Home</a>
    <a href="analysis.php">Charts</a>
    <a href="notifications.php">Notifications</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="dashboard-container">
    <h2 class="dash-title">Welcome, <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['user']); ?> 👋</h2>

    <div class="summary-grid">

        <div class="summary-card balance">
            <h3>Total Balance</h3>
            <p class="amount">₱5,600</p>
            <small>as of today</small>
        </div>

        <div class="summary-card income">
            <h3>Total Income</h3>
            <p class="amount">₱8,000</p>
            <small>monthly</small>
        </div>

        <div class="summary-card expense">
            <h3>Total Expense</h3>
            <p class="amount">₱2,300</p>
            <small>monthly</small>
        </div>

        <div class="summary-card savings">
            <h3>Savings</h3>
            <p class="amount">₱3,700</p>
            <small>estimation</small>
        </div>

    </div>

    <div class="dashboard-charts">
        <div class="chart-box small">
            <canvas id="miniBar"></canvas>
        </div>
        <div class="chart-box small">
            <canvas id="miniPie"></canvas>
        </div>
    </div>

</div>

<script>
new Chart(document.getElementById('miniBar'), {
    type: 'bar',
    data: {
        labels: ['Income','Expense'],
        datasets: [{ label: '₱', data: [8000,2300], backgroundColor: ['#2ecc71','#e74c3c'] }]
    },
    options: { responsive:true, plugins:{ legend:{display:false} } }
});

new Chart(document.getElementById('miniPie'), {
    type: 'doughnut',
    data: {
        labels:['Savings','Expenses','Others'],
        datasets:[{ data:[3700,2300,600], backgroundColor:['#3498db','#e74c3c','#f1c40f'] }]
    },
    options:{ responsive:true, plugins:{ legend:{position:'bottom'} } }
});
</script>

</body>
</html>
