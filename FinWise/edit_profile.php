<?php
session_start();
if(!isset($_SESSION['user'])){ 
    header('Location:index.php'); 
    exit; 
}

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Avatar upload
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0){
        $allowed = ['image/png','image/jpeg','image/jpg','image/gif'];

        if(in_array($_FILES['avatar']['type'], $allowed)){
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $newName = 'uploads/'.uniqid('avatar_').".".$ext;

            if(move_uploaded_file($_FILES['avatar']['tmp_name'], $newName)){
                $_SESSION['avatar'] = $newName;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only PNG, JPG, JPEG, GIF allowed.";
        }
    }

    // Save fields
    $_SESSION['fullname'] = trim($_POST['fullname']);
    $_SESSION['birthdate'] = $_POST['birthdate'];
    $_SESSION['gender'] = $_POST['gender'];
    $_SESSION['address'] = trim($_POST['address']);
    $_SESSION['user'] = trim($_POST['email']);

    if(!$error){
        $success = "Profile updated.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="gradient">

<div class="nav">
    <a href="dashboard.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="card" style="max-width:420px; margin-top:30px;">
    <h2><i class="fa-solid fa-user-pen"></i> Edit Profile</h2>

    <?php if($success): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>

    <?php if($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div style="text-align:center;margin-bottom:10px;">
            <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png'); ?>" 
                 id="preview"
                 style="width:110px;height:110px;border-radius:50%;border:3px solid #fff;">
        </div>

        <label class="label">Change Avatar</label>
        <div class="input-icon">
            <i class="fa-solid fa-image"></i>
            <input type="file" name="avatar" accept="image/*" onchange="previewImg(event)">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="fullname" required
                   value="<?= htmlspecialchars($_SESSION['fullname'] ?? '') ?>"
                   placeholder="Full Name">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-calendar"></i>
            <input type="date" name="birthdate" required
                   value="<?= htmlspecialchars($_SESSION['birthdate'] ?? '') ?>">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-venus-mars"></i>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male"   <?= ($_SESSION['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($_SESSION['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other"  <?= ($_SESSION['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-location-dot"></i>
            <input type="text" name="address" required
                   value="<?= htmlspecialchars($_SESSION['address'] ?? '') ?>"
                   placeholder="Address">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" required
                   value="<?= htmlspecialchars($_SESSION['user'] ?? '') ?>"
                   placeholder="Email">
        </div>

        <button type="submit"><i class="fa-solid fa-save"></i> Save Changes</button>
    </form>
</div>

<script>
function previewImg(event){
    const file = event.target.files[0];
    if(file){
        document.getElementById('preview').src = URL.createObjectURL(file);
    }
}
</script>

</body>
</html>
