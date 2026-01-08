<?php
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$name    = trim($_POST["name"] ?? "");
$email   = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");
$ip      = $_SERVER["REMOTE_ADDR"];

if ($name === "" || $email === "" || $message === "") {
    die("Thiếu dữ liệu!");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email không hợp lệ!");
}

$stmt = $conn->prepare(
    "INSERT INTO contacts (name, email, message, ip_address)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $message, $ip);
$stmt->execute();

echo "<script>
    alert('Gửi liên hệ thành công!');
    window.location='index.php#contact';
</script>";
