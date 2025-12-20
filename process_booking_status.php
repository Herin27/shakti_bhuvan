<?php
include 'db.php';

if (isset($_GET['booking_id']) && $_GET['action'] == 'checkout') {
    $booking_id = intval($_GET['booking_id']);

    // ૧. રૂમ નંબર મેળવો
    $res = mysqli_query($conn, "SELECT room_number FROM bookings WHERE id = $booking_id");
    $row = mysqli_fetch_assoc($res);
    $rm_no = $row['room_number'];

    // ૨. બુકિંગ ટેબલમાં સ્ટેટસ અપડેટ
    mysqli_query($conn, "UPDATE bookings SET status = 'Checked-out' WHERE id = $booking_id");
    
    // ૩. રૂમ નંબર્સ ટેબલમાં સ્ટેટસ ડાયરેક્ટ 'Available' કરો
    if (!empty($rm_no)) {
        mysqli_query($conn, "UPDATE room_numbers SET status = 'Available' WHERE room_number = '$rm_no'");
    }

    header("Location: admin_dashboard.php?section=bookings-section&msg=checkout_success");
    exit;
}
?>