<?php
$servername = "localhost";
$host = "localhost";
$username = "root";
$password = "";
$database = "lab_task";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>