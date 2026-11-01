
<?php
date_default_timezone_set("Asia/Kolkata");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db.php";

// Read timeout from MySQL (in minutes)
$timeout_minutes = 5;

$stmt = $conn->prepare(
    "SELECT setting_value FROM app_settings
     WHERE setting_key = 'session_timeout'"
);

if ($stmt) {
    $stmt->execute();
    $stmt->bind_result($setting_value);

    if ($stmt->fetch()) {
        $timeout_minutes = (int) $setting_value;
    }

    $stmt->close();
}

$timeout = max(1, $timeout_minutes) * 60;

if (isset($_SESSION["admin"])) {
    if (isset($_SESSION["last_activity"])) {
        $inactive_time = time() - $_SESSION["last_activity"];

        if ($inactive_time >= $timeout) {
            $_SESSION = [];
            session_destroy();

            header("Location: /Admin-Dashboard/login.php?timeout=1");
            exit();
        }
    }

    $_SESSION["last_activity"] = time();
}
?>
