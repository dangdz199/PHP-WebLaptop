<?php
include 'includes/db.php';
include 'controllers/productController.php';
include 'includes/header.php';
?>
<main>
    <div class="container mt-4">
        <!-- Form tìm kiếm và sắp xếp -->
        <form method="get" action="index.php" class="form-inline mb-4">
            <input type="text" id="search" name="search" class="form-control mr-2" placeholder="Tìm kiếm sản phẩm"
                value="<?php echo htmlspecialchars($searchQuery); ?>">

            <select name="sort" class="form-control mr-2">
                <option value="price ASC" <?php echo $sortOrder == 'price ASC' ? 'selected' : ''; ?>>Giá tăng dần</option>
                <option value="price DESC" <?php echo $sortOrder == 'price DESC' ? 'selected' : ''; ?>>Giá giảm dần
                </option>
                <option value="name ASC" <?php echo $sortOrder == 'name ASC' ? 'selected' : ''; ?>>Tên A-Z</option>
                <option value="name DESC" <?php echo $sortOrder == 'name DESC' ? 'selected' : ''; ?>>Tên Z-A</option>
            </select>
            <select name="category" class="form-control mr-2">
                <option value="all">Tất cả</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>" <?= $sortCategory == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>
        </form>

        <h2>Sản phẩm</h2>
        <hr>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card border-light shadow-sm">
                        <img src="assets/images/<?= htmlspecialchars($product['image']) ?>" class="card-img-top"
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text text-muted"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                            <a href="index.php?action=bookmark&product_id=<?php echo $product['id']; ?>"
                                class="btn m-1 <?php echo in_array($product['id'], $bookmarkedProductIds) ? 'btn-danger' : 'btn-outline-primary'; ?>">
                                <?php echo in_array($product['id'], $bookmarkedProductIds) ? 'Bỏ dấu trang' : 'Dấu trang'; ?>
                            </a>
                            <!-- Thêm Form Thêm vào Giỏ Hàng -->
                            <form action="cart.php" method="post" class="mt-3">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="form-group">
                                    <label for="quantity">Số lượng:</label>
                                    <input type="number" id="quantity" name="quantity" class="form-control" min="1"
                                        value="1">
                                </div>
                                <button type="submit" name="action" value="add" class="btn btn-primary">Thêm vào giỏ
                                    hàng</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?search=<?php echo urlencode($searchQuery); ?>&sort=<?php echo urlencode($sortOrder); ?>&page=<?php echo $page - 1; ?>"
                            aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link"
                            href="?search=<?php echo urlencode($searchQuery); ?>&sort=<?php echo urlencode($sortOrder); ?>&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?search=<?php echo urlencode($searchQuery); ?>&sort=<?php echo urlencode($sortOrder); ?>&page=<?php echo $page + 1; ?>"
                            aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

    </div>
</main>

<?php
include 'includes/footer.php';
?>