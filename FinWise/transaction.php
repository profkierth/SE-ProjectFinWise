<?php
session_start();
if(!isset($_SESSION['user_id'])){ 
    header('Location: index.php'); 
    exit; 
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transactions - FinWise</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="gradient">

<div class="nav">
    <a href="dashboard.php">Home</a>
    <a href="analysis.php">Charts</a>
    <a href="notifications.php">Notifications</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="transaction-container">
    <h2 class="dash-title">Transaction</h2>

    <div class="transaction-grid">

        <div class="transaction-card">
            <h3>Total Balance</h3>
            <p class="amount">₱5,600</p>
        </div>

        <div class="transaction-card">
            <h3>Total Income</h3>
            <p class="amount">₱8,000</p>
        </div>

        <div class="transaction-card">
            <h3>Total Expense</h3>
            <p class="amount">₱2,300</p>
        </div>

    </div>

</div>

</body>
</html>
