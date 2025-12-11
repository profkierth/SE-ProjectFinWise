<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit;
}

include 'db.php';

// TOTAL INCOME & EXPENSE
$total_income = $conn->query("SELECT SUM(amount) AS total FROM transactions1 WHERE type='income'")->fetch_assoc()['total'] ?? 0;
$total_expense = $conn->query("SELECT SUM(amount) AS total FROM transactions1 WHERE type='expense'")->fetch_assoc()['total'] ?? 0;
$balance = $total_income - $total_expense;

// EXPENSES BY CATEGORY
$cat_result = $conn->query("
    SELECT c.name, SUM(t.amount) AS total
    FROM transactions1 t
    JOIN categories c ON t.category_id = c.id
    WHERE t.type='expense'
    GROUP BY c.name
");

$categories = [];
$cat_totals = [];
while($row = $cat_result->fetch_assoc()){
    $categories[] = $row['name'];
    $cat_totals[] = $row['total'];
}

// MONTHLY INCOME/EXPENSE
$month_result = $conn->query("
    SELECT month,
        SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS income,
        SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS expense
    FROM transactions1
    GROUP BY month
    ORDER BY STR_TO_DATE(CONCAT('01-', month), '%d-%M-%Y')
");

$months = [];
$monthly_income = [];
$monthly_expense = [];
while($row = $month_result->fetch_assoc()){
    $months[] = $row['month'];
    $monthly_income[] = $row['income'];
    $monthly_expense[] = $row['expense'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Analysis - Finance App</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="gradient">

<div class="nav">
    <a href="dashboard.php">Home</a>
    <a href="transactions.php">Transactions</a>
    <a href="analysis.php" class="active">Analysis</a>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
    <h1>Financial Analysis</h1>

    <!-- Summary -->
    <div class="card">
        <h2>Summary</h2>
        <p>Total Income: <strong>₱<?= number_format($total_income, 2) ?></strong></p>
        <p>Total Expense: <strong>₱<?= number_format($total_expense, 2) ?></strong></p>
        <p>Balance: <strong>₱<?= number_format($balance, 2) ?></strong></p>
    </div>

    <!-- Expenses by Category -->
    <div class="card">
        <h2>Expenses by Category</h2>
        <canvas id="categoryChart" style="width:100%; max-width:600px; height:300px;"></canvas>
    </div>

    <!-- Monthly Trend -->
    <div class="card">
        <h2>Monthly Income/Expense</h2>
        <canvas id="monthlyChart" style="width:100%; max-width:700px; height:350px;"></canvas>
    </div>
</div>

<script>
    // Pie Chart: Expenses by Category
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'pie',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                label: 'Expenses by Category',
                data: <?= json_encode($cat_totals) ?>,
                backgroundColor: [
                    '#e74c3c','#3498db','#2ecc71','#f1c40f','#9b59b6','#1abc9c'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Bar Chart: Monthly Income vs Expense
    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [
                {
                    label: 'Income',
                    data: <?= json_encode($monthly_income) ?>,
                    backgroundColor: '#2ecc71'
                },
                {
                    label: 'Expense',
                    data: <?= json_encode($monthly_expense) ?>,
                    backgroundColor: '#e74c3c'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

</body>
</html>
