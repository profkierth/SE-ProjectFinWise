<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$displayName = $_SESSION['fullname']
    ?? $_SESSION['user']
    ?? 'User';
?>

<nav class="top-nav">
    <div class="nav-left">
        <span class="wave">👋</span>
        <span class="welcome-text">
            WELCOME <?= htmlspecialchars(strtoupper($displayName)) ?>!
        </span>
    </div>

    <ul class="nav-links">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="transactions.php">Transactions</a></li>
        <li><a href="analysis.php">Analysis</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
</nav>
