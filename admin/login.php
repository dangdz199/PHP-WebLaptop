<?php
include_once '../init.php';
include '../includes/db.php'; // Use PDO for the connection

session_start();

// Check if the user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Redirect to the admin dashboard or any other page
    header("Location: index.php");
    die(0);
}

// Initialize error variable
$error = '';

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Perform the login authentication here
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Add your authentication logic here
    // Example: Check if the username and password match a record in the database
    if ($username === 'admin' && $password === '123') {
        // Set the admin session variable
        $_SESSION['admin_logged_in'] = true;

        // Redirect to the admin dashboard or any other page
        header("Location: index.php");
        die(0);
    } else {
        // Invalid credentials, show an error message
        $error = "Invalid username or password";
    }
}

include '../includes/header.php';
?>
    <div class="container mt-5 p-5">
        <h1>Admin Login</h1>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>

    <!-- Add Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
include '../includes/footer.php';
?>
