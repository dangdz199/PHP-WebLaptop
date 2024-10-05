<?php
include_once 'init.php';

session_start();

$error = ''; // Biến để lưu lỗi nếu có

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Truy vấn người dùng từ cơ sở dữ liệu
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = :username AND IsActive = TRUE");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['PasswordHash'])) {
        // Lưu tên người dùng vào phiên
        $_SESSION['user'] = $user['Username'];
        // Cập nhật thời gian đăng nhập cuối cùng
        $stmt = $pdo->prepare("UPDATE Users SET LastLogin = CURRENT_TIMESTAMP WHERE UserID = :userid");
        $stmt->execute(['userid' => $user['UserID']]);
        header('Location: index.php'); // Chuyển hướng tới trang quản lý
        exit;
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    }
}

include 'includes/header.php';
?>
<div class="container mt-5 p-5">
    <h1>Người dùng đăng nhập</h1>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

    <p class="mt-3">Chưa có tài khoản? <a href="register.php">Đăng ký ngay!</a></p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
include 'includes/footer.php';
?>
