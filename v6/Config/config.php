<?php
namespace Config;
	$servername="localhost";
	$user= "root";
	$pass= "";
	$dbname="MySite";
	$tbname = "SiteData";
	$password = "";
	$confirm_password = ""; 


try {
    $pdo = new \PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>