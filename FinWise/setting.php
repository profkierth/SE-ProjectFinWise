    <?php
    session_start();

    if (!isset($_SESSION['user'])) { 
        header('Location:index.php'); 
        exit; 
    }

    $msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        $_SESSION['notify'] = isset($_POST['notify']) ? true : false;
        $_SESSION['theme']  = ($_POST['theme'] === 'dark') ? 'dark' : 'light';

        $msg = 'Settings saved.';
    }


    $notify = $_SESSION['notify'] ?? true;  
    $theme  = $_SESSION['theme']  ?? 'light'; 
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Settings</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>

    <body class="gradient <?= $theme === 'dark' ? 'dark-theme' : '' ?>">

    <div class="nav">
        <a href="dashboard.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="card" style="max-width:480px;margin-top:30px;">
        <h2><i class="fa-solid fa-gear"></i> Settings</h2>

        <?php if($msg): ?>
            <p style="color:green;"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form method="POST">

            <label class="settings-row">
                <span>Notifications</span>
                <input type="checkbox" name="notify" <?= $notify ? 'checked' : '' ?>>
            </label>

            <label class="settings-row">
                <span>Theme</span>
                <select name="theme">
                    <option value="light" <?= $theme==='light' ? 'selected' : '' ?>>Light</option>
                    <option value="dark"  <?= $theme==='dark' ? 'selected' : '' ?>>Dark</option>
                </select>
            </label>

            <button type="submit">
                <i class="fa-solid fa-save"></i> Save Settings
            </button>
        </form>
    </div>
    </body>
    </html>
