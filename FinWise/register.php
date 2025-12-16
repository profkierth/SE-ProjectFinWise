<?php
session_start();
require 'db.php';

$error = '';
$values = [
    'fullname' => '',
    'birthdate' => '',
    'gender' => '',
    'address' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname  = trim($_POST['fullname']);
    $birthdate = $_POST['birthdate'];
    $gender    = $_POST['gender'] ?? '';
    $address   = trim($_POST['address']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm'];

    $values = compact('fullname','birthdate','gender','address','email');
   
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $error = "Email already registered. Please login or use another email.";
        } else {
        
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (fullname, birthdate, gender, address, email, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullname, $birthdate, $gender, $address, $email, $hashedPassword);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php?registered=1");
                exit;
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - FinWise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        
        body.gradient {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #008080, #003f3f);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(6px);
            padding: 25px 30px;
            border-radius: 15px;
            width: 400px;
            box-sizing: border-box;
            color: #fff;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

       
        .input-icon {
            position: relative;
            width: 100%;
            margin: 12px 0;
            box-sizing: border-box;
        }

        .input-icon i {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #0abab5;
            font-size: 16px;
            pointer-events: none;
        }

        .input-icon input,
        .input-icon select,
        .input-icon input[type="file"] {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
            outline: none;
            background-color: #fff;
            color: #333;
            box-sizing: border-box;
        }

        .input-icon input:focus,
        .input-icon select:focus,
        .input-icon input[type="file"]:focus {
            border-color: #0abab5;
            box-shadow: 0 0 5px rgba(10, 186, 181, 0.5);
        }

        select {
            appearance: none;
        }

        .error-text {
            color: #ff6666;
            margin-top: 10px;
            text-align: center;
        }

        button {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background-color: #0abab5;
            color: #003f3f;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
        }

        button:hover {
            background-color: #00d0d0;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="gradient">

<div class="card">
    <h2><i class="fa-solid fa-user-plus"></i> Create Account</h2>

    <form method="POST">

        <div class="input-icon">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="fullname" placeholder="Full Name" required
                   value="<?= htmlspecialchars($values['fullname']) ?>">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-calendar"></i>
            <input type="date" name="birthdate" required
                   value="<?= htmlspecialchars($values['birthdate']) ?>">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-venus-mars"></i>
            <select name="gender" required>
                <option value="" disabled <?= $values['gender']=='' ? 'selected' : '' ?>>Select Gender</option>
                <option value="Male"   <?= $values['gender']=='Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $values['gender']=='Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other"  <?= $values['gender']=='Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-location-dot"></i>
            <input type="text" name="address" placeholder="Address" required
                   value="<?= htmlspecialchars($values['address']) ?>">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address" required
                   value="<?= htmlspecialchars($values['email']) ?>">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="confirm" placeholder="Confirm Password" required>
        </div>

        <button type="submit"><i class="fa-solid fa-arrow-right"></i> Register</button>

        <?php if($error): ?>
            <p class="error-text"><?= $error ?></p>
        <?php endif; ?>

        <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>

    </form>
</div>

</body>
</html>
