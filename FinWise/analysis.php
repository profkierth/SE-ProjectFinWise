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
    <title>Financial Analysis - FinWise</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .analysis-container {
            padding: 30px;
            color: white;
            max-width: 1200px;
            margin: auto;
        }

        .analysis-title {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .filter-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .filter-btn {
            padding: 10px 18px;
            background: rgba(255,255,255,0.2);
            border: 2px solid white;
            border-radius: 10px;
            cursor: pointer;
            color: white;
            font-weight: 600;
            transition: 0.3s;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: white;
            color: #008080;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 25px;
            border-radius: 15px;
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.4);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.25);
        }

        .stat-title {
            font-size: 18px;
            font-weight: bold;
        }

        .stat-value {
            font-size: 32px;
            margin-top: 10px;
            font-weight: 700;
        }

        .section-title {
            margin-top: 40px;
            margin-bottom: 15px;
            font-size: 25px;
            font-weight: 700;
        }

        .chart-box {
            width: 100%;
            height: 280px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
        }
.top-nav {
    width: 100%;
    padding: 18px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255,255,255,0.4);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.nav-left {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    color: white;
    font-weight: 700;
}

.wave {
    font-size: 24px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 35px;
    margin: 0;
    padding: 0;
}

.nav-links a {
    color: white;
    font-weight: 600;
    text-decoration: none;
    font-size: 18px;
    transition: 0.2s;
    padding-bottom: 5px;
}

.nav-links a:hover {
    border-bottom: 2px solid white;
}

.logout-btn {
    color: #ffdddd;
    font-weight: 700;
}


    </style>
</head>

<body class="gradient">

<?php include "nav.php"; ?>

<div class="analysis-container">

    <h1 class="analysis-title">Your Financial Analysis</h1>

    
    <div class="filter-tabs">
        <button class="filter-btn active">Daily</button>
        <button class="filter-btn">Weekly</button>
        <button class="filter-btn">Monthly</button>
        <button class="filter-btn">Yearly</button>
    </div>

   
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Income</div>
            <div class="stat-value">₱0.00</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Total Expenses</div>
            <div class="stat-value">₱0.00</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Balance</div>
            <div class="stat-value">₱0.00</div>
        </div>
    </div>

    <div class="section-title">Expense Breakdown</div>
    <div class="chart-box">Chart Placeholder</div>

</div>

</body>
</html>
