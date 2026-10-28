
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['uid'])) {
    header('Location: /login.php');
    exit;
}
?>
