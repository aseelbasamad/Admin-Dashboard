
<?php
session_start();

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
