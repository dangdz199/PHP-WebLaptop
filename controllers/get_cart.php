<?php
session_start();
include '../includes/db.php';

// Kiểm tra nếu giỏ hàng có tồn tại và không rỗng
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
    $total = 0;

    echo '<table class="table table-striped">';
    echo '<thead class="thead-dark">';
    echo '<tr><th>Tên sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Tổng</th></tr>';
    echo '</thead><tbody>';

    foreach ($cartItems as $productId => $quantity) {
        $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();
        if ($product) {
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            echo '<tr>';
            echo '<td>' . htmlspecialchars($product['name']) . '</td>';
            echo '<td>' . number_format($product['price'], 2) . ' VNĐ</td>';
            echo '<td>' . htmlspecialchars($quantity) . '</td>';
            echo '<td>' . number_format($subtotal, 2) . ' VNĐ</td>';
            echo '</tr>';
        }
    }

    echo '<tr class="font-weight-bold">';
    echo '<td colspan="3">Tổng cộng:</td>';
    echo '<td>' . number_format($total, 2) . ' VNĐ</td>';
    echo '</tr>';
    echo '</tbody></table>';
} else {
    echo '<p style="color: black">Giỏ hàng của bạn hiện đang rỗng.</p>';
}
?>
