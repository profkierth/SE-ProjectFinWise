<?php
session_start();
$error='';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $email=trim($_POST['email']);
    $pass=$_POST['password'];
    if(!empty($email) && !empty($pass)){
        if(isset($_SESSION['user']) && $_SESSION['user']==$email && isset($_SESSION['password']) && $_SESSION['password']==$pass){
            $_SESSION['logged_in']=true;
            header('Location: dashboard.php');
            exit;
        } else {
            $error='Invalid credentials or user not registered.';
        }
    } else {
        $error='Please fill all fields.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Login - FinWise</title>
</head>
<body class="gradient">
<div class="card">
    <h2>FinWise</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p style="color:red;"><?php echo $error;?></p>
    </form>
    <p style="margin-top:8px;">Don't have an account? <a href="register.php">Create account</a></p>
</div>
</body>
</html>
