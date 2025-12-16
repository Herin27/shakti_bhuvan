<?php
session_start();
// Include database connection
include 'db.php'; 

// --- Razorpay config ---
// NOTE: For security, the keySecret should NOT be hardcoded here, 
// but fetched from a secure config file.
$keyId = "rzp_test_RqeUyvsrea1Qdx";
$keySecret = "DypnwCtjMOpiwBcJmZKkeYbd";

require('razorpay-php-master/Razorpay.php'); // Ensure this path is correct
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json'); // Set header for JSON response

$response = [
    'status' => 'failed',
    'error' => 'An unknown error occurred.',
    'booking_id' => null
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['booking'])) {
    $response['error'] = "Invalid request or session expired.";
    echo json_encode($response);
    exit;
}

// Data received from Razorpay handler on the client side
$payment_id = $_POST['razorpay_payment_id'] ?? null;
$order_id = $_POST['razorpay_order_id'] ?? null;
$signature = $_POST['razorpay_signature'] ?? null;
$booking_id = intval($_POST['booking_id']); // DB booking ID

$expected_order_id = $_SESSION['booking']['razorpay_order_id'] ?? null;
$expected_booking_id = intval($_SESSION['booking']['booking_id']);

if ($booking_id !== $expected_booking_id || $order_id !== $expected_order_id) {
    $response['error'] = "Mismatch in booking IDs. Possible tampering.";
    echo json_encode($response);
    exit;
}

if (empty($payment_id) || empty($signature) || empty($order_id)) {
    $response['error'] = "Missing payment verification parameters.";
    echo json_encode($response);
    exit;
}

try {
    $api = new Api($keyId, $keySecret);

    // Verify the payment signature
    $attributes = array(
        'razorpay_order_id' => $order_id,
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $signature
    );

    $api->utility->verifyPaymentSignature($attributes);
    
    // --- SIGNATURE VERIFIED: UPDATE DATABASE ---
    
    // 1. Update booking status
    $sql_update_booking = "UPDATE bookings 
                           SET status = 'Confirmed', payment_status = 'Paid', razorpay_id = '$payment_id' 
                           WHERE id = ?";
    
    // Use prepared statements for security
    $stmt = $conn->prepare($sql_update_booking);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        // 2. Update user stats (Bookings + Total Spent) - Already done in process_booking, but if you want to update it HERE:
        // You would need to fetch customer_id and total_price from the bookings table or session

        // 3. Success Response
        $response['status'] = 'success';
        $response['booking_id'] = $booking_id;
        $response['message'] = "Payment verified and booking confirmed.";
        
        // 4. Clear the session to prevent re-payment
        unset($_SESSION['booking']);
        
    } else {
        $response['error'] = "Payment verified but database update failed.";
        // IMPORTANT: Log this error and manually check the database!
    }
    
} catch(SignatureVerificationError $e) {
    $response['error'] = "Razorpay Signature Verification Failed: " . $e->getMessage();
    
} catch (\Exception $e) {
    $response['error'] = "General Error during verification: " . $e->getMessage();
}

echo json_encode($response);
// Ensure we exit after sending the JSON response
exit;
?>