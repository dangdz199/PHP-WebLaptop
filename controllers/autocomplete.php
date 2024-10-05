<?php
include '../includes/db.php';

if (isset($_GET['term'])) {
    $searchTerm = $_GET['term'];

    // Đảm bảo từ khóa tìm kiếm không quá ngắn
    if (strlen($searchTerm) > 0) {
        // Sử dụng SQL LIKE để tìm các tên sản phẩm bắt đầu bằng ký tự tìm kiếm
        $query = $pdo->prepare("
            SELECT name 
            FROM products 
            WHERE name LIKE :term 
            ORDER BY name ASC 
            LIMIT 10
        ");
        // Thay %$searchTerm% bằng $searchTerm% để chỉ lọc những tên sản phẩm bắt đầu bằng ký tự tìm kiếm
        $query->execute(['term' => "$searchTerm%"]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        // Trả về dữ liệu JSON
        echo json_encode(array_column($results, 'name'));
    } else {
        // Trả về mảng rỗng nếu từ khóa tìm kiếm quá ngắn
        echo json_encode([]);
    }
}
?>
