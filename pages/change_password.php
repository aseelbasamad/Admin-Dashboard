
<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit();
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT password FROM admins WHERE username = ?"
    );
    $stmt->bind_param("s", $_SESSION["admin"]);
    $stmt->execute();

    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if (!$admin || !password_verify($current_password, $admin["password"])) {
        $error = "Your current password is incorrect!";
    } elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters long!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif ($current_password === $new_password) {
        $error = "Your new password must be different!";
    } else {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "UPDATE admins SET password = ? WHERE username = ?"
        );
        $stmt->bind_param("ss", $new_hash, $_SESSION["admin"]);

        if ($stmt->execute() && $stmt->affected_rows === 1) {
            $message = "Password changed successfully!";
        } else {
            $error = "Could not update your password. Please try again.";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">

    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <a href="../dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="settings.php">Settings</a>
            <a href="change_password.php" class="active">Change Password</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="topbar">
            <h2>Change Password</h2>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["admin"]); ?></span>
        </header>

        <section class="dashboard-content">
            <div class="card">
                <h3>Update Your Password</h3>

                <?php if ($message !== ""): ?>
                    <p style="color: green;">
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                <?php endif; ?>

                <?php if ($error !== ""): ?>
                    <p style="color: red;">
                        <?php echo htmlspecialchars($error); ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password"
                           name="current_password" required>

                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password"
                           name="new_password" minlength="8" required>

                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password"
                           name="confirm_password" minlength="8" required>

                    <button type="submit">Change Password</button>
                </form>
            </div>
        </section>
    </main>

</body>
</html>
