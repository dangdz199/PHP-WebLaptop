<?php
include_once 'init.php';
include 'includes/db.php'; // Sử dụng PDO cho kết nối
session_start();
$error = ''; // Biến để lưu lỗi nếu có
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    // Kiểm tra xem tên người dùng hoặc email đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = :username OR Email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    if ($stmt->fetch()) {
        $error = 'Tên người dùng hoặc email đã tồn tại.';
    } else {
        // Băm mật khẩu
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        // Thêm người dùng vào cơ sở dữ liệu
        $stmt = $pdo->prepare("INSERT INTO Users (Username, PasswordHash, Email) VALUES (:username, :passwordHash, :email)");
        $stmt->execute(['username' => $username, 'passwordHash' => $passwordHash, 'email' => $email]);
        header('Location: login.php'); // Chuyển hướng đến trang đăng nhập
        exit;
    }
}
include 'includes/header.php';
?>
<div class="container mt-5 p-5">
    <h1>Đăng ký</h1>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>

    <p class="mt-3">Đã có tài khoản? <a href="login.php">Đăng nhập ngay!</a></p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
include 'includes/footer.php';
?>
