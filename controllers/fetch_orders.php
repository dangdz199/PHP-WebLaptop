<?php

// Kiểm tra người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM orders WHERE Username = :Username");
$stmt->execute(['Username' => $_SESSION['user']]); // Giả sử email trùng với Username
$orders = $stmt->fetchAll();

// Tiếp tục với phần hiển thị đơn hàng
?>