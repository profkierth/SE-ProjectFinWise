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

$recentStmt = $conn->prepare("
    SELECT 
        t.amount,
        t.created_at,
        c.name AS category,
        c.icon,
        LOWER(c.type) AS type
    FROM transactions t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
    ORDER BY t.created_at DESC
    LIMIT 5
");

$recentStmt->bind_param("i", $user_id);
$recentStmt->execute();
$recentTransactions = $recentStmt->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - FinWise</title>

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
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
        
.section-title {
    margin: 30px 0 15px;
    font-size: 20px;
    font-weight: 700;
}


.recent-transactions {
    background: #ffffff;
    border-radius: 14px;
    padding: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.tx-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 10px;
    border-bottom: 1px solid #eee;
}

.tx-item:last-child {
    border-bottom: none;
}


.tx-left {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
}


.tx-item.income .tx-left {
    background: #2ecc71;
}

.tx-item.expense .tx-left {
    background: #e74c3c;
}


.tx-middle {
    flex: 1;
    margin-left: 12px;
}

.tx-middle strong {
    display: block;
    font-size: 15px;
}

.tx-middle small {
    color: #777;
    font-size: 12px;
}


.tx-right {
    font-weight: bold;
    font-size: 15px;
}

.tx-item.income .tx-right {
    color: #2ecc71;
}

.tx-item.expense .tx-right {
    color: #e74c3c;
}


.empty-state {
    text-align: center;
    padding: 20px;
    color: #777;
}

    </style>
</head>

<body>


<div class="top-header">
    <a href="notification.php" class="notif-btn">
        <i class="fa-solid fa-bell"></i>
        <span class="notif-dot"></span>
    </a>
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
<h2 class="section-title">Recent Transactions</h2>

<div class="recent-transactions">

<?php if ($recentTransactions && $recentTransactions->num_rows > 0): ?>
    <?php while ($tx = $recentTransactions->fetch_assoc()): ?>
        <div class="tx-item <?= $tx['type'] === 'income' ? 'income' : 'expense' ?>">
            <div class="tx-left">
                <i class="fa-solid <?= htmlspecialchars($tx['icon'] ?? 'fa-circle') ?>"></i>
            </div>

            <div class="tx-middle">
                <strong><?= htmlspecialchars($tx['category'] ?? 'Uncategorized') ?></strong>
                <small><?= date("M d, Y h:i A", strtotime($tx['created_at'])) ?></small>
            </div>

            <div class="tx-right">
                <?= $tx['type'] === 'income' ? '+' : '-' ?>
                ₱<?= number_format($tx['amount'], 2) ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p class="empty-state">No transactions yet</p>
<?php endif; ?>

</div>



</div>
</body>
</html>
