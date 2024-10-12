<?php
include 'includes/db.php';
session_start();
include 'includes/header.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '
    <main class="container mt-4">
        <div class="alert alert-info" role="alert">
            Hiện không có sản phẩm nào để thanh toán
        </div>
    </main>
    ';
    include 'includes/footer.php';
    exit;
}

if (!isset($_SESSION['user'])) {
    echo '
    <main class="container mt-4">
        <div class="alert alert-info" role="alert">
           Vui lòng đăng nhập để thanh toán <a href="login.php">Đăng nhập ngay!</a>
        </div>
    </main>
    ';
    include 'includes/footer.php';
    exit;
}
// Lấy thông tin sản phẩm từ giỏ hàng
$products = [];
foreach ($_SESSION['cart'] as $productId => $quantity) {
    $query = "SELECT name, price FROM products WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();
    if ($product) {
        $products[] = array_merge($product, ['quantity' => $quantity]);
    }
}

$total = 0;
foreach ($products as $product) {
    $total += $product['price'] * $product['quantity'];
}
?>

<main class="container mt-4">
    <h2 class="mb-4">Thanh toán</h2>
    <form action="controllers/handle_order.php" method="post">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Thông tin giỏ hàng:</h5>
                <ul class="list-group mb-3">
                    <?php foreach ($products as $product): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($product['name']); ?>
                            <span class="badge rounded-pill"><?php echo number_format($product['price'] * $product['quantity'], 2); ?> VNĐ</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Tổng cộng:</h4>
                    <h4 class="mb-0"><?php echo number_format($total, 2); ?> VNĐ</h4>
                </div>
            </div>
        </div>

        <!-- Thông tin địa chỉ -->
        <div class="form-group">
            <label for="address">Địa chỉ email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">Địa chỉ giao hàng:</label>
            <input type="text" id="address" name="address" class="form-control" required>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="form-group">
            <label for="payment_method">Phương thức thanh toán:</label>
            <select id="payment_method" name="payment_method" class="form-control" required>
                <option value="cash_on_delivery">Thanh toán khi nhận hàng</option>
                <option value="credit_card">Thẻ tín dụng</option>
                <option value="momo">Momo</option>
                <option value="vnpay">VNPay</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
    </form>
</main>
<?php
include 'includes/footer.php';
?>
