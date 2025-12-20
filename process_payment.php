<?php
session_start();
// Prevent any warnings from breaking JSON output
error_reporting(0); 
include 'db.php'; 

$keyId = "rzp_test_RqeUyvsrea1Qdx"; 
$keySecret = "DypnwCtjMOpiwBcJmZKkeYbd"; 

require('razorpay-php-master/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json');

$response = ['status' => 'failed', 'error' => 'Unknown error', 'booking_id' => null];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['booking'])) {
    $response['error'] = "Session expired or invalid request.";
    echo json_encode($response);
    exit;
}

$payment_id = $_POST['razorpay_payment_id'] ?? '';
$order_id = $_POST['razorpay_order_id'] ?? '';
$signature = $_POST['razorpay_signature'] ?? '';
$booking_id = intval($_POST['booking_id']);
$room_type_id = intval($_SESSION['booking']['room_id']); 

try {
    $api = new Api($keyId, $keySecret);
    $attributes = [
        'razorpay_order_id' => $order_id,
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $signature
    ];

    $api->utility->verifyPaymentSignature($attributes);
    
    // Find room
    $sql_find = "SELECT room_number FROM room_numbers 
                 WHERE room_type_id = $room_type_id AND status = 'Available' 
                 LIMIT 1";
    $result_find = mysqli_query($conn, $sql_find);

    if ($result_find && mysqli_num_rows($result_find) > 0) {
        $row = mysqli_fetch_assoc($result_find);
        $assigned_room = $row['room_number'];

        // Start transaction for safety
        mysqli_begin_transaction($conn);

        $upd_room = mysqli_query($conn, "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$assigned_room'");
        
        $sql_update = "UPDATE bookings SET 
                       status = 'Confirmed', 
                       payment_status = 'Paid', 
                       razorpay_id = '$payment_id', 
                       room_number = '$assigned_room' 
                       WHERE id = $booking_id";
        $upd_book = mysqli_query($conn, $sql_update);

        if ($upd_room && $upd_book) {
            mysqli_commit($conn);
            $response['status'] = 'success';
            $response['booking_id'] = $booking_id;
            unset($_SESSION['booking']); 
        } else {
            mysqli_rollback($conn);
            $response['error'] = "Database update failed: " . mysqli_error($conn);
        }
    } else {
        $response['error'] = "No rooms available in this category.";
    }
} catch(SignatureVerificationError $e) {
    $response['error'] = "Signature verification failed.";
} catch(\Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
exit;