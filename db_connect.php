<?php
// db_connect.php

$servername = "localhost";
$username = "root";  // Change if your username is different
$password = "";  // Change if your password is different
$dbname = "ecommerce";  // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
