<?php
session_start();
require "db.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare(
            "SELECT id, fullname, password, avatar FROM users WHERE email = ?"
        );
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
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    </form>
    <p style="margin-top:8px;">Don't have an account? <a href="register.php">Create account</a></p>
</div>
</body>
</html>
