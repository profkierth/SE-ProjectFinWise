<?php
session_start();
if(!isset($_SESSION['user'])){ header('Location:index.php'); exit; }
$msg='';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $_SESSION['notify'] = isset($_POST['notify']) ? true : false;
    $_SESSION['theme'] = ($_POST['theme'] ?? 'light');
    $msg = 'Settings saved.';
}
$notify = $_SESSION['notify'] ?? true;
$theme = $_SESSION['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="gradient <?php if($theme=='dark') echo 'dark-theme'; ?>">
<div class="nav">
    <a href="dashboard.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="card" style="max-width:480px;margin-top:30px;">
    <h2><i class="fa-solid fa-gear"></i> Settings</h2>
    <?php if($msg): ?><p style="color:green;"><?php echo $msg; ?></p><?php endif; ?>

    <form method="POST">
        <label class="settings-row"><span>Notifications</span>
            <input type="checkbox" name="notify" <?php if($notify) echo 'checked'; ?>>
        </label>

        <label class="settings-row"><span>Theme</span>
            <select name="theme">
                <option value="light" <?php if($theme=='light') echo 'selected'; ?>>Light</option>
                <option value="dark" <?php if($theme=='dark') echo 'selected'; ?>>Dark</option>
            </select>
        </label>

        <button type="submit"><i class="fa-solid fa-save"></i> Save Settings</button>
    </form>
</div>
</body>
</html>
