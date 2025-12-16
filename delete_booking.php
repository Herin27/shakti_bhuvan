<?php
// delete_booking.php

include 'db.php'; 

// Check for a valid database connection immediately
if (!isset($conn) || $conn->connect_error) {
    $message = "Database connection failed.";
    $redirect_url = 'admin_dashboard.php?section=bookings-section';
    header("Location: $redirect_url&alert_type=error&msg=" . urlencode($message));
    exit();
}

// Function to safely sanitize input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, $data); 
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Simplified Validation: Only check if the ID is valid.
if ($booking_id <= 0) {
    $message = "Invalid booking ID or insufficient parameters for deletion.";
    $redirect_url = 'admin_dashboard.php?section=bookings-section';
    header("Location: $redirect_url&alert_type=danger&msg=" . urlencode($message));
    exit();
}

$redirect_url = 'admin_dashboard.php?section=bookings-section';
$transaction_successful = false;
$alert_type = 'error'; 

// --- Start Deletion Transaction ---
mysqli_begin_transaction($conn);

try {
    // 2. Fetch required details (Room Number) BEFORE deletion
    $sql_fetch_details = "SELECT room_number FROM bookings WHERE id = $booking_id";
    $result_details = mysqli_query($conn, $sql_fetch_details);
    $booking_details = mysqli_fetch_assoc($result_details);
    $physical_room_number = $booking_details['room_number'] ?? null;
    $booking_id_display = 'BK' . str_pad($booking_id, 4, '0', STR_PAD_LEFT);
    
    // --- DEPENDENCY DELETION ORDER ---

    // A. Delete dependent records from the 'payments' table (Must be first)
    // FIX: Delete based on BOTH possible formats: numerical ID AND formatted string ID ('BKxxxx')
    $sql_delete_payments = "
        DELETE FROM payments 
        WHERE booking_id = '$booking_id' OR booking_id = '$booking_id_display'
    ";
    if (!mysqli_query($conn, $sql_delete_payments)) {
        throw new Exception("Error deleting dependent payment records: " . mysqli_error($conn));
    }
    $payments_deleted = mysqli_affected_rows($conn);


    // B. Delete the booking record from the 'bookings' table
    $sql_delete_booking = "DELETE FROM bookings WHERE id = $booking_id";
    if (!mysqli_query($conn, $sql_delete_booking)) {
        throw new Exception("Error deleting booking record: " . mysqli_error($conn));
    }
    $bookings_deleted = mysqli_affected_rows($conn);
    
    if ($bookings_deleted === 0) {
         throw new Exception("Booking ID $booking_id_display not found in the database. Deletion failed.");
    }
    
    // C. Reset the physical room status back to 'Available'
    if ($physical_room_number) {
        // Only reset if it was occupied by this booking (status check ensures maintenance stays maintenance)
        $sql_reset_room = "UPDATE room_numbers SET status = 'Available' WHERE room_number = '$physical_room_number' AND status != 'Maintenance'";
        mysqli_query($conn, $sql_reset_room);
    }
    
    // Commit the transaction.
    mysqli_commit($conn);
    $transaction_successful = true;
    $alert_type = 'success';
    $message = "Booking $booking_id_display deleted successfully. Removed $payments_deleted payment record(s) and reset Room #$physical_room_number status.";
    
} catch (Exception $e) {
    // Rollback the transaction on failure
    mysqli_rollback($conn);
    $message = "Deletion failed (Rollback): " . $e->getMessage();
    $alert_type = 'danger';
}

mysqli_close($conn);

// 4. Redirect with the result message
header("Location: $redirect_url&alert_type=$alert_type&msg=" . urlencode($message));
exit();
?>