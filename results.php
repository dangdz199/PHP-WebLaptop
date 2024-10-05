<?php
include 'includes/db.php';
include 'controllers/productController.php';
include 'includes/header.php';


$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'; //Put your secret key in there

if (!empty($_GET)) {
    $partnerCode = $_GET["partnerCode"];
    $accessKey = isset($_GET["accessKey"]) ? $_GET["accessKey"] : '';
    $orderId = $_GET["orderId"];
    $localMessage = isset($_GET["localMessage"]) ? utf8_encode($_GET["localMessage"]) : '';
    $message = $_GET["message"];
    $transId = $_GET["transId"];
    $orderInfo = utf8_encode($_GET["orderInfo"]);
    $amount = $_GET["amount"];
    $errorCode = isset($_GET["errorCode"]) ? $_GET["errorCode"] : '';
    $responseTime = $_GET["responseTime"];
    $requestId = $_GET["requestId"];
    $extraData = $_GET["extraData"];
    $payType = $_GET["payType"];
    $orderType = $_GET["orderType"];
    $extraData = $_GET["extraData"];
    $m2signature = $_GET["signature"]; //MoMo signature


    //Checksum
    $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
        "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
        "&payType=" . $payType . "&extraData=" . $extraData;


    $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);

    echo "<script>console.log('Debug huhu Objects: " . $rawHash . "' );</script>";
    echo "<script>console.log('Debug huhu Objects: " . $secretKey . "' );</script>";
    echo "<script>console.log('Debug huhu Objects: " . $partnerSignature . "' );</script>";

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute(["Completed", $orderId]);
    unset($_SESSION['cart']);
} else {
    header('Location: index.php');
    exit;
}
?>

<?php
echo '
                     <main class="container mt-4">
                         <div class="alert alert-info" role="alert">
                         <h4 class="alert-heading">Payment status/Kết quả thanh toán</h4>
                         <p class="mb-0">Thanh toán thành công!</p>
                         </div>
                     </main>
                     ';
?>

<?php
include 'includes/footer.php';
?>