<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update') {
            // Cập nhật số lượng
            foreach ($_POST['quantity'] as $productId => $quantity) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$productId] = (int)$quantity;
                } else {
                    unset($_SESSION['cart'][$productId]); // Xóa sản phẩm nếu số lượng là 0
                }
            }
        } elseif ($_POST['action'] === 'remove') {
            // Xóa sản phẩm
            if (isset($_POST['product_id'])) {
                $productId = $_POST['product_id'];
                if (isset($_SESSION['cart'][$productId])) {
                    unset($_SESSION['cart'][$productId]); // Xóa sản phẩm khỏi giỏ hàng
                }
            }
        }
    }
}

// Chuyển hướng về trang giỏ hàng sau khi cập nhật
header('Location: ../cart.php');
exit;
?>
