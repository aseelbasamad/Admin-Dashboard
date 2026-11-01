
<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit();
}

$admin = $_SESSION["admin"];
$message = "";
$error = "";

// Read current session timeout
$current_timeout = 5;

$stmt = $conn->prepare(
    "SELECT setting_value FROM app_settings
     WHERE setting_key = 'session_timeout'"
);

if ($stmt) {
    $stmt->execute();
    $stmt->bind_result($timeout_value);

    if ($stmt->fetch()) {
        $current_timeout = (int) $timeout_value;
    }

    $stmt->close();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Change username
    if (isset($_POST["change_username"])) {

        $new_username = trim($_POST["new_username"] ?? "");

        if (strlen($new_username) < 3 || strlen($new_username) > 50) {
            $error = "Username must be between 3 and 50 characters.";
        } elseif ($new_username === $admin) {
            $error = "This is already your username.";
        } else {
            $stmt = $conn->prepare(
                "UPDATE admins SET username = ? WHERE username = ?"
            );

            if ($stmt) {
                $stmt->bind_param("ss", $new_username, $admin);

                if ($stmt->execute() && $stmt->affected_rows === 1) {
                    $_SESSION["admin"] = $new_username;
                    $admin = $new_username;
                    $message = "Username changed successfully!";
                } else {
                    $error = "Could not change username. It may already be taken.";
                }

                $stmt->close();
            } else {
                $error = "Database error. Could not change username.";
            }
        }
    }

    // Save session timeout
    if (isset($_POST["save_timeout"])) {

        $new_timeout = (int) ($_POST["session_timeout"] ?? 0);

        if (in_array($new_timeout, [5, 10, 15], true)) {

            $stmt = $conn->prepare(
                "UPDATE app_settings
                 SET setting_value = ?
                 WHERE setting_key = 'session_timeout'"
            );

            if ($stmt) {
                $value = (string) $new_timeout;
                $stmt->bind_param("s", $value);

                if ($stmt->execute() && $stmt->affected_rows >= 0) {
                    $current_timeout = $new_timeout;
                    $message = "Session timeout updated successfully!";
                } else {
                    $error = "Could not update the session timeout.";
                }

                $stmt->close();
            } else {
                $error = "Database error. Could not update the timeout.";
            }
        } else {
            $error = "Please select a valid timeout.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="admin-body">

    <aside class="sidebar">
        <h2>Admin Panel</h2>

        <nav>
            <a href="../dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="settings.php" class="active">Settings</a>
            <a href="change_password.php">Change Password</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>

    <div class="admin-main">

        <header class="topbar">
            <h2>Settings</h2>
            <span>Welcome, <?php echo htmlspecialchars($admin); ?></span>
        </header>

        <main class="dashboard-content">

            <h1>System Settings</h1>
            <p>Manage your dashboard preferences.</p>

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

            <!-- Account Settings -->
            <section class="dashboard-info">
                <h2>Account Settings</h2>

                <p>
                    <strong>Current Username:</strong>
                    <?php echo htmlspecialchars($admin); ?>
                </p>

                <form method="POST" action="">
                    <label for="new_username">New Username</label>

                    <input
                        type="text"
                        id="new_username"
                        name="new_username"
                        minlength="3"
                        maxlength="50"
                        required
                    >

                    <button type="submit" name="change_username">
                        Change Username
                    </button>
                </form>

                <p><strong>Role:</strong> Administrator</p>
            </section>

            <!-- Session Timeout Settings -->
            <section class="dashboard-info">
                <h2>Session Timeout</h2>

                <p>
                    Choose how long your account can remain inactive
                    before automatic logout.
                </p>

                <p>
                    <strong>Current Timeout:</strong>
                    <?php echo $current_timeout; ?> minutes
                </p>

                <form method="POST" action="">
                    <label for="session_timeout">Timeout Duration</label>

                    <select name="session_timeout" id="session_timeout" required>
                        <option value="5"
                            <?php if ($current_timeout === 5) echo "selected"; ?>>
                            5 minutes
                        </option>

                        <option value="10"
                            <?php if ($current_timeout === 10) echo "selected"; ?>>
                            10 minutes
                        </option>

                        <option value="15"
                            <?php if ($current_timeout === 15) echo "selected"; ?>>
                            15 minutes
                        </option>
                    </select>

                    <button type="submit" name="save_timeout">
                        Save Timeout
                    </button>
                </form>
            </section>

            <!-- Security Settings -->
            <section class="dashboard-info">
                <h2>Security Settings</h2>

                <p>Session authentication is enabled.</p>
                <p>Protected pages require an active admin session.</p>
                <p>Automatic session expiration is enabled.</p>
            </section>

        </main>
    </div>

</body>
</html>
