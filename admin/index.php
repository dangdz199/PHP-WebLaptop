<?php
include_once '../init.php';
include '../includes/db.php'; // Sử dụng PDO để kết nối

session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] === false) {
    header("Location: login.php");
    exit;
}

// Xử lý đăng xuất
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Xử lý hành động cho đơn hàng: xóa, cập nhật
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_POST['action'] == 'update' && isset($_POST['id']) && isset($_POST['status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    } elseif ($_POST['action'] == 'update' && isset($_POST['id']) && isset($_POST['payment_status'])) {
        $id = $_POST['id'];
        $payment_status =  $_POST['payment_status'];
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $stmt->execute([$payment_status, $id]);
    }
}

// Xử lý hành động cho sản phẩm: thêm, cập nhật, xóa
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Tải lên hình ảnh
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = "../assets/images/$image";
    move_uploaded_file($image_tmp, $image_path);

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image, $category_id]);
} elseif (isset($_POST['update_product']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    if (!empty($_FILES['image']['name'])) {
        // Cập nhật hình ảnh nếu có tệp mới được tải lên
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = '../assets/images/' . $image;
        move_uploaded_file($image_tmp, $image_path);
    } else {
        // Giữ hình ảnh cũ nếu không có tệp mới được tải lên
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $image = $result['image'];
    }
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category_id = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $image, $category_id, $id]);
} elseif (isset($_POST['delete_product']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}

// Xử lý hành động cho người dùng: thêm, cập nhật, xóa
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    // Check if the email already exists in the database
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE Email = ?");
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Email already exists, show a popup
        echo "<script>alert('Email already exists. Please use a different one.');</script>";
    } else {
        // Proceed with inserting the new user if email doesn't exist
        $stmt = $pdo->prepare("INSERT INTO Users (Username, PasswordHash, Email, CreatedAt, IsActive) VALUES (?, ?, ?, NOW(), 1)");
        $stmt->execute([$username, $password, $email]);
        // Success popup
        echo "<script>alert('User added successfully!');</script>";
    }
} elseif (isset($_POST['update_user']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE Users SET Username = ?, Email = ?, IsActive = ? WHERE UserID = ?");
    $stmt->execute([$username, $email, $isActive, $id]);

    // Success popup for update
    echo "<script>alert('User updated successfully!');</script>";
} elseif (isset($_POST['delete_user']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM Users WHERE UserID = ?");
    $stmt->execute([$id]);

    // Success popup for delete
    echo "<script>alert('User deleted successfully!');</script>";
}

// Xử lý hành động cho loại sản phẩm: thêm, cập nhật, xóa
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    // Thêm loại sản phẩm vào cơ sở dữ liệu
    $stmt = $pdo->prepare("INSERT INTO ProductCategory (name) VALUES (?)");
    $stmt->execute([$category_name]);
    echo "<script>alert('Thêm loại sản phẩm thành công!');</script>";
} elseif (isset($_POST['update_category']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $category_name = $_POST['category_name'];

    // Cập nhật loại sản phẩm
    $stmt = $pdo->prepare("UPDATE ProductCategory SET name = ? WHERE id = ?");
    $stmt->execute([$category_name, $id]);
    echo "<script>alert('Cập nhật loại sản phẩm thành công!');</script>";
} elseif (isset($_POST['delete_category']) && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Xóa loại sản phẩm
    $stmt = $pdo->prepare("DELETE FROM ProductCategory WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Xóa loại sản phẩm thành công!');</script>";
}

$categoriesStmt = $pdo->query("SELECT * FROM ProductCategory");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách người dùng
$userStmt = $pdo->query("SELECT * FROM Users");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách tất cả các đơn hàng
$ordersStmt = $pdo->query("SELECT * FROM orders");
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

// Mảng để lưu số lượng bán của từng sản phẩm
$productSales = [];

// Duyệt qua từng đơn hàng và phân tích JSON trong cột all_products
foreach ($orders as $order) {
    $products = json_decode($order['all_products'], true);
    foreach ($products as $product) {
        $productId = $product['id'];
        $productName = $product['name'];
        $productQuantity = $product['quantity'];

        // Cộng dồn số lượng sản phẩm bán ra
        if (!isset($productSales[$productId])) {
            $productSales[$productId] = [
                'name' => $productName,
                'quantity' => 0
            ];
        }
        $productSales[$productId]['quantity'] += $productQuantity;
    }
}

// Sắp xếp sản phẩm bán chạy theo số lượng bán được
usort($productSales, function ($a, $b) {
    return $b['quantity'] - $a['quantity'];
});

// Giới hạn 5 sản phẩm bán chạy nhất
$topProducts = array_slice($productSales, 0, 5);


// Lấy danh sách sản phẩm
$productStmt = $pdo->query("SELECT * FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy tổng số đơn hàng và tổng doanh thu
$orderStatsStmt = $pdo->query("SELECT COUNT(*) AS total_orders, SUM(total) AS total_revenue FROM orders");
$orderStats = $orderStatsStmt->fetch(PDO::FETCH_ASSOC);

// Xác định trang nào để hiển thị
$page = $_GET['page'] ?? 'orders';
?>

<!DOCTYPE html>
<html>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Thêm Chart.js CDN -->
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding: 15px;
            border-right: 1px solid #ddd;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="list-group">
            <a href="index.php?page=main" class="list-group-item list-group-item-action">Trang tổng quan</a>
            <a href="index.php?page=products" class="list-group-item list-group-item-action">Quản lý Sản phẩm</a>
            <a href="index.php?page=categories" class="list-group-item list-group-item-action">Quản lý Loại sản phẩm</a>
            <a href="index.php?page=orders" class="list-group-item list-group-item-action">Quản lý Đơn hàng</a>
            <a href="index.php?page=users" class="list-group-item list-group-item-action">Quản lý Người dùng</a>
        </div>
        <form method="post" class="mt-3">
            <button type="submit" name="logout" class="btn btn-danger btn-block">Đăng xuất</button>
        </form>
    </div>

    <div class="main-content">
        <?php if ($page === 'main'): ?>
            <h2>Thống kê</h2>

            <!-- Thống kê tổng doanh thu -->
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Thống kê Đơn hàng</h4>
                    <p>Tổng số đơn hàng: <?= $orderStats['total_orders'] ?></p>
                    <p>Tổng doanh thu: <?= number_format($orderStats['total_revenue'], 0, ',', '.') ?> VNĐ</p>
                </div>
            </div>

            <!-- Thống kê đơn hàng -->
            <div class="mb-3 mt-3">
                <!-- Biểu đồ cột cho sản phẩm bán chạy -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="card-title">Sản phẩm bán chạy</h4>
                        <canvas id="topProductsChart" height="70"></canvas> <!-- Nơi để vẽ biểu đồ -->
                    </div>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Biểu đồ đường cho doanh thu hàng tháng -->
            
        </div>

        </div>
        <?php

        $monthlyData = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Tạo mảng labels và dữ liệu doanh thu
        $monthlyLabels = [];
        $monthlyRevenue = [];

        foreach ($monthlyData as $row) {
            $monthlyLabels[] = 'Tháng ' . $row['month'];
            $monthlyRevenue[] = $row['revenue'];
        }

        // Chuyển đổi sang JSON để sử dụng trong JavaScript
        $monthlyLabels = json_encode($monthlyLabels);
        $monthlyRevenue = json_encode($monthlyRevenue);
        ?>
        <script>

            // Dữ liệu cho sản phẩm bán chạy
            const topProducts = <?= json_encode(array_column($topProducts, 'name')) ?>;
            const topProductsSales = <?= json_encode(array_column($topProducts, 'quantity')) ?>;

            // Cấu hình biểu đồ cột cho sản phẩm bán chạy
            const ctxTopProducts = document.getElementById('topProductsChart').getContext('2d');
            const topProductsChart = new Chart(ctxTopProducts, {
                type: 'bar',
                data: {
                    labels: topProducts,
                    datasets: [{
                        label: 'Số lượng bán',
                        data: topProductsSales,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const monthlyLabels = <?= $monthlyLabels ?>;
            const monthlyRevenue = <?= $monthlyRevenue ?>;

            // Cấu hình biểu đồ đường cho doanh thu hàng tháng
            const ctxMonthlyRevenue = document.getElementById('monthlyRevenueChart').getContext('2d');
            const monthlyRevenueChart = new Chart(ctxMonthlyRevenue, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: monthlyRevenue,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        </script>

        <!-- Sản phẩm bán chạy -->
        <div class="card mb-3">
            <div class="card-body">

            </div>
        <?php elseif ($page === 'orders'): ?>
            <h2>Quản lý Đơn hàng</h2>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th>Tất cả Sản phẩm</th>
                        <th>Phương thức thanh toán</th>
                        <th>Tổng cộng</th>
                        <th>Trạng thái thanh toán</th>
                        <th>Trạng thái giao hàng</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php
                            $products = json_decode($row['all_products'], true);
                            foreach ($products as $product) {
                                echo '<li>' . $product['name'] . ' x ' . $product['quantity'] . '</li>';
                            }
                            ?></td>
                            <td><?php
                            if ($row['payment_method'] == 'cash_on_delivery') {
                                echo 'Thanh toán khi nhận hàng';
                            } elseif ($row['payment_method'] == 'credit_card') {
                                echo 'Thẻ tín dụng';
                            } elseif ($row['payment_method'] == 'momo') {
                                echo 'Momo';
                            } elseif ($row['payment_method'] == 'paypal') {
                                echo 'PayPal';
                            }
                            ?></td>
                            <td><?php
                            echo number_format($row['total'], 0, ',', '.') . ' VNĐ';
                            ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Pending" <?php if ($row['status'] == 'Pending')
                                            echo 'selected'; ?>>Đang
                                            chờ</option>
                                        <option value="Completed" <?php if ($row['status'] == 'Completed')
                                            echo 'selected'; ?>>
                                            Hoàn thành</option>
                                        <option value="Cancelled" <?php if ($row['status'] == 'Cancelled')
                                            echo 'selected'; ?>>Đã
                                            hủy</option>
                                    </select>
                                    <input type="hidden" name="action" value="update">
                                </form>
                            </td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <select name="payment_status" onchange="this.form.submit()">
                                        <option value="Pending" <?php if ($row['payment_status'] == 'Pending')
                                            echo 'selected'; ?>>Đang
                                            chờ</option>
                                        <option value="Completed" <?php if ($row['payment_status'] == 'Completed')
                                            echo 'selected'; ?>>
                                            Hoàn thành</option>
                                        <option value="Cancelled" <?php if ($row['payment_status'] == 'Cancelled')
                                            echo 'selected'; ?>>Đã
                                            hủy</option>
                                    </select>
                                    <input type="hidden" name="action" value="update">
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($page === 'categories'): ?>
            <?php
            // Lấy danh sách loại sản phẩm
            $categoryStmt = $pdo->query("SELECT * FROM ProductCategory");
            $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2>Loại sản phẩm</h2>
            <div class="card mb-3 mt-3">
                <div class="card-body">
                    <h4>Thêm Loại sản phẩm</h4>
                    <form method="post">
                        <div class="form-group">
                            <label for="category_name">Tên loại sản phẩm</label>
                            <input type="text" name="category_name" id="category_name" class="form-control" required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-primary">Thêm Loại sản phẩm</button>
                    </form>
                </div>
            </div>
            <!-- Form Thêm Loại sản phẩm -->


            <!-- Bảng Loại sản phẩm -->
            <h4 class="mt-4">Danh sách Loại sản phẩm</h4>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['created_at']); ?></td>
                            <td>
                                <!-- Form Cập nhật Loại sản phẩm -->
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#updateModal-<?php echo htmlspecialchars($category['id']); ?>">Cập
                                    nhật</button>

                                <!-- Form Xóa Loại sản phẩm -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">
                                    <input type="hidden" name="delete_category" value="1">
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>

                                <!-- Modal Cập nhật Loại sản phẩm -->
                                <div class="modal fade" id="updateModal-<?php echo htmlspecialchars($category['id']); ?>"
                                    tabindex="-1" role="dialog"
                                    aria-labelledby="updateModalLabel-<?php echo htmlspecialchars($category['id']); ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="updateModalLabel-<?php echo htmlspecialchars($category['id']); ?>">Cập
                                                    nhật Loại sản phẩm</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id"
                                                        value="<?php echo htmlspecialchars($category['id']); ?>">
                                                    <div class="form-group">
                                                        <label for="category_name">Tên loại sản phẩm</label>
                                                        <input type="text" name="category_name" id="category_name"
                                                            class="form-control"
                                                            value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Đóng</button>
                                                    <button type="submit" name="update_category" class="btn btn-primary">Lưu
                                                        thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($page === 'products'): ?>
            <h2>Quản lý Sản phẩm</h2>

            <!-- Form Thêm Sản phẩm -->
            <div class="card mb-3 mt-3">
                <div class="card-body">
                    <h4>Thêm Sản phẩm</h4>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Tên</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Loại sản phẩm</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <?php
                                foreach ($categories as $row):
                                    ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Giá</label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" min="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="image">Hình ảnh</label>
                            <input type="file" name="image" id="image" class="form-control-file" required>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary">Thêm Sản phẩm</button>
                    </form>
                </div>
            </div>

            <!-- Bảng Sản phẩm -->
            <h4 class="mt-4">Danh sách Sản phẩm</h4>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Loại</th>
                        <th>Mô tả</th>
                        <th>Giá</th>
                        <th>Hình ảnh</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php
                            foreach ($categories as $cate) {
                                if ($cate['id'] == $product['category_id']) {
                                    echo htmlspecialchars($cate['name']);
                                    break;
                                }
                            }
                            ?></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                            <td><img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>" width="100"></td>
                            <td><?php echo htmlspecialchars($product['created_at']); ?></td>
                            <td>
                                <!-- Form Cập nhật Sản phẩm -->
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#updateModal-<?php echo htmlspecialchars($product['id']); ?>">Cập nhật</button>

                                <!-- Form Xóa Sản phẩm -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <input type="hidden" name="delete_product" value="1">
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>

                                <!-- Modal Cập nhật Sản phẩm -->
                                <div class="modal fade" id="updateModal-<?php echo htmlspecialchars($product['id']); ?>"
                                    tabindex="-1" role="dialog"
                                    aria-labelledby="updateModalLabel-<?php echo htmlspecialchars($product['id']); ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="updateModalLabel-<?php echo htmlspecialchars($product['id']); ?>">Cập
                                                    nhật Sản phẩm</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id"
                                                        value="<?php echo htmlspecialchars($product['id']); ?>">
                                                    <div class="form-group">
                                                        <label for="name">Tên</label>
                                                        <input type="text" name="name" id="name" class="form-control"
                                                            value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="category_id">Loại sản phẩm</label>
                                                        <select name="category_id" id="category_id" class="form-control">
                                                            <?php foreach ($categories as $row): ?>
                                                                <option value="<?= $row['id'] ?>"
                                                                    <?= ($row['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                                                    <?= $row['name'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Mô tả</label>
                                                        <textarea name="description" id="description"
                                                            class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="price">Giá</label>
                                                        <input type="number" name="price" id="price" class="form-control"
                                                            value="<?php echo htmlspecialchars($product['price']); ?>"
                                                            step="0.01" min="0.01" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="image">Hình ảnh</label>
                                                        <input type="file" name="image" id="image" class="form-control-file">
                                                        <small>Để trống để giữ hình ảnh hiện tại.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Đóng</button>
                                                    <button type="submit" name="update_product" class="btn btn-primary">Lưu thay
                                                        đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($page === 'users'): ?>
            <h2>Quản lý Người dùng</h2>


            <div class="card mb-3 mt-3">
                <div class="card-body">
                    <!-- Form Thêm Người dùng -->
                    <h4>Thêm Người dùng</h4>
                    <form method="post">
                        <div class="form-group">
                            <label for="username">Tên đăng nhập</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-primary">Thêm Người dùng</button>
                    </form>
                </div>
            </div>

            <!-- Bảng Người dùng -->
            <h4 class="mt-4">Danh sách Người dùng</h4>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Ngày tạo</th>
                        <th>Hoạt động</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                            <td><?php echo htmlspecialchars($user['Username']); ?></td>
                            <td><?php echo htmlspecialchars($user['Email']); ?></td>
                            <td><?php echo htmlspecialchars($user['CreatedAt']); ?></td>
                            <td><?php echo $user['IsActive'] ? 'Có' : 'Không'; ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['UserID']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" name="delete_user">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>

    <!-- Thêm JS của Bootstrap và các phụ thuộc -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>