
<?php
require_once "../includes/session.php";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit();
}

$admin = $_SESSION["admin"];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">

    <aside class="sidebar">
        <h2>Admin Panel</h2>

        <nav>
            <a href="../dashboard.php">Dashboard</a>
            <a href="profile.php" class="active">Profile</a>
            <a href="settings.php">Settings</a>
            <a href="change_password.php">Change Password</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>

    <div class="admin-main">

        <header class="topbar">
            <h2>My Profile</h2>
            <span>Welcome, <?php echo htmlspecialchars($admin); ?></span>
        </header>

        <main class="dashboard-content">

            <h1>Admin Profile</h1>
            <p>View your account and session information.</p>

            <section class="dashboard-info">
                <h2>Account Information</h2>

                <p>
                    <strong>Username:</strong>
                    <?php echo htmlspecialchars($admin); ?>
                </p>

                <p><strong>Role:</strong> Administrator</p>

                <p><strong>Account Status:</strong> Active</p>
            </section>

            <section class="dashboard-info">
                <h2>Session Information</h2>

                <p><strong>Login Status:</strong> Authenticated</p>

                <p><strong>Session Status:</strong> Active</p>

                <p>
                    <strong>Last Activity:</strong>
                    <?php
                    echo date(
                        "d M Y, h:i:s A",
                        $_SESSION["last_activity"]
                    );
                    ?>
                </p>
            </section>

        </main>
    </div>

</body>
</html>
