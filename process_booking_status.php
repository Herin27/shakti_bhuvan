<?php
include 'db.php';

if (isset($_GET['booking_id']) && isset($_GET['action'])) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);
    $action = $_GET['action'];

    // 1. Get room details from the booking
    $sql_get_room = "SELECT room_id, room_number FROM bookings WHERE id = '$booking_id'";
    $res = mysqli_query($conn, $sql_get_room);
    $booking = mysqli_fetch_assoc($res);

    if ($booking) {
        $room_type_id = $booking['room_id'];
        $room_number = $booking['room_number'];

        if ($action == 'checkout') {
            $new_status = 'Cleaning';
            $booking_status = 'Checked-out';
        } else {
            $new_status = 'Available';
            $booking_status = 'Confirmed'; // Keep confirmed or change as per logic
        }

        // 2. Update Physical Room Number Status
        mysqli_query($conn, "UPDATE room_numbers SET status = '$new_status' WHERE room_number = '$room_number' AND room_type_id = '$room_type_id'");

        // 3. Update Room Type Status (The general category)
        mysqli_query($conn, "UPDATE rooms SET status = '$new_status' WHERE id = '$room_type_id'");

        // 4. Update Booking Status
        if ($action == 'checkout') {
            mysqli_query($conn, "UPDATE bookings SET status = '$booking_status' WHERE id = '$booking_id'");
        }

        header("Location: admin_dashboard.php?section=bookings-section&msg=Status Updated");
    }
}
?>