<?php
// Include the database connection file
include 'db.php'; 

// Function to safely sanitize input
function sanitize_input($conn, $data) {
    // Note: Since we only expect an integer ID, intval() is safer, but keeping this for string inputs.
    return mysqli_real_escape_string($conn, $data); 
}

$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$record_type = isset($_GET['type']) ? sanitize_input($conn, $_GET['type']) : '';

// 1. Basic Validation
if ($room_id <= 0 || $record_type !== 'Room') {
    die("Invalid request or insufficient parameters for deletion.");
}

$message = '';
$redirect_url = 'admin_dashboard.php?section=manage-rooms-section';

// --- Start Deletion Process ---

// Use a transaction for safety: if one part fails, everything rolls back
mysqli_begin_transaction($conn);
$transaction_successful = true;

try {
    // 2. Fetch image paths associated with the room (MUST happen before deleting the room record)
    $sql_fetch_images = "SELECT image FROM rooms WHERE id = $room_id";
    $result_images = mysqli_query($conn, $sql_fetch_images);

    if ($result_images && $row = mysqli_fetch_assoc($result_images)) {
        $image_string = $row['image'];
        
        // 3. Delete physical files from the server (Keep this step before DB deletion)
        if (!empty($image_string)) {
            $image_paths = array_filter(explode(',', $image_string));
            foreach ($image_paths as $path) {
                $path = trim($path);
                if (file_exists($path) && !is_dir($path)) {
                    // Suppress errors if unlink fails, logging is better for production
                    @unlink($path); 
                }
            }
        }
    }

    // 4. CASCADE FIX: Delete all dependent records in the 'bookings' table first
    $sql_delete_bookings = "DELETE FROM bookings WHERE room_id = $room_id";
    if (!mysqli_query($conn, $sql_delete_bookings)) {
        throw new Exception("Error deleting dependent bookings: " . mysqli_error($conn));
    }
    $bookings_deleted = mysqli_affected_rows($conn);

    // 5. Delete the room record from the 'rooms' table
    $sql_delete_room = "DELETE FROM rooms WHERE id = $room_id";
    if (!mysqli_query($conn, $sql_delete_room)) {
        throw new Exception("Error deleting room record: " . mysqli_error($conn));
    }

    // If we reach here, both deletions were successful. Commit the transaction.
    mysqli_commit($conn);
    $message = "Room ID $room_id deleted successfully. $bookings_deleted related booking record(s) were also removed.";
    
} catch (Exception $e) {
    // If any error occurred, rollback the transaction
    mysqli_rollback($conn);
    $transaction_successful = false;
    $message = "Deletion failed: " . $e->getMessage();
}

mysqli_close($conn);

// 6. Redirect with the result message
if ($transaction_successful) {
    header("Location: $redirect_url&status=success&msg=" . urlencode($message));
} else {
    header("Location: $redirect_url&status=error&msg=" . urlencode($message));
}
exit();
?>