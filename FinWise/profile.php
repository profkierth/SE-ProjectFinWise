<?php
session_start();
if(!isset($_SESSION['user'])){ 
    header("Location:index.php"); 
    exit; 
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="gradient">

<div class="profile-header">
    <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>

    <div class="profile-top">
        <img src="<?php echo htmlspecialchars($_SESSION['avatar'] ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png'); ?>" 
             class="profile-avatar">

        <h2 class="profile-name">
            <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['user']); ?>
        </h2>
    </div>
</div>

<div class="profile-menu">

    <a href="edit_profile.php" class="profile-item">
        <i class="fa-solid fa-user"></i>
        <span>Edit Profile</span>
    </a>

    <a href="security.php" class="profile-item">
        <i class="fa-solid fa-shield"></i>
        <span>Security</span>
    </a>

    <a href="setting.php" class="profile-item">
        <i class="fa-solid fa-gear"></i>
        <span>Settings</span>
    </a>

    <a href="logout.php" class="profile-item logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
    </a>

</div>

</body>
</html>
