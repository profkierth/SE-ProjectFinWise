
<?php
session_start();


if (!isset($_SESSION['user'])) { 
    header("Location:index.php"); 
    exit; 
}

if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [
        ["type" => "income", "label" => "Salary", "amount" => 8000],
        ["type" => "expense", "label" => "Transportation", "amount" => 250],
        ["type" => "expense", "label" => "Food", "amount" => 500],
    ];
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $type = $_POST['type'];
    $label = trim($_POST['label']);
    $amount = intval($_POST['amount']);

    $_SESSION['transactions'][] = [
        "type" => $type,
        "label" => $label,
        "amount" => $amount
    ];
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    unset($_SESSION['transactions'][$id]);
    $_SESSION['transactions'] = array_values($_SESSION['transactions']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="gradient">

<div class="nav">
    <a href="dashboard.php" class="active">Home</a>
    <a href="analysis.php">Analysis</a>
    <a href="notifications.php">Notifications</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<h2 class="page-title">Transactions</h2>


<div class="tx-top">
    <button class="add-btn" onclick="document.getElementById('addModal').style.display='flex'">
        <i class="fa-solid fa-plus"></i> Add Transaction
    </button>
</div>


<div class="tx-container">
<?php foreach ($_SESSION['transactions'] as $i => $t): ?>
    <div class="tx-card">
        <div class="tx-icon <?php echo $t['type']; ?>">
            <?php echo $t['type'] == 'income' ? '↑' : '↓'; ?>
        </div>
        
        <div class="tx-info">
            <h3><?php echo htmlspecialchars($t['label']); ?></h3>
            <p>₱<?php echo number_format($t['amount']); ?></p>
        </div>

        <a href="?delete=<?php echo $i; ?>" class="tx-delete">
            <i class="fa-solid fa-trash"></i>
        </a>
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

            <input type="text" name="label" placeholder="Label (e.g. Salary, Food)" required>

            <input type="number" name="amount" placeholder="Amount" required>

            <button type="submit" name="add" class="save-btn">Save</button>

            <button type="button" class="cancel-btn"
                onclick="document.getElementById('addModal').style.display='none'">
                Cancel
            </button>
        </form>
    </div>
</div>

</body>
</html>
