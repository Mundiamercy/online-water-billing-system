<!-- user_navbar.php -->
<nav>
    <div class="nav-left">
        Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>
    </div>
    <div class="nav-right">
        <a href="user_dashboard.php">Dashboard</a>
        <a href="user_water_usage.php">Water Usage</a>
        <a href="user_change_password.php">Change Password</a>
        <a href="user_logout.php">Logout</a>
    </div>
</nav>
