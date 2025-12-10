<?php
session_start();
if(!isset($_SESSION['user'])){ header('Location:index.php'); exit; }
$msg=''; $err='';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if(isset($_SESSION['password']) && $_SESSION['password'] != '' ){
        if($current !== $_SESSION['password']){
            $err = 'Current password is incorrect.';
        } elseif($new !== $confirm){
            $err = 'New passwords do not match.';
        } else {
            $_SESSION['password'] = $new;
            $msg = 'Password changed successfully.';
        }
    } else {
        if($new !== $confirm){ $err='New passwords do not match.'; } else { $_SESSION['password']=$new; $msg='Password set.'; }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Security</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="gradient">
<div class="nav">
    <a href="dashboard.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="card" style="max-width:480px;margin-top:30px;">
    <h2><i class="fa-solid fa-shield-halved"></i> Security</h2>

    <?php if($msg): ?><p style="color:green;"><?php echo $msg; ?></p><?php endif; ?>
    <?php if($err): ?><p style="color:red;"><?php echo $err; ?></p><?php endif; ?>

    <form method="POST" id="pwForm">
        <div class="input-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="current" placeholder="Current Password">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="new" id="newpw" placeholder="New Password" required oninput="checkStrength()">
        </div>

        <div style="width:90%;margin:10px auto;text-align:left;">
            <small>Password strength: <span id="strength">-</span></small>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="confirm" placeholder="Confirm New Password" required>
        </div>

        <button type="submit"><i class="fa-solid fa-key"></i> Change Password</button>
    </form>
</div>

<script>
function checkStrength(){
    const v = document.getElementById('newpw').value;
    const s = document.getElementById('strength');
    let score = 0;
    if(v.length>=8) score++;
    if(/[A-Z]/.test(v)) score++;
    if(/[0-9]/.test(v)) score++;
    if(/[^A-Za-z0-9]/.test(v)) score++;
    if(score<=1) s.textContent='Weak';
    else if(score==2) s.textContent='Medium';
    else if(score>=3) s.textContent='Strong';
}
</script>
</body>
</html>
