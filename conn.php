<?php
$servername = "localhost"; // XAMPP default
$username = "root"; // Default username
$password = "RidiculousDB"; // Default password (empty in XAMPP)
$database = "ncpr_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//$conn->query("SET time_zone = '+08:00';");

// Return the MySQLi connection object
return $conn;

