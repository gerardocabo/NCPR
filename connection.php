<?php
$servername = "localhost"; // XAMPP default
$username = "root"; // Default username
$password = "RidiculousDB"; // Default password (empty in XAMPP)
$database = "ncpr_db"; // Your database name

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set default fetch mode
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Set MySQL session timezone to Philippine Time
    //$pdo->exec("SET time_zone = '+08:00';"); // or 'Asia/Manila' if available

    // Return the PDO connection object
    return $pdo;
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
