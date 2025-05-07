<?php
include '../Config/connect.php';
include '../Config/config.php';
require '../index1.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        logAttempt($username, true);
        //echo "Login successful!<br>";
        header('Location: dashboard.php');
    } else {
        logAttempt($username, false);
        echo "Login failed!<br>";
    }
}
function logAttempt($username, $success) {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO logs (username, success, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$username, $success, $ip]);
}
?>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
    <p>YOU HAVE LOGGED IN</p>
</form>