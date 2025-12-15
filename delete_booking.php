<?php
// Include the database connection file
include 'db.php'; 

// Function to safely sanitize input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, $data); 
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$record_type = isset($_GET['type']) ? sanitize_input($conn, $_GET['type']) : '';

// 1. Basic Validation
if ($booking_id <= 0 || $record_type !== 'Booking') {
    $message = "Invalid request or insufficient parameters for deletion.";
    $redirect_url = 'admin_dashboard.php?section=bookings-section';
    header("Location: $redirect_url&status=error&msg=" . urlencode($message));
    exit();
}

$redirect_url = 'admin_dashboard.php?section=bookings-section';
$message = '';

// --- Start Deletion Process ---

// 2. Delete the booking record from the 'bookings' table ONLY.
// This is safe because the bookings table does not have child tables, and we are not affecting the 'rooms' table.
$sql_delete_booking = "DELETE FROM bookings WHERE id = $booking_id";

if (mysqli_query($conn, $sql_delete_booking)) {
    // Check if any rows were actually affected
    if (mysqli_affected_rows($conn) > 0) {
        $message = "Booking ID BK" . str_pad($booking_id, 4, '0', STR_PAD_LEFT) . " deleted successfully.";
        
        // --- NOTE: If you need to update the room status (e.g., from Occupied to Available)
        // when a booking is deleted, you would add that logic here.
        
        // Redirect back to the admin panel Bookings section with a success message
        header("Location: $redirect_url&status=success&msg=" . urlencode($message));
        exit();
    } else {
        $message = "Booking ID $booking_id not found in the database.";
        header("Location: $redirect_url&status=warning&msg=" . urlencode($message));
        exit();
    }
} else {
    $message = "ERROR: Could not delete booking: " . mysqli_error($conn);
    header("Location: $redirect_url&status=error&msg=" . urlencode($message));
    exit();
}

mysqli_close($conn);
?>