<?php
session_start();

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

/* =========================
   CATEGORY DEFINITIONS
========================= */
$categories = [
    "salary" => ["name"=>"Salary","icon"=>"fa-money-bill-wave","color"=>"#3498db"],
    "food" => ["name"=>"Food","icon"=>"fa-burger","color"=>"#e67e22"],
    "transport" => ["name"=>"Transport","icon"=>"fa-bus","color"=>"#9b59b6"],
    "bills" => ["name"=>"Bills","icon"=>"fa-file-invoice-dollar","color"=>"#34495e"],
    "entertainment" => ["name"=>"Entertainment","icon"=>"fa-film","color"=>"#f39c12"],
    "others" => ["name"=>"Others","icon"=>"fa-layer-group","color"=>"#7f8c8d"],
];

/* =========================
   CSRF TOKEN
========================= */
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* =========================
   SAMPLE DATA
========================= */
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

/* =========================
   ADD TRANSACTION
========================= */
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

/* =========================
   DELETE TRANSACTION (POST)
========================= */
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
    <a href="dashboard.php">Home</a>
    <a href="analysis.php">Analysis</a>
    <a href="notifications.php">Notifications</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<h2 class="page-title">Transactions</h2>

<div class="tx-top">
    <button class="add-btn" onclick="document.getElementById('addModal').style.display='flex'">
        <i class="fa-solid fa-plus"></i> New Transaction
    </button>
</div>

<!-- TRANSACTION LIST -->
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

        <form method="POST" style="display:inline;">
    <input type="hidden" name="delete" value="<?php echo $i; ?>">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
    <button class="tx-delete">
        <i class="fa-solid fa-trash"></i>
    </button>
</form>

<?php endforeach; ?>
</div>

<!-- MODAL -->
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
            <option value="<?php echo $key; ?>">
                <?php echo $cat['name']; ?>
            </option>
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

</body>
</html>

