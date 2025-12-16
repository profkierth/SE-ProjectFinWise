<?php
session_start();
require "db.php"; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, fullname, password, avatar FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['avatar'] = $user['avatar'];

                header("Location: dashboard.php");
                exit;
            }
        }
        $error = "Invalid email or password";
    } else {
        $error = "Please fill in all fields";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - FinWise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            background: url('finwise.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4); 
            z-index: -1;
        }

        
        .card {
            background: rgba(255,255,255,0.9); 
            padding: 35px 40px;
            border-radius: 15px;
            width: 360px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .card h2 {
            margin-bottom: 25px;
            color: #0abab5;
            font-size: 28px;
        }

        .card form {
            display: flex;
            flex-direction: column;
        }

        
        .input-icon {
            position: relative;
            margin-bottom: 20px;
        }

        .input-icon i {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #0abab5;
            font-size: 16px;
        }

        .input-icon input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .card button {
            background: #0abab5;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .card button:hover {
            background: #089aa0;
        }

        .error-text {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .card a {
            display: block;
            margin-top: 15px;
            color: #0abab5;
            text-decoration: none;
            font-size: 14px;
        }

        .card a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="card">
    <h2><i class="fa-solid fa-lock"></i> FinWise</h2>
    <form method="POST" action="">
        <div class="input-icon">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit"><i class="fa-solid fa-arrow-right"></i> Login</button>

        <p class="error-text"><?php echo htmlspecialchars($error ?? ''); ?></p>

        <a href="register.php">Don't have an account? Create account</a>
    </form>
</div>

</body>
</html>
