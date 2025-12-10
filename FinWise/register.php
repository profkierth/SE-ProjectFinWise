<?php
session_start();
$error='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else if (!empty($fullname) && !empty($birthdate) && !empty($gender) && !empty($address) && !empty($email) && !empty($password)) {

        // Store user in session (demo)
        $_SESSION['user'] = $email;
        $_SESSION['fullname'] = $fullname;
        $_SESSION['birthdate'] = $birthdate;
        $_SESSION['gender'] = $gender;
        $_SESSION['address'] = $address;
        $_SESSION['password'] = $password;
        // default avatar
        $_SESSION['avatar'] = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

        header('Location: dashboard.php');
        exit;

    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - FinWise</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="gradient">

<div class="card">
    <h2><i class="fa-solid fa-user-plus"></i> Create Account</h2>

    <form method="POST">

        <div class="input-icon">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="fullname" placeholder="Full Name" required>
        </div>

        <label class="label">Birthdate</label>
        <div class="input-icon">
            <i class="fa-solid fa-calendar"></i>
            <input type="date" name="birthdate" required>
        </div>

        <label class="label">Gender</label>
        <div class="input-icon">
            <i class="fa-solid fa-venus-mars"></i>
            <select name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-location-dot"></i>
            <input type="text" name="address" placeholder="Address" required>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address" required>
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

        <p class="error-text"><?php echo $error; ?></p>

        <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>

    </form>
</div>

</body>
</html>
