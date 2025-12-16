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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        
        .gradient {
            background: linear-gradient(180deg, #0fb9b1, #0a7f86);
            min-height: 100vh;
        }

        
        .page-title {
            text-align: center;
            color: #ffffff;
            font-size: 28px;
            margin: 30px 0 25px 0;
        }

       
        .notification-container {
            width: 92%;
            max-width: 700px;
            margin: 30px auto 40px auto;
            padding: 0 15px;
        }

       
        .notif-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-left: 6px solid #0abab5;
            transition: 0.2s;
        }

        .notif-card:hover {
            transform: translateY(-3px);
        }

        
        .notif-header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 0;
            padding: 18px 20px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            z-index: 1000;
        }

        .notif-header h2 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        
        .back-btn {
            position: absolute;
            left: 20px;
            font-size: 22px;
            color: rgb(255, 255, 255);
            text-decoration: none;
        }

        .back-btn:hover {
            transform: translateX(-4px);
        }

        
        .notif-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .notif-title i {
            font-size: 22px;
            color: #008080;
        }

        .notif-title h3 {
            margin: 0;
            font-size: 18px;
        }

        
        .notif-card p {
            margin: 5px 0 8px;
            color: #444;
        }

        .notif-time {
            font-size: 13px;
            color: #777;
        }

       
        .empty-text {
            text-align: center;
            color: #eee;
            margin-top: 40px;
        }
    </style>
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
