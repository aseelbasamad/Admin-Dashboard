


<?php
session_start();

if (isset($_SESSION["admin"])) {
    header("Location: dashboard.php");
    exit();
}

require_once "includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT username, password FROM admins WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($password, $admin["password"])) {
            session_regenerate_id(true);

            $_SESSION["admin"] = $admin["username"];
            $_SESSION["last_activity"] = time();

            header("Location: dashboard.php");
            exit();
        }
    }

    $error = "Invalid username or password!";
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">

    <div class="login-container">
        <h2>Admin Login</h2>
        <p>Please log in to continue.</p>

        <?php if ($error != ""): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>
