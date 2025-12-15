<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$categories = [
    "salary" => ["name"=>"Salary","icon"=>"fa-money-bill-wave","color"=>"#3498db"],
    "food" => ["name"=>"Food","icon"=>"fa-burger","color"=>"#e67e22"],
    "transport" => ["name"=>"Transport","icon"=>"fa-bus","color"=>"#9b59b6"],
    "bills" => ["name"=>"Bills","icon"=>"fa-file-invoice-dollar","color"=>"#34495e"],
    "entertainment" => ["name"=>"Entertainment","icon"=>"fa-film","color"=>"#f39c12"],
    "others" => ["name"=>"Others","icon"=>"fa-layer-group","color"=>"#7f8c8d"],
];


if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}


if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die("Invalid request");
    }

    $type = in_array($_POST['type'], ['income','expense']) ? $_POST['type'] : 'expense';
    $label = trim($_POST['label']);
    $amount = floatval($_POST['amount']);
    $category = $_POST['category'];

    if ($amount <= 0 || empty($label)) {
        die("Invalid input");
    }

    if (!isset($categories[$category])) {
        $category = "others";
    }

    $_SESSION['transactions'][] = [
        "type" => $type,
        "label" => htmlspecialchars($label),
        "amount" => $amount,
        "category" => $category
    ];
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die("Invalid request");
    }

    $index = intval($_POST['delete']);
    if (isset($_SESSION['transactions'][$index])) {
        unset($_SESSION['transactions'][$index]);
        $_SESSION['transactions'] = array_values($_SESSION['transactions']);
    }
}

$totalIncome = 0;
$totalExpense = 0;

foreach ($_SESSION['transactions'] as $t) {
    if ($t['type'] === 'income') {
        $totalIncome += $t['amount'];
    } else {
        $totalExpense += $t['amount'];
    }
}

$balance = $totalIncome - $totalExpense;
$savings = $balance > 0 ? $balance : 0;


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Transactions</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", sans-serif;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #1abc9c, #16a085);
    padding-bottom: 100px; /* space for bottom nav */
}


.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #ffffff;
    display: flex;
    justify-content: space-around;
    padding: 12px 0;
    box-shadow: 0 -5px 20px rgba(0,0,0,.15);
    z-index: 100;
}

.bottom-nav a {
    text-decoration: none;
    color: #555;
    font-size: 13px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    transition: .3s;
}

.bottom-nav a i {
    font-size: 18px;
}

.bottom-nav a.active,
.bottom-nav a:hover {
    color: #1abc9c;
    transform: translateY(-3px);
}


.page-title {
    text-align: center;
    color: #fff;
    margin: 20px 0;
    font-size: 26px;
}


.main-content {
    padding: 0 15px;
    max-width: 900px;
    margin: auto;
}

.overview-title {
    text-align: center;
    color: #fff;
    margin-bottom: 15px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}

