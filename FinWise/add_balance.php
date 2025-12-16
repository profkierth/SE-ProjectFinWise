<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $source = $_POST['source'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO balances (user_id, amount, source, category) VALUES (?,?,?,?)");
    $stmt->bind_param("idss", $user_id, $amount, $source, $category);
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Balance - FinWise</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body{
    font-family:Arial;
    background:linear-gradient(135deg,#008080,#003f3f);
    padding:40px;
    color:#fff
}
.form-card{
    background:#fff;
    color:#333;
    border-radius:14px;
    padding:25px;
    max-width:400px;
    margin:auto
}
.form-card h2{
    text-align:center;
    margin-bottom:20px
}
.form-group{
    margin-bottom:15px
}
.form-group label{
    display:block;
    margin-bottom:6px;
    font-weight:bold
}
.form-group input,.form-group select{
    width:100%
    ;padding:10px;
    border-radius:8px;
    border:1px solid #ccc
}
.submit-btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#10b3ad;
    color:#fff;
    font-size:16px;
    cursor:pointer
}
.submit-btn:hover{
    background:#0e9c97
}
.back-link{
    text-align:center;
    margin-top:15px
}
.back-link a{
    color:#10b3ad;
    text-decoration:none
}
</style>
</head>
<body>
<div class="form-card">
    <h2>Add Balance</h2>
    <form method="POST">
        <div class="form-group">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" required>
        </div>
        <div class="form-group">
            <label>Source</label>
            <select name="source" required>
                <option value="Income">Income</option>
                <option value="Salary">Salary</option>
                <option value="Allowance">Allowance</option>
            </select>
        </div>
        <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" placeholder="e.g. Initial Balance">
        </div>
        <button class="submit-btn">Add Balance</button>
    </form>
    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
