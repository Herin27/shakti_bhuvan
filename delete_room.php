<?php
// delete_room.php

// 1. Include the database connection file
include 'db.php'; 

// Check for a valid database connection immediately
if (!isset($conn) || $conn->connect_error) {
    $message = "Database connection failed. Check your db.php file.";
    $alert_type = 'error';
    $redirect_url = 'admin_dashboard.php?section=manage-rooms-section';
    header("Location: $redirect_url&alert_type=$alert_type&msg=" . urlencode($message));
    exit();
}


// Function to safely sanitize input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, $data); 
}

$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$record_type = isset($_GET['type']) ? sanitize_input($conn, $_GET['type']) : '';

$message = '';
$redirect_url = 'admin_dashboard.php?section=manage-rooms-section';
$transaction_successful = false;
$alert_type = 'error'; 

// 2. Basic Validation Check
if ($room_id <= 0 || $record_type !== 'Room') {
    $message = "Invalid request or insufficient parameters for deletion. (ID: $room_id, Type: $record_type)";
    $alert_type = 'danger';
    header("Location: $redirect_url&alert_type=$alert_type&msg=" . urlencode($message));
    exit();
}

// --- Start Deletion Process ---

// Use a transaction for safety
mysqli_begin_transaction($conn);

try {
    // 3. Fetch image paths associated with the room
    $sql_fetch_images = "SELECT image FROM rooms WHERE id = $room_id";
    $result_images = mysqli_query($conn, $sql_fetch_images);

    if ($result_images && $row = mysqli_fetch_assoc($result_images)) {
        $image_string = $row['image'];
        
        // 4. Delete physical files from the server
        if (!empty($image_string)) {
            $image_paths = array_filter(explode(',', $image_string));
            foreach ($image_paths as $path) {
                $path = trim($path);
                // Assuming your image files are stored in 'uploads/' directory
                $full_path = 'uploads/' . $path; 
                
                if (file_exists($full_path) && !is_dir($full_path)) {
                    @unlink($full_path); 
                }
            }
        }
    }

    // --- DEPENDENCY DELETION ORDER (Crucial Fix) ---

    // A. Collect IDs of bookings tied to this room
    $booking_ids_to_delete = [];
    $sql_fetch_booking_ids = "SELECT id FROM bookings WHERE room_id = $room_id";
    $result_booking_ids = mysqli_query($conn, $sql_fetch_booking_ids);
    
    if ($result_booking_ids) {
        while ($row = mysqli_fetch_assoc($result_booking_ids)) {
            $booking_ids_to_delete[] = $row['id'];
        }
    }
    
    // B. Delete dependent records from the 'payments' table FIRST
    $payments_deleted = 0;
    if (!empty($booking_ids_to_delete)) {
        $booking_id_list = implode(",", $booking_ids_to_delete);
        
        // Prepare string list for 'BKXXXX' format
        $booking_bk_list = [];
        foreach ($booking_ids_to_delete as $id) {
            $booking_bk_list[] = "'" . 'BK' . str_pad($id, 4, '0', STR_PAD_LEFT) . "'";
        }
        $booking_bk_list_str = implode(",", $booking_bk_list);

        // FIX: Delete based on BOTH possible formats: numerical ID AND formatted string ID ('BKxxxx')
        $sql_delete_payments = "
            DELETE FROM payments 
            WHERE booking_id IN ($booking_id_list) 
            OR booking_id IN ($booking_bk_list_str);
        ";
        
        if (!mysqli_query($conn, $sql_delete_payments)) {
             throw new Exception("Error deleting dependent payments: " . mysqli_error($conn));
        }
        $payments_deleted = mysqli_affected_rows($conn);
    }
    
    // C. Delete all dependent records in the 'bookings' table
    $sql_delete_bookings = "DELETE FROM bookings WHERE room_id = $room_id";
    if (!mysqli_query($conn, $sql_delete_bookings)) {
        throw new Exception("Error deleting dependent bookings: " . mysqli_error($conn));
    }
    $bookings_deleted = mysqli_affected_rows($conn);

    // D. Delete all physical room numbers associated with this room type
    // (This is redundant due to ON DELETE CASCADE on room_numbers.fk_room_type but safe)
    $sql_delete_rn = "DELETE FROM room_numbers WHERE room_type_id = $room_id";
    if (!mysqli_query($conn, $sql_delete_rn)) {
        throw new Exception("Error deleting associated physical room numbers: " . mysqli_error($conn));
    }
    $rn_deleted = mysqli_affected_rows($conn);


    // E. Delete the room record from the 'rooms' table
    $sql_delete_room = "DELETE FROM rooms WHERE id = $room_id";
    if (!mysqli_query($conn, $sql_delete_room)) {
        throw new Exception("Error deleting room record: " . mysqli_error($conn));
    }

    // If we reach here, commit the transaction.
    mysqli_commit($conn);
    $transaction_successful = true;
    $alert_type = 'success';
    $message = "Room Type ID $room_id deleted successfully. Removed $rn_deleted physical rooms, $bookings_deleted bookings, and $payments_deleted payment records.";
    
} catch (Exception $e) {
    // Rollback the transaction on failure
    mysqli_rollback($conn);
    $message = "Deletion failed (Rollback): " . $e->getMessage();
    $alert_type = 'danger';
}

mysqli_close($conn);

// 8. Redirect with the result message
header("Location: $redirect_url&alert_type=$alert_type&msg=" . urlencode($message));
exit();
?>