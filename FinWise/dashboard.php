<?php 
session_start(); 
if(!isset($_SESSION['user'])){ 
    header('Location:index.php'); 
    exit; 
} 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - FinWise</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="gradient">

    <!-- TOP HEADER WITH USER INFO -->
    <header class="top-header">
        <div class="user-info">
            <div class="user-icon">👋</div>
            <div class="user-text">
                <h2>WELCOME, <?php echo $_SESSION['user']; ?>!</h2>
                <p class="subtitle">Glad to see you again</p>
            </div>
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="active">Home</a>
            <a href="analysis.php">Charts</a>
            <a href="notifications.php">Notifications</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <!-- DASHBOARD CONTENT -->
    <section class="dashboard-container">

        <div class="summary-grid">

            <div class="summary-card balance">
                <h3>Total Balance</h3>
                <p class="amount">₱5,600</p>
            </div>

            <div class="summary-card income">
                <h3>Total Income</h3>
                <p class="amount">₱8,000</p>
            </div>

            <div class="summary-card expense">
                <h3>Total Expense</h3>
                <p class="amount">₱2,300</p>
            </div>

            <div class="summary-card savings">
                <h3>Savings</h3>
                <p class="amount">₱3,700</p>
            </div>

        </div>

    </section>

</body>
</html>

