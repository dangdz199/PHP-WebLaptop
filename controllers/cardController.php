<?php
include './includes/db.php';

// Khởi tạo giỏ hàng nếu chưa có
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 0;
    }

    $_SESSION['cart'][$productId] += $quantity;
    header('Location: cart.php');
    exit;
}

// Lấy danh sách sản phẩm trong giỏ hàng
$productIds = array_keys($_SESSION['cart']);
if (count($productIds) > 0) {
    $query = "SELECT * FROM products WHERE id IN (" . implode(',', $productIds) . ")";
    $stmt = $pdo->query($query);
    $products = $stmt->fetchAll();
} else {
    $products = [];
}
?>