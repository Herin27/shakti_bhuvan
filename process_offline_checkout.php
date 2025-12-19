<?php
include 'db.php';

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$room_number = mysqli_real_escape_string($conn, $_GET['room']);

if ($booking_id > 0 && !empty($room_number)) {
    
    mysqli_begin_transaction($conn);

    try {
        $sql_update_room = "UPDATE room_numbers SET status = 'Available' WHERE room_number = '$room_number'";
        mysqli_query($conn, $sql_update_room);

        $sql_delete_off = "DELETE FROM offline_booking WHERE id = $booking_id";
        mysqli_query($conn, $sql_delete_off);

        $sql_update_main = "UPDATE bookings SET status = 'Checked-out' 
                           WHERE room_number = '$room_number' AND customer_name = 'Offline Guest' 
                           AND status = 'Confirmed' LIMIT 1";
        mysqli_query($conn, $sql_update_main);

        mysqli_commit($conn);
        header("Location: admin_dashboard.php?section=offline-bookings-section&msg=Room is now Available");

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: admin_dashboard.php?section=offline-bookings-section");
}
?>