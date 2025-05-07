<?php
include '../Config/connect.php';
include '../Config/config.php';
require '../index1.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $first_name, $last_name, $email, $password]);
    echo "Registration successful!<br>";
    
}
?>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    First Name: <input type="text" name="first_name" required><br>
    Last Name: <input type="text" name="last_name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Register">
    <p><a href="../index1.php">Back to Home</a></p>
</form>