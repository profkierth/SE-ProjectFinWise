<?php
// --- PHP PROCESSING (must be at the top) ---
$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['full_name'])) {
        $errors['full_name'] = "Full Name is required.";
    }
    if (empty($_POST['birthdate'])) {
        $errors['birthdate'] = "Birthdate is required.";
    }
    if (empty($_POST['gender'])) {
        $errors['gender'] = "Gender is required.";
    }
    if (empty($_POST['email'])) {
        $errors['email'] = "Email is required.";
    }
    if (empty($_POST['password'])) {
        $errors['password'] = "Password is required.";
    }
    if (empty($_POST['confirm_password'])) {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $success_message = "Registration successful! You can now log in.";
        $_POST = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="style.css"> <!-- FIXED: CSS link -->
</head>

<body class="gradient">

    <div class="login-container"> <!-- Reuse login style for centering -->
        
        <h2 class="login-title">Create Your <span>FinWise</span> Account ✨</h2>

        <?php if (!empty($success_message)): ?>
            <div class="success" style="
                margin-bottom: 20px;
                padding: 12px;
                border-radius: 10px;
                background: rgba(0,255,100,0.25);
                color: white;
                text-align: center;">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-box"> <!-- Reuse login-box style -->

            <div class="form-group">
                <label class="label">Full Name</label>
                <input type="text" name="full_name"
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                <?php if (!empty($errors['full_name'])): ?>
                    <div class="error-text"><?= $errors['full_name'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="label">Birthdate</label>
                <input type="date" name="birthdate"
                       value="<?= htmlspecialchars($_POST['birthdate'] ?? '') ?>">
                <?php if (!empty($errors['birthdate'])): ?>
                    <div class="error-text"><?= $errors['birthdate'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="label">Gender</label>
                <select name="gender">
                    <option value="">Select Gender</option>
                    <option value="male"   <?= (($_POST['gender'] ?? '') == 'male') ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= (($_POST['gender'] ?? '') == 'female') ? 'selected' : '' ?>>Female</option>
                    <option value="other"  <?= (($_POST['gender'] ?? '') == 'other') ? 'selected' : '' ?>>Other</option>
                </select>
                <?php if (!empty($errors['gender'])): ?>
                    <div class="error-text"><?= $errors['gender'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="label">Address</label>
                <input type="text" name="address"
                       value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="label">Email Address</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-text"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="label">Password</label>
                <input type="password" name="password">
                <?php if (!empty($errors['password'])): ?>
                    <div class="error-text"><?= $errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="label">Confirm Password</label>
                <input type="password" name="confirm_password">
                <?php if (!empty($errors['confirm_password'])): ?>
                    <div class="error-text"><?= $errors['confirm_password'] ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="login-btn">Create Account</button>

            <p class="create" style="margin-top: 10px;">
                Already have an account?
                <a href="index.php">Login here</a>
            </p>

        </form>

    </div>

</body>
</html>

