

<?php
require_once "includes/session.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

$admin = $_SESSION["admin"];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="admin-body">

    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="pages/profile.php">Profile</a>
            <a href="pages/settings.php">Settings</a>
            <a href="pages/change_password.php">Change Password</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="topbar">
            <h2>Dashboard</h2>
            <span>Welcome, <?php echo htmlspecialchars($admin); ?></span>
        </header>

        <main class="dashboard-content">
            <h1>Welcome, <?php echo htmlspecialchars($admin); ?>!</h1>
            <p>You have successfully logged in.</p>

            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Users</h3>
                    <p>25</p>
                </div>

                <div class="card">
                    <h3>Active Sessions</h3>
                    <p>1</p>
                </div>

                <div class="card">
                    <h3>System Status</h3>
                    <p>Online</p>
                </div>
            </div>

            <section class="dashboard-info">
                <h2>Session Information</h2>
                <p><strong>Logged-in Admin:</strong>
                    <?php echo htmlspecialchars($admin); ?>
                </p>
                <p><strong>Session Status:</strong> Active</p>
                <p><strong>Authentication:</strong> Verified</p>
                <p><strong>Last Activity:</strong>
                    <?php echo date("d M Y, h:i:s A", $_SESSION["last_activity"]); ?>
                </p>
            </section>
        </main>
    </div>

</body>
</html>
