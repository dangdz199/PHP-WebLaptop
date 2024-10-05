<?php
include 'controllers/cardController.php';
include 'includes/header.php';
?>
<main class="container mt-4">
    <h2 class="mb-4">Giỏ hàng</h2>
    <?php if (count($products) > 0): ?>
        <form action="controllers/update_cart.php" method="post">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($products as $product):
                        $quantity = $_SESSION['cart'][$product['id']];
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo number_format($product['price'], 2); ?> VNĐ</td>
                            <td>
                                <input type="number" name="quantity[<?php echo $product['id']; ?>]" value="<?php echo $quantity; ?>" min="1" class="form-control d-inline-block w-auto" />
                            </td>
                            <td><?php echo number_format($subtotal, 2); ?> VNĐ</td>
                            <td>
                                <!-- Thêm hidden input để gửi ID sản phẩm -->
                                <form action="controllers/update_cart.php" method="post" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="action" value="remove" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold">
                        <td colspan="3">Tổng cộng:</td>
                        <td><?php echo number_format($total, 2); ?> VNĐ</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between">
                <button type="submit" name="action" value="update" class="btn btn-secondary">Cập nhật giỏ hàng</button>
                <a href="checkout.php" class="btn btn-primary">Thanh toán</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Giỏ hàng của bạn hiện đang rỗng.
        </div>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
