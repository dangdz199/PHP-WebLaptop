<?php
session_start();
// Khởi tạo danh sách sản phẩm đã đánh dấu nếu chưa có
if (!isset($_SESSION['bookmarked_products'])) {
    $_SESSION['bookmarked_products'] = [];
}

// Xử lý hành động đánh dấu sản phẩm
if (isset($_GET['action']) && $_GET['action'] === 'bookmark' && isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']);

    // Kiểm tra nếu sản phẩm đã được đánh dấu
    if (in_array($productId, $_SESSION['bookmarked_products'])) {
        // Xóa dấu trang nếu đã tồn tại
        $_SESSION['bookmarked_products'] = array_diff($_SESSION['bookmarked_products'], [$productId]);
    } else {
        // Thêm dấu trang nếu chưa tồn tại
        $_SESSION['bookmarked_products'][] = $productId;
    }

    // Điều hướng lại trang
    header('Location: index.php');
    exit;
}

$categoriesStmt = $pdo->query("SELECT * FROM ProductCategory");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Các phần còn lại của mã
$itemsPerPage = 3; // Số sản phẩm mỗi trang
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Trang hiện tại
$page = max($page, 1); // Đảm bảo trang là số dương
$offset = ($page - 1) * $itemsPerPage; // Tính toán offset

// Xử lý tìm kiếm
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Xử lý sắp xếp
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'price DESC'; // Sắp xếp theo giá theo mặc định

// Xử lý sắp xếp bằng category_id
$sortCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

$categoryCondition = '';
if ($sortCategory !== 'all') {
    $categoryCondition = ' AND category_id = :category_id';
}

// Lấy tổng số sản phẩm có lọc theo danh mục
$totalQuery = "SELECT COUNT(*) as total FROM products WHERE name LIKE :search $categoryCondition";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);

if ($sortCategory !== 'all') {
    $totalStmt->bindValue(':category_id', $sortCategory, PDO::PARAM_INT);
}
$totalStmt->execute();
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $itemsPerPage);

// Lấy danh sách sản phẩm với phân trang và lọc theo danh mục
$query = "SELECT * FROM products WHERE name LIKE :search $categoryCondition ORDER BY $sortOrder LIMIT :offset, :limit";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);

if ($sortCategory !== 'all') {
    $stmt->bindValue(':category_id', $sortCategory, PDO::PARAM_INT);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Danh sách sản phẩm đã đánh dấu từ session
$bookmarkedProductIds = $_SESSION['bookmarked_products'];

?>