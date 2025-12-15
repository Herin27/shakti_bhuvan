<?php
session_start();

// --- 1. Basic Error Check and Includes ---
include 'db.php'; 

// Razorpay config
$keyId = "rzp_test_RqeUyvsrea1Qdx";
$keySecret = "DypnwCtjMOpiwBcJmZKkeYbd"; 

require('razorpay-php-master/Razorpay.php'); // 🚨 VERIFY THIS PATH
use Razorpay\Api\Api;
use Razorpay\Errors\SignatureVerificationError;

header('Content-Type: application/json');

$response = [
    'status' => 'failed',
    'error' => 'Invalid or incomplete payment data.'
];

if (!isset($_SESSION['booking']) || $_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST['razorpay_payment_id'])) {
    echo json_encode($response);
    exit;
}

try {
    $api = new Api($keyId, $keySecret);
    $booking = $_SESSION['booking'];

    $attributes = array(
        'razorpay_order_id' => $_POST['razorpay_order_id'],
        'razorpay_payment_id' => $_POST['razorpay_payment_id'],
        'razorpay_signature' => $_POST['razorpay_signature']
    );

    // --- 2. Verify the Payment Signature ---
    $api->utility->verifyPaymentSignature($attributes);
    
    // --- 3. Payment Verified: Insert into Database ---
    
    // Data from Session (Ensure these keys exist when running the 'booking' page!)
    $room_id = $booking['room_id'] ?? 0; // ⚠️ Ensure you set a valid room_id in your booking session
    $name = $booking['customer_name'] ?? 'Guest';
    $email = $booking['email'] ?? 'N/A';
    $phone = $booking['phone'] ?? 'N/A';
    $check_in = $booking['checkin'];
    $check_out = $booking['checkout'];
    $total_price = $booking['total_price'];
    $nights = $booking['nights'];

    // Data from Razorpay
    $payment_id = $_POST['razorpay_payment_id'];
    $order_id = $_POST['razorpay_order_id'];
    $status = 'Confirmed';

    // SQL INSERT Query
    $stmt = $conn->prepare("INSERT INTO bookings 
        (room_id, customer_name, email, phone, check_in, check_out, total_price, nights, status, payment_id, order_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
    // Type definition string: i=int, s=string, d=double/decimal
    // You have 2 INTs (room_id, nights) and 1 DECIMAL (total_price), the rest are STRINGS.
    // i s s s s s d i s s s 
    $stmt->bind_param("issssdsisss", 
        $room_id, $name, $email, $phone, 
        $check_in, $check_out, $total_price, $nights, 
        $status, $payment_id, $order_id);

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;
        
        // --- 4. Success: Clear Session & Return Success ---
        unset($_SESSION['booking']);
        $response['status'] = 'success';
        $response['booking_id'] = $booking_id;
        $response['message'] = "Booking confirmed with ID #$booking_id.";
    } else {
        // Database execution failed
        $response['error'] = 'DB Insertion Failed: ' . $conn->error;
    }
    
    $stmt->close();
    $conn->close();

} catch(SignatureVerificationError $e) {
    // Razorpay signature verification failed
    $response['error'] = 'Razorpay Signature Error: ' . $e->getMessage();
} catch(\Throwable $e) {
    // General runtime error (e.g., missing variable, file not found)
    $response['error'] = 'General Server Error: ' . $e->getMessage();
}

echo json_encode($response);
exit;
?>