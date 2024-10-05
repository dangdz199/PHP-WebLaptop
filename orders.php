<?php
// Đặt session_start() ở đầu tệp để đảm bảo không có xuất dữ liệu trước
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Kiểm tra người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Lấy đơn hàng của người dùng
$stmt = $pdo->prepare("SELECT * FROM orders WHERE Username = :user");
$stmt->execute(['user' => $_SESSION['user']]); // Giả sử email trùng với Username
$orders = $stmt->fetchAll();

?>
<main>
    <div class="container mt-5">
        <h1>Đơn hàng của bạn</h1>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th>Sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái giao hàng</th>
                            <th>Ngày đặt hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1; // Bắt đầu từ 1
                        foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($i++); ?></td>
                                <td><?= htmlspecialchars($order['email']); ?></td>
                                <td><?= htmlspecialchars($order['address']); ?></td>
                                <td>
                                    <ul>
                                        <?php
                                        $products = json_decode($order['all_products'], true);
                                        foreach ($products as $product) {
                                            echo '<li>' . htmlspecialchars($product['name']) . ' x ' . htmlspecialchars($product['quantity']) . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </td>
                                <td>
                                    <?= number_format($order['total'], 0, ) . ' VNĐ';
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($order['status']); ?></td>
                                <td><?= htmlspecialchars($order['payment_status']); ?></td>
                                <td><?= htmlspecialchars($order['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>