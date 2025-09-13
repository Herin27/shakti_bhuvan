<?php
session_start();
include 'db.php';  // your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = $_POST['name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $location   = $_POST['location'];
    $guests     = $_POST['guests'];
    $room_id    = $_POST['room_id'];
    $checkin    = $_POST['checkin'];
    $checkout   = $_POST['checkout'];
    $totalPrice = $_POST['total_price'];
    $createdAt  = date("Y-m-d H:i:s");

    // 1️⃣ Insert or Update user in users table
    $checkUser = $conn->prepare("SELECT customer_id FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        // Existing user → update bookings count & total spent
        $user = $result->fetch_assoc();
        $customer_id = $user['customer_id'];

        $updateUser = $conn->prepare("UPDATE users 
                                      SET bookings = bookings + 1, total_spent = total_spent + ? 
                                      WHERE customer_id = ?");
        $updateUser->bind_param("di", $totalPrice, $customer_id);
        $updateUser->execute();

    } else {
        // New user → insert
        $customer_id = "CUST" . rand(1000, 9999);
        $insertUser = $conn->prepare("INSERT INTO users 
            (customer_id, name, email, phone, location, member_since, bookings, total_spent, rating, status) 
            VALUES (?, ?, ?, ?, ?, CURDATE(), 1, ?, NULL, 'ACTIVE')");
        $insertUser->bind_param("sssssd", $customer_id, $name, $email, $phone, $location, $totalPrice);
        $insertUser->execute();
    }

    // 2️⃣ Insert into bookings table
$status = "Pending";            // Initially pending
$payment_status = "Pending";    // Will update after actual payment

$insertBooking = $conn->prepare("INSERT INTO bookings 
    (customer_name, phone, guests, room_id, checkin, checkout, total_price, created_at, status, payment_status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$insertBooking->bind_param("ssiissdsss", $name, $phone, $guests, $room_id, $checkin, $checkout, $totalPrice, $createdAt, $status, $payment_status);

if ($insertBooking->execute()) {
    $booking_id = $conn->insert_id; // Get the booking ID for payment tracking

    // ✅ Immediately update status to Confirmed after successful form submission
    $updateStatus = $conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE id = ?");
    $updateStatus->bind_param("i", $booking_id);
    $updateStatus->execute();
    $updateStatus->close();

    // Redirect to payment and pass booking ID
    header("Location: payment.php?booking_id=" . $booking_id);
    exit();
} else {
    echo "Error: " . $insertBooking->error;
}

$insertBooking->close();

}
?>
