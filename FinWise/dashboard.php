<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<nav class="top-nav">
    <div class="nav-left">
        <span class="wave">👋</span>
        <span class="welcome-text">WELCOME <?php echo strtoupper($_SESSION['user']); ?>!</span>
    </div>

    <ul class="nav-links">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="transactions.php">Transactions</a></li>
        <li><a href="analysis.php">Analysis</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
</nav>
