<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location:index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT * FROM notifications 
     WHERE user_id = ? 
     ORDER BY created_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
     href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="gradient">


<div class="notif-header">
    <a href="dashboard.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h2>Notifications</h2>
</div>

<div class="notification-container">

<?php while($row = $result->fetch_assoc()): ?>
<div class="notif-card">

    <div class="notif-title">
        <i class="fa-solid 
        <?php
            if ($row['type'] == 'income') echo 'fa-coins';
            elseif ($row['type'] == 'expense') echo 'fa-wallet';
            elseif ($row['type'] == 'report') echo 'fa-chart-line';
            else echo 'fa-bell';
        ?>"></i>

        <h3><?= htmlspecialchars($row['title']) ?></h3>
    </div>

    <p><?= htmlspecialchars($row['message']) ?></p>
    <span class="notif-time">
        <?= date("M d, Y h:i A", strtotime($row['created_at'])) ?>
    </span>

</div>
<?php endwhile; ?>

<?php if($result->num_rows == 0): ?>
<p class="empty-text">No notifications yet</p>
<?php endif; ?>

</div>

</body>

</html>
