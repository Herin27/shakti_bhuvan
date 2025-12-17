<?php
// process_booking_status.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['booking_id']) || !isset($_POST['action'])) {
    $_SESSION['error_message'] = "Error: Invalid request for status change.";
    header('Location: admin.php?section=bookings-section');
    exit;
}

$booking_id = intval($_POST['booking_id']);
$action = $_POST['action']; // 'checkout' or 'make_available'

if ($booking_id <= 0) {
    $_SESSION['error_message'] = "Error: Invalid Booking ID.";
    header('Location: admin.php?section=bookings-section');
    exit;
}

// --- 1. Fetch current booking data (specifically room_number) ---
$sql_booking = "SELECT room_number, status FROM bookings WHERE id = $booking_id";
$result_booking = mysqli_query($conn, $sql_booking);
$booking_data = mysqli_fetch_assoc($result_booking);

if (!$booking_data) {
    $_SESSION['error_message'] = "Error: Booking #{$booking_id} not found.";
    header('Location: admin.php?section=bookings-section');
    exit;
}

$physical_room_number = mysqli_real_escape_string($conn, $booking_data['room_number']);
$room_number_status = '';
$booking_new_status = '';
$message = '';

if ($action === 'checkout') {
    // Action 1: CHECK OUT
    // Booking Status: Checked-out (maintains history)
    // Room Status: Maintenance (for cleaning)
    $booking_new_status = 'Checked-out';
    $room_number_status = 'Maintenance';
    $message = "Booking #{$booking_id} has been checked out. Room {$physical_room_number} is now marked for cleaning.";

} elseif ($action === 'make_available') {
    // Action 2: MAKE AVAILABLE
    // Booking Status: Remains the same (e.g., 'Checked-out' or 'Cancelled')
    // Room Status: Available (ready for new booking)
    $booking_new_status = $booking_data['status']; // Keep current booking status
    $room_number_status = 'Available';
    $message = "Room {$physical_room_number} has been manually released and is now Available.";
    
    // Optional: Only allow "Make Available" if the booking is already Checked-out/Cancelled
    if ($booking_new_status !== 'Checked-out' && $booking_new_status !== 'Cancelled') {
        $_SESSION['warning_message'] = "Room {$physical_room_number} was manually released, but booking status remains '{$booking_new_status}'.";
    }

} else {
    $_SESSION['error_message'] = "Error: Unknown action specified.";
    header('Location: admin.php?section=bookings-section');
    exit;
}

// --- 2. Update Booking Status (if different from current, or required by action) ---
$sql_update_booking = "UPDATE bookings SET status = '$booking_new_status' WHERE id = $booking_id AND status != '$booking_new_status'";
// Only execute if action is 'checkout', as 'make_available' typically doesn't change booking status
if ($action === 'checkout') {
    mysqli_query($conn, $sql_update_booking);
}


// --- 3. Update Physical Room Status ---
$sql_update_room_status = "UPDATE room_numbers 
                           SET status = '$room_number_status' 
                           WHERE room_number = '$physical_room_number'";

if (mysqli_query($conn, $sql_update_room_status)) {
    $_SESSION['success_message'] = $message;
} else {
    $_SESSION['error_message'] = "Critical Error: Failed to update room status. Please check Room #{$physical_room_number} manually. " . mysqli_error($conn);
}

mysqli_close($conn);

header('Location: admin_dashboard.php?section=bookings-section');
exit;
?>