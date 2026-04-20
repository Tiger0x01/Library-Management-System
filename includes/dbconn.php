<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbservername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "lms"; 

$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>