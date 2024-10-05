<!DOCTYPE html>
<html lang="vi">
<?php
include_once dirname(__DIR__) . '/init.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Cửa hàng</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .navbar {
            background-color: #212529;
            /* Màu nền tối cho navbar */
        }

        .navbar-brand {
            font-weight: bold;
            color: #f8f9fa;
            /* Màu chữ sáng */
        }

        .navbar-nav .nav-link {
            color: #f8f9fa;
            /* Màu chữ sáng cho liên kết */
            margin-right: 15px;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff;
            /* Màu chữ sáng khi hover */
        }

        footer {
            background-color: #212529;
            /* Màu nền tối */
            color: #f8f9fa;
            /* Màu chữ sáng */
            padding: 20px 0;
            /* Khoảng cách trên và dưới */
            text-align: center;
            /* Căn giữa nội dung */
            position: relative;
            /* Đặt footer vào vị trí chính */
            bottom: 0;
            /* Đảm bảo footer luôn nằm ở cuối trang */
            width: 100%;
            /* Chiếm toàn bộ chiều rộng */
            border-top: 1px solid #343a40;
            /* Đường viền trên cho footer */
        }

        footer p {
            margin: 0;
            /* Loại bỏ khoảng cách mặc định của các thẻ p */
            font-size: 14px;
            /* Kích thước chữ cho dễ đọc */
        }

        footer a {
            color: #007bff;
            /* Màu chữ liên kết */
            text-decoration: none;
            /* Loại bỏ gạch chân */
        }

        footer a:hover {
            color: #0056b3;
            /* Màu chữ khi hover */
            text-decoration: underline;
            /* Gạch chân khi hover */
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <a class="navbar-brand" href="index.php">DB Store</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($currentPage, 'index.php') !== false ? 'active' : ''; ?>"
                            href="<?= $baseURL ?>index.php">
                            Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($currentPage, 'cart.php') !== false ? 'active' : ''; ?>"
                            href="<?= $baseURL ?>cart.php">
                            Giỏ hàng
                            <?= isset($_SESSION['cart']) && count($_SESSION['cart']) ? '<span class="badge badge-secondary">' . count($_SESSION['cart']) . '</span>' : '' ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($currentPage, 'checkout.php') !== false ? 'active' : ''; ?>"
                            href="<?= $baseURL ?>checkout.php">
                            Thanh toán
                        </a>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#cartModal">
                            <i class="fa fa-shopping-cart"></i>
                        </button>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user'])):
                        $stmt = $pdo->prepare("SELECT * FROM orders WHERE Username = :user");
                        $stmt->execute(['user' => $_SESSION['user']]); // Giả sử email trùng với Username
                        $orders = $stmt->fetchAll();
                        $orderCount = count($orders);
                        ?>
                        <li class="nav-item">
                            <span class="navbar-text text-white ml-2">
                                Xin chào, <?= htmlspecialchars($_SESSION['user']); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseURL ?>orders.php">Đơn hàng của bạn
                                <?= $orderCount ? '<span class="badge badge-secondary">' . $orderCount . '</span>' : '' ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseURL ?>logout.php">Đăng xuất</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseURL ?>login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseURL ?>register.php">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <!-- Modal Giỏ Hàng -->
        <div class="modal fade" id="cartModal" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cartModalLabel">Giỏ hàng của bạn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="cart-content">
                            <!-- Nội dung giỏ hàng sẽ được chèn vào đây -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="checkout.php" class="btn btn-primary">Thanh toán</a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </header>