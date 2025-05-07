<?php
namespace Config;
use Config\config;

// Include the configuration file
require 'config.php';

try {
    $conn = new \PDO("mysql:host=$servername;dbname=$dbname",  $user, $pass);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // Exit if the connection fails
}

// Create tables
try {
    // Create users table
    $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // Create logs table
    $sqlLogs = "CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN NOT NULL,
    ip_address VARCHAR(45) NOT NULL
)";

$sqlRec = "CREATE TABLE IF NOT EXISTS records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

    // Execute the SQL statements
    $conn->exec($sqlUsers);
    $conn->exec($sqlLogs);
    $conn->exec($sqlRec);

    echo "Tables created successfully.<br />";
} catch (PDOException $g) {
    echo "Error creating tables: " . $g->getMessage();
}

if (!$conn) {
    die("Connection failed: " . $conn->errorInfo());
}
?>
