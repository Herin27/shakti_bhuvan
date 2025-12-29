<?php
include 'db.php';

if (isset($_POST['update_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $room_id = intval($_POST['room_id']);
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];

    $update_sql = "UPDATE bookings SET 
                    customer_name = '$customer_name',
                    phone = '$phone',
                    email = '$email',
                    room_id = '$room_id',
                    room_number = '$room_number',
                    checkin = '$checkin',
                    checkout = '$checkout',
                    total_price = '$total_price',
                    status = '$status',
                    payment_status = '$payment_status'
                   WHERE id = $booking_id";

    if (mysqli_query($conn, $update_sql)) {
        // સફળતાપૂર્વક અપડેટ થયા પછી પાછા View Details પેજ પર મોકલો
        header("Location: admin_dashboard.php?id=$booking_id&msg=updated");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>