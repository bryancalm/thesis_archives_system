<?php
$host = "localhost";
$dbname = "thesis_management";
$username = "root";
$password = ""; // set your DB password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?>
