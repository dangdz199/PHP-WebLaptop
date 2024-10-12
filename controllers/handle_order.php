<?php
include '../includes/db.php'; // Đảm bảo đường dẫn đúng đến tệp db.php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra giỏ hàng
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'] || !isset($_SESSION['user']))) {
        header('Location: ../index.php'); // Chuyển hướng về trang chính nếu không có giỏ hàng
        exit;
    }

    $username = $_SESSION['user'];

    // Lấy thông tin từ biểu mẫu
    $email = $_POST['email'];
    $address = $_POST['address'];
    $paymentMethod = $_POST['payment_method'];

    // Tính tổng số tiền và chuẩn bị danh sách sản phẩm
    $total = 0;
    $products = [];
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $query = "SELECT price FROM products WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();
        if ($product) {
            $total += $product['price'] * $quantity;
            // $products[] = ['id' => $productId, 'quantity' => $quantity];
            // them name vao
            $query = "SELECT name FROM products WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $productId]);
            $product_name = $stmt->fetch();
            $products[] = ['id' => $productId, 'quantity' => $quantity, 'name' => $product_name['name']];
        }
    }

    // Chuyển đổi danh sách sản phẩm thành chuỗi JSON
    $allProducts = json_encode($products);

    // Thêm đơn hàng vào bảng orders
    $query = "INSERT INTO orders (id, email, Username, address, all_products, payment_method, total, created_at) 
              VALUES (:id, :email, :username, :address, :all_products, :payment_method, :total, NOW())";
    $stmt = $pdo->prepare($query);

    $orderId = time() . "";
    $stmt->execute([
        'id' => $orderId,
        'username' => $username,
        'email' => $email,
        'address' => $address,
        'all_products' => $allProducts,
        'payment_method' => $paymentMethod,
        'total' => $total
    ]);

    // Xóa giỏ hàng
    if ($paymentMethod !== 'momo') {
        header('Location: ../index.php');
        unset($_SESSION['cart']);
        die();
    }

    header('Content-type: text/html; charset=utf-8');

function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        )
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //execute post
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result;
}


$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$serectkey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
$orderInfo = "Thanh toán qua MoMo";
$amount = $total;
$redirectUrl = "http://localhost/PhamDucDang_2230140008/results.php";
$ipnUrl = "http://localhost/PhamDucDang_2230140008/results.php";
$extraData = "";


$requestId = time() . "";
$requestType = "payWithATM";

//before sign HMAC SHA256 signature
$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
$signature = hash_hmac("sha256", $rawHash, $serectkey);
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    "storeId" => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);
$result = execPostRequest($endpoint, json_encode($data));
$jsonResult = json_decode($result, true);  // decode json
header('Location: ' . $jsonResult['payUrl']);

    exit;
} else {
    // Nếu không phải là phương thức POST, chuyển hướng về trang chính
    header('Location: ../index.php');
    exit;
} 