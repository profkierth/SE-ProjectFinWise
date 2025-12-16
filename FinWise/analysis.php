<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        SUM(CASE WHEN c.type='income' THEN t.amount ELSE 0 END) AS income,
        SUM(CASE WHEN c.type='expense' THEN t.amount ELSE 0 END) AS expense
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totals = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("
    SELECT IFNULL(SUM(amount),0) AS total
    FROM balances
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$balance = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$savings = $balance > 0 ? $balance : 0;

$income  = $totals['income'] ?? 0;
$expense = $totals['expense'] ?? 0;





$daily = $conn->prepare("
    SELECT 
        DATE(t.created_at) AS label,
        SUM(t.amount) AS total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
      AND c.type = 'expense'
      AND t.created_at >= CURDATE() - INTERVAL 7 DAY
    GROUP BY label
    ORDER BY label
");




$daily->bind_param("i", $user_id);
$daily->execute();
$dailyData = $daily->get_result()->fetch_all(MYSQLI_ASSOC);


$weekly = $conn->prepare("
    SELECT 
        CONCAT('Week ', WEEK(t.created_at, 1)) AS label,
        SUM(t.amount) AS total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
      AND c.type = 'expense'
      AND t.created_at >= CURDATE() - INTERVAL 28 DAY
    GROUP BY label
    ORDER BY MIN(t.created_at)
");




$weekly->bind_param("i", $user_id);
$weekly->execute();
$weeklyData = array_reverse($weekly->get_result()->fetch_all(MYSQLI_ASSOC));


$monthly = $conn->prepare("
    SELECT 
        DATE_FORMAT(t.created_at, '%M') AS label,
        SUM(t.amount) AS total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
      AND c.type = 'expense'
      AND YEAR(t.created_at) = YEAR(CURDATE())
    GROUP BY label
    ORDER BY MIN(t.created_at)
");



$monthly->bind_param("i", $user_id);
$monthly->execute();
$monthlyData = $monthly->get_result()->fetch_all(MYSQLI_ASSOC);


$yearly = $conn->prepare("
    SELECT 
        YEAR(t.created_at) AS label,
        SUM(t.amount) AS total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
      AND c.type = 'expense'
    GROUP BY label
    ORDER BY label
");


$yearly->bind_param("i", $user_id);
$yearly->execute();
$yearlyData = $yearly->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Financial Analysis</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    background: linear-gradient(135deg,#1abc9c,#0b7d85);
    color:#fff;
    font-family:Segoe UI;
    padding-bottom:90px;
}
.container {max-width:1200px;margin:auto;padding:40px;}
h1 {font-size:40px;margin-bottom:20px;}

.tabs {display:flex;gap:12px;margin-bottom:25px;}
.tabs button {
    padding:10px 20px;border:2px solid #fff;
    background:transparent;color:#fff;
    border-radius:14px;cursor:pointer;
}
.tabs button.active, .tabs button:hover {
    background:#fff;color:#0b7d85;
}

.stats {display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:40px;}
.card {
    background:rgba(255,255,255,.2);
    border-radius:18px;padding:25px;
}
.card p {font-size:28px;font-weight:800;margin-top:10px;}

.chart-box {
    background:rgba(255,255,255,.2);
    border-radius:20px;padding:20px;
}

.bottom-nav {
    position:fixed;bottom:0;width:100%;
    background:#fff;display:flex;
    justify-content:space-around;padding:14px 0;
}
.bottom-nav a {text-decoration:none;color:#666;text-align:center;}
.bottom-nav a i {font-size:20px;display:block;}
.bottom-nav a.active {color:#1abc9c;}
canvas {
    background: rgba(255,255,255,0.08);
    border-radius: 12px;
}

</style>
</head>

<body>

<div class="container">
<h1>Your Financial Analysis</h1>

<div class="tabs">
    <button class="active" onclick="loadChart('daily')">Daily</button>
    <button onclick="loadChart('weekly')">Weekly</button>
    <button onclick="loadChart('monthly')">Monthly</button>
    <button onclick="loadChart('yearly')">Yearly</button>
</div>

<div class="stats">
    <div class="card">Income<p>₱<?=number_format($income,2)?></p></div>
    <div class="card">Expense<p>₱<?=number_format($expense,2)?></p></div>
    <div class="card">Balance<p>₱<?=number_format($balance,2)?></p></div>
    <div class="card">Savings<p>₱<?=number_format($savings,2)?></p></div>
</div>

<div class="chart-box">
    <canvas id="expenseChart" height="120"></canvas>
</div>
</div>

<div class="bottom-nav">
    <a href="dashboard.php"><i class="fa fa-house"></i>Home</a>
    <a href="transactions.php"><i class="fa fa-list"></i>Transactions</a>
    <a href="analysis.php" class="active"><i class="fa fa-chart-line"></i>Analysis</a>
    <a href="categories.php"><i class="fa fa-layer-group"></i>Categories</a>
    <a href="profile.php"><i class="fa fa-user"></i>Profile</a>
</div>

<script>
const dataSets = {
    daily: <?= json_encode($dailyData ?: []) ?>,
    weekly: <?= json_encode($weeklyData ?: []) ?>,
    monthly: <?= json_encode($monthlyData ?: []) ?>,
    yearly: <?= json_encode($yearlyData ?: []) ?>
};

let chart;
const ctx = document.getElementById("expenseChart").getContext("2d");

function loadChart(type, btn){
    document.querySelectorAll(".tabs button")
        .forEach(b => b.classList.remove("active"));

    if(btn) btn.classList.add("active");

    let labels = dataSets[type].map(d => d.label);
    let values = dataSets[type].map(d => Number(d.total));

    // fallback if no data
    if(labels.length === 0){
        labels = ["No data"];
        values = [0];
    }

    if(chart) chart.destroy();

    chart = new Chart(ctx, {
        type: "line",
        data: {
            labels,
            datasets: [{
                label: "Expenses",
                data: values,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                borderColor: "#ffffff",
                backgroundColor: "rgba(255,255,255,0.25)",
                pointRadius: 4,
                pointBackgroundColor: "#fff"
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { ticks: { color: "#fff" } },
                y: { ticks: { color: "#fff" } }
            }
        }
    });
}

document.querySelectorAll(".tabs button").forEach(btn => {
    btn.addEventListener("click", () => {
        loadChart(btn.textContent.toLowerCase(), btn);
    });
});


loadChart("daily", document.querySelector(".tabs button"));
</script>


</body>
</html>
