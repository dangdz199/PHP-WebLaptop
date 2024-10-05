<?php
$host = 'localhost'; // Hoặc địa chỉ của server cơ sở dữ liệu
$db = 'shop';
$user = 'root'; // Tên người dùng cơ sở dữ liệu
$pass = ''; // Mật khẩu cơ sở dữ liệu

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
