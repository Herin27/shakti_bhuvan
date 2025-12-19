<?php
include 'db.php';

$room_number = mysqli_real_escape_string($conn, $_GET['room_number']);
$checkin = mysqli_real_escape_string($conn, $_GET['checkin']);

mysqli_begin_transaction($conn);

try {
    $sql_off = "INSERT INTO offline_booking (room_number, checkin_date) VALUES ('$room_number', '$checkin')";
    mysqli_query($conn, $sql_off);

    $sql_main = "INSERT INTO bookings (customer_name, room_number, checkin, checkout, status, notes) 
                 VALUES ('Offline Guest', '$room_number', '$checkin', DATE_ADD('$checkin', INTERVAL 1 DAY), 'Confirmed', 'Manual Offline Booking')";
    mysqli_query($conn, $sql_main);

    $sql_update_room = "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$room_number'";
    mysqli_query($conn, $sql_update_room);

    mysqli_commit($conn);
    header("Location: admin_dashboard.php?section=room-dashboard-section&msg=Offline Booking Success");

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
}
?>