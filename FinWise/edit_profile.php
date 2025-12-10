<?php
session_start();
if(!isset($_SESSION['user'])){ header('Location:index.php'); exit; }
$success=''; $error='';

if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error']==0){
        $allowed = ['image/png','image/jpeg','image/jpg','image/gif'];
        if(in_array($_FILES['avatar']['type'],$allowed)){
            $name = basename($_FILES['avatar']['name']);
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $target = 'uploads/'.uniqid('avatar_').'.'.$ext;
            if(move_uploaded_file($_FILES['avatar']['tmp_name'], $target)){
                $_SESSION['avatar'] = $target;
            } else {
                $error = 'Failed to upload image.';
            }
        } else {
            $error = 'Only JPG/PNG/GIF allowed.';
        }
    }
    $_SESSION['fullname'] = trim($_POST['fullname'] ?? $_SESSION['fullname']);
    $_SESSION['birthdate'] = $_POST['birthdate'] ?? $_SESSION['birthdate'];
    $_SESSION['gender'] = $_POST['gender'] ?? $_SESSION['gender'];
    $_SESSION['address'] = trim($_POST['address'] ?? $_SESSION['address']);
    $_SESSION['user'] = trim($_POST['email'] ?? $_SESSION['user']);
    $success = 'Profile updated.';
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

<div class="card" style="max-width:420px;margin-top:30px;">
    <h2><i class="fa-solid fa-user-pen"></i> Edit Profile</h2>

    <?php if($success): ?><p style="color:green;"><?php echo $success; ?></p><?php endif; ?>
    <?php if($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div style="text-align:center;margin-bottom:10px;">
            <img src="<?php echo htmlspecialchars($_SESSION['avatar'] ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png'); ?>" id="preview" style="width:100px;height:100px;border-radius:50%;border:3px solid #fff;">
        </div>

        <label class="label">Change Avatar</label>
        <div class="input-icon">
            <i class="fa-solid fa-image"></i>
            <input type="file" name="avatar" accept="image/*" onchange="previewImg(event)">
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($_SESSION['fullname'] ?? ''); ?>" placeholder="Full name" required>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-calendar"></i>
            <input type="date" name="birthdate" value="<?php echo htmlspecialchars($_SESSION['birthdate'] ?? ''); ?>" required>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-venus-mars"></i>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?php if(($_SESSION['gender']??'')=='Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if(($_SESSION['gender']??'')=='Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if(($_SESSION['gender']??'')=='Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-location-dot"></i>
            <input type="text" name="address" value="<?php echo htmlspecialchars($_SESSION['address'] ?? ''); ?>" placeholder="Address" required>
        </div>

        <div class="input-icon">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user'] ?? ''); ?>" placeholder="Email" required>
        </div>

        <button type="submit"><i class="fa-solid fa-save"></i> Save Changes</button>
    </form>
</div>

<script>
function previewImg(e){
    const [file] = e.target.files;
    if(file){
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(file);
    }
}
</script>

</body>
</html>

