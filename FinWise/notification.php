<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location:index.php"); exit; }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="gradient">
<div class="nav">
    <a href="dashboard.php">Home</a>
    <a href="analysis.php">Charts</a>
    <a href="notifications.php" class="active">Notifications</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="notification-container">
    <h2 class="page-title">Notifications</h2>

    <div class="notif-card"><h3>New Expense Added</h3><p>You recorded ₱250 for transportation.</p><span>2 hours ago</span></div>
    <div class="notif-card"><h3>Income Update</h3><p>Your salary ₱8,000 was added.</p><span>1 day ago</span></div>
    <div class="notif-card"><h3>Weekly Report</h3><p>Your weekly summary is now available.</p><span>2 days ago</span></div>

</div>
</body>
</html>

