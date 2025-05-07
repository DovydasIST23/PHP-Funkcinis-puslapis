<?php
require 'Config/autoload.php';
use Config\Config;
use Config\Connect;

session_start();
include 'Config/connect.php';
include 'Config/config.php';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'] ?: bin2hex(random_bytes(4)); // Generate password if not provided
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $first_name, $last_name, $email, $hashed_password]);
    
    echo "Registration successful! Your password is: $password<br>";
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
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

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Login or Register</title>
    <style>
        .form-container { display: none; }
    </style>
</head>
<body>
    <button onclick="showLogin()">Login</button>
    <button onclick="showRegister()">Register</button>

    <div id="loginForm" class="form-container" style="display:block;">
        <h2>Login</h2>
        <form method="POST">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" name="login" value="Login">
        </form>
    </div>

    <div id="registerForm" class="form-container">
        <h2>Register</h2>
        <form method="POST">
            Username: <input type="text" name="username" required><br>
            First Name: <input type="text" name="first_name" required><br>
            Last Name: <input type="text" name="last_name" required><br>
            Email: <input type="email" name="email" required><br>
            Password: <input type="password" name="password"><br> <!-- Optional -->
            <input type="submit" name="register" value="Register">
        </form>
    </div>

    <script>
        function showLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        }
        function showRegister() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }
    </script>
</body>
</html>
