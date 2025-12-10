<?php session_start(); $error=''; if($_SERVER['REQUEST_METHOD']=='POST'){ $email=$_POST['email']; $pass=$_POST['password']; if(!empty($email)&&!empty($pass)){ $_SESSION['user']=$email; header('Location: dashboard.php'); exit;} else{$error='Invalid login';}} ?>
<!DOCTYPE html>
<html>
<head>
    <title>FinWise Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="gradient">

    <div class="login-container">
        <h2 class="login-title">Welcome to <span>FinWise</span> 👋</h2>

        <form action="login_process.php" method="POST" class="login-box">

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" class="login-btn">Login</button>

            <p class="create">
                Don't have an account? 
                <a href="register.php">Create account</a>
            </p>

        </form>
    </div>

</body>
</html>

