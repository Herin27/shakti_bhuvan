<?php
session_start();
include 'db.php';

if (!isset($_POST['code'])) {
    echo json_encode(["success" => false, "message" => "No coupon provided"]);
    exit;
}

$code = trim($_POST['code']);
$today = date("Y-m-d");

$sql = "SELECT * FROM coupons 
        WHERE code = ? 
        AND status = 'active' 
        AND start_date <= ? 
        AND end_date >= ? 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $code, $today, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $coupon = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "discount_percent" => (int)$coupon['discount_percent']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid or expired coupon"]);
}
?>
