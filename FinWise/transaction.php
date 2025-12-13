
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

<div class="tx-page">

    <h2 class="page-title">Transaction Overview</h2>

    <div class="tx-header">
        <button class="add-btn" onclick="openModal()">
            <i class="fa-solid fa-plus"></i> New Transaction
        </button>
    </div>

    <div class="tx-list">
        <?php foreach ($_SESSION['transactions'] as $i => $t): ?>
            <div class="tx-item">
                <div class="tx-left">
                    <div class="tx-icon <?php echo $t['type']; ?>">
                        <i class="fa-solid <?php echo $t['type']=='income' ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                    </div>

                    <div class="tx-details">
                        <span class="tx-label"><?php echo htmlspecialchars($t['label']); ?></span>
                        <small><?php echo ucfirst($t['type']); ?></small>
                    </div>
                </div>

                <div class="tx-right">
                    <span class="tx-amount">₱<?php echo number_format($t['amount']); ?></span>
                    <a href="?delete=<?php echo $i; ?>" class="tx-delete">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>



<div class="modal" id="addModal">
    <div class="modal-box">
        <h3>Add Transaction</h3>
        <form method="POST">
            <select name="type" required>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>

            <input type="text" name="label" placeholder="Label" required>
            <input type="number" name="amount" placeholder="Amount" required>

            <button type="submit" name="add" class="save-btn">Save</button>
            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openModal(){ document.getElementById('addModal').style.display='flex'; }
function closeModal(){ document.getElementById('addModal').style.display='none'; }
</script>

