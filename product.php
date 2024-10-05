<?php
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$productId = intval($_GET['id']);

// Lấy thông tin sản phẩm
$query = "SELECT * FROM products WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}
?>

<main class="main-content p-5">
    <div class="row">
        <!-- Cột chứa hình ảnh sản phẩm -->
        <div class="col-md-6 mb-4">
            <img src="./assets/images/<?=$product['image']?>" class="img-fluid rounded shadow-sm p-3" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <!-- Cột chứa thông tin sản phẩm -->
        <div class="col-md-6 mb-4">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
            <p class="h4 text-primary"><?php echo number_format($product['price'], 2); ?> VNĐ</p>
            
            <form action="cart.php" method="post" class="mt-3">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="form-group">
                    <label for="quantity">Số lượng:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1">
                </div>
                <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>
</main>


<?php
    include 'includes/footer.php';
?>