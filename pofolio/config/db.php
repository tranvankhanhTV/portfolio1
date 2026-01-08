<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "portfolio_db";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
