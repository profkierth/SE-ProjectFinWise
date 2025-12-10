<?php
session_start();
require 'db.php';

$error='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $error = "Email already registered. Please login or use another email.";
} else {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (fullname, birthdate, gender, address, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullname, $birthdate, $gender, $address, $email, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: index.php?registered=1");
        exit;
    } else {
        $error = "Registration failed: " . $conn->error;
    }
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