@media (min-width: 700px) {
    .summary-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.summary-card {
    background: #fff;
    border-radius: 16px;
    padding: 18px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
    animation: fadeUp .6s ease;
}

.summary-card h3 {
    font-size: 14px;
    color: #777;
}

.summary-card .amount {
    font-size: 20px;
    font-weight: bold;
    margin-top: 6px;
}

.summary-card.balance .amount { color: #2980b9; }
.summary-card.income  .amount { color: #2ecc71; }
.summary-card.expense .amount { color: #e74c3c; }
.summary-card.savings .amount { color: #16a085; }


.tx-top {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.add-btn {
    background: #ffffff;
    color: #1abc9c;
    border: none;
    padding: 12px 22px;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
    transition: .3s;
    box-shadow: 0 8px 20px rgba(0,0,0,.2);
}

.add-btn:hover {
    transform: scale(1.05);
}


.tx-container {
    padding: 0 15px;
    max-width: 900px;
    margin: auto;
}

.tx-card {
    background: #fff;
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
    animation: fadeUp .5s ease;
}

.tx-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.tx-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: #fff;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tx-icon.income { background: #2ecc71; }
.tx-icon.expense { background: #e74c3c; }

.tx-left h4 {
    font-size: 15px;
    margin-bottom: 4px;
}

.tx-category {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
}


.tx-delete {
    background: transparent;
    border: none;
    color: #e74c3c;
    font-size: 18px;
    cursor: pointer;
}

.tx-delete:hover {
    transform: scale(1.2);
}


.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.modal-box {
    background: #fff;
    padding: 20px;
    border-radius: 18px;
    width: 90%;
    max-width: 350px;
    animation: scaleIn .4s ease;
}

.modal-box h3 {
    text-align: center;
    margin-bottom: 15px;
}

.modal-box select,
.modal-box input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 10px;
    border: 1px solid #ddd;
}


.save-btn {
    background: #1abc9c;
    color: #fff;
    width: 100%;
    padding: 10px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
}

.cancel-btn {
    background: #ccc;
    width: 100%;
    padding: 10px;
    border-radius: 12px;
    border: none;
    margin-top: 6px;
    cursor: pointer;
}


@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes scaleIn {
    from { transform: scale(.8); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
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

<h2 class="page-title">Transactions</h2>

<div class="main-content">

    <h2 class="overview-title">Your Financial Overview</h2>

    <div class="summary-grid">

        <div class="summary-card balance">
            <h3>Total Balance</h3>
            <p class="amount">₱<?= number_format($balance, 2) ?></p>
        </div>

        <div class="summary-card income">
            <h3>Total Income</h3>
            <p class="amount">₱<?= number_format($totalIncome, 2) ?></p>
        </div>

        <div class="summary-card expense">
            <h3>Total Expense</h3>
            <p class="amount">₱<?= number_format($totalExpense, 2) ?></p>
        </div>

        <div class="summary-card savings">
            <h3>Total Savings</h3>
            <p class="amount">₱<?= number_format($savings, 2) ?></p>
        </div>

    </div>
</div>


<div class="tx-top">
    <button class="add-btn" onclick="document.getElementById('addModal').style.display='flex'">
        <i class="fa-solid fa-plus"></i> New Transaction
    </button>
</div>

<div class="tx-container">
<?php foreach($_SESSION['transactions'] as $i=>$t):
    $cat = $categories[$t['category']] ?? $categories['others'];
?>
    <div class="tx-card">
        <div class="tx-left">
            <div class="tx-icon <?php echo $t['type']; ?>">
                <?php echo $t['type']=="income"?"↑":"↓"; ?>
            </div>
            <div>
                <h4><?php echo htmlspecialchars($t['label']); ?></h4>
                <span class="tx-category" style="background:<?php echo $cat['color']; ?>">
                    <i class="fa-solid <?php echo $cat['icon']; ?>"></i>
                    <?php echo $cat['name']; ?>
                </span>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="delete" value="<?php echo $i; ?>">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
            <button class="tx-delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
<?php endforeach; ?>
</div>


<div class="modal" id="addModal">
<div class="modal-box">
<h3>Add Transaction</h3>

<form method="POST">
    <select name="type" required>
        <option value="income">Income</option>
        <option value="expense">Expense</option>
    </select>

    <select name="category" required>
        <?php foreach($categories as $key=>$cat): ?>
            <option value="<?php echo $key; ?>"><?php echo $cat['name']; ?></option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="label" placeholder="Label" required>
    <input type="number" name="amount" placeholder="Amount" required>
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">

    <button type="submit" name="add" class="save-btn">Save</button>
    <button type="button" class="cancel-btn"
        onclick="document.getElementById('addModal').style.display='none'">
        Cancel
    </button>
</form>
</div>
</div>


<div class="bottom-nav">
    <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>Home</span></a>
    <a href="transactions.php" class="active"><i class="fa-solid fa-wallet"></i><span>Transactions</span></a>
    <a href="analysis.php"><i class="fa-solid fa-chart-pie"></i><span>Analysis</span></a>
    <a href="categories.php"><i class="fa-solid fa-tags"></i><span>Categories</span></a>
    <a href="profile.php"><i class="fa-solid fa-user"></i><span>Profile</span></a>
</div>

</body>
</html>
