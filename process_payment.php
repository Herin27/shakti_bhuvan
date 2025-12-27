<?php
session_start();
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
    // --- STEP 1: તે ચોક્કસ તારીખે ખાલી હોય તેવો રૂમ શોધો ---
// --- Updated Step 1: Find a room that is free Online AND Offline ---
$checkin = $_SESSION['booking']['checkin'];
$checkout = $_SESSION['booking']['checkout'];
$room_type_id = intval($_SESSION['booking']['room_id']);

$sql_assign = "SELECT rn.room_number 
               FROM room_numbers rn 
               WHERE rn.room_type_id = $room_type_id 
               AND rn.status != 'Maintenance'
               AND rn.room_number NOT IN (
                   /* ૧. Online Bookings ચેક કરો */
                   SELECT b.room_number 
                   FROM bookings b 
                   WHERE b.room_id = $room_type_id 
                   AND b.status IN ('Confirmed', 'Checked-in') 
                   AND b.room_number IS NOT NULL
                   AND NOT (b.checkout <= '$checkin' OR b.checkin >= '$checkout')
               ) 
               AND rn.room_number NOT IN (
                   /* ૨. Offline Bookings ચેક કરો */
                   SELECT o.room_number 
                   FROM offline_booking o 
                   WHERE NOT (o.checkout_date <= '$checkin' OR o.checkin_date >= '$checkout')
               ) 
               LIMIT 1";

$result_assign = mysqli_query($conn, $sql_assign);

if (mysqli_num_rows($result_assign) > 0) {
    $row = mysqli_fetch_assoc($result_assign);
    $assigned_room = $row['room_number'];
    
    // બાકીનો કોડ (Razorpay Verification અને Update)
    // ...
} else {
    // જો કોઈ રૂમ ખાલી ન મળે તો
    $response['error'] = "Last minute conflict: Room already booked offline/online.";
    echo json_encode($response);
    exit;
}

    // $row = mysqli_fetch_assoc($result_check);
    // $assigned_room = $row['room_number'];

    // --- STEP 2: Razorpay સિગ્નેચર વેરિફાય કરો ---
    $api = new Api($keyId, $keySecret);
    $attributes = [
        'razorpay_order_id' => $order_id,
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $signature
    ];

    $api->utility->verifyPaymentSignature($attributes);
    
    // --- STEP 3: રૂમ અસાઇન કરો અને બુકિંગ કન્ફર્મ કરો ---
    mysqli_begin_transaction($conn);

    // રૂમનું સ્ટેટસ બદલો
    // $upd_room = mysqli_query($conn, "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$assigned_room'");
    // સુધારેલી કન્ડિશન:

    $sql_update = "UPDATE bookings SET 
                   status = 'Confirmed', 
                   payment_status = 'Paid', 
                   razorpay_id = '$payment_id', 
                   room_number = '$assigned_room' 
                   WHERE id = $booking_id";
    $upd_book = mysqli_query($conn, $sql_update);

    // ૨. પેમેન્ટ ટેબલમાં ડેટા સ્ટોર કરો (નવી ફંક્શનલિટી)
    $final_amount = $_SESSION['booking']['total_price']; // સેસનમાંથી ટોટલ પ્રાઈસ લીધી
    $current_date = date('Y-m-d');
    
    $sql_payment = "INSERT INTO payments (booking_id, amount, payment_date) 
                    VALUES ('$booking_id', '$final_amount', '$current_date')";
    $ins_payment = mysqli_query($conn, $sql_payment);

// ચેક કરો કે બંને ક્વેરી સફળ રહી કે નહીં
    if ($upd_book && $ins_payment) { 
        mysqli_commit($conn);
        $response['status'] = 'success';
        $response['booking_id'] = $booking_id;
        unset($_SESSION['booking']); 
    } else {
        mysqli_rollback($conn);
        $response['error'] = "Payment verified but database update failed.";
    }

} catch(SignatureVerificationError $e) {
    $response['error'] = "Signature verification failed.";
} catch(\Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
exit;