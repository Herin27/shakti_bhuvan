<?php
// daily_cleanup.php

// Define the absolute path to your database connection file
// NOTE: Adjust this path if necessary, but keep it in a secure location.
include 'db.php'; 

// Function to run the cleanup process
function runCleanup($conn) {
    
    // Check if the connection is valid
    if (!$conn) {
        // Output error to the server log (recommended for cron jobs)
        error_log("DAILY CLEANUP ERROR: Database connection failed.");
        return false;
    }
    
    $current_date = date('Y-m-d');
    $log = "--- Room Cleanup Started: " . date('Y-m-d H:i:s') . " ---\n";

    // --- 1. Revert Room Status for ALL physical rooms whose latest confirmed booking has ended ---
    
    /* * This query finds physical rooms that are currently 'Occupied' 
     * and sets their status back to 'Available' 
     * IF the latest associated CONFIRMED booking has a checkout date in the past.
     * It also ensures no conflicting FUTURE confirmed/pending bookings exist for that room number.
    */
    
    $sql_revert_room_status = "
        UPDATE room_numbers rn
        SET rn.status = 'Available'
        WHERE rn.status = 'Occupied'
        AND rn.room_number IN (
            -- Subquery: Find all room numbers whose checkout date has passed
            SELECT room_number 
            FROM bookings 
            WHERE checkout < '$current_date' 
            AND status IN ('Confirmed', 'Checked-in')
        )
        AND NOT EXISTS (
            -- Subquery: Ensure there is no *future* booking currently active/pending for this room number
            SELECT 1 FROM bookings b2 
            WHERE b2.room_number = rn.room_number 
            AND b2.checkin >= '$current_date' 
            AND b2.status IN ('Confirmed', 'Pending') 
            LIMIT 1
        );
    ";
    
    if (mysqli_query($conn, $sql_revert_room_status)) {
        $rows_affected = mysqli_affected_rows($conn);
        $log .= "SUCCESS: $rows_affected physical room statuses reverted to 'Available'.\n";
    } else {
        $log .= "ERROR: Room status update failed: " . mysqli_error($conn) . "\n";
    }

    // --- 2. Update Status of Old Bookings in the 'bookings' table ---

    // Mark old bookings (past checkout date) as 'Checked-out'
    $sql_update_bookings = "
        UPDATE bookings
        SET status = 'Checked-out'
        WHERE checkout < '$current_date'
        AND status IN ('Confirmed', 'Checked-in');
    ";
    
    if (mysqli_query($conn, $sql_update_bookings)) {
        $rows_affected = mysqli_affected_rows($conn);
        $log .= "SUCCESS: $rows_affected old booking records marked as 'Checked-out'.\n";
    } else {
        $log .= "ERROR: Booking record update failed: " . mysqli_error($conn) . "\n";
    }

    // --- 3. Optional: Mark Pending/Unpaid Bookings as Cancelled (e.g., after 24 hours) ---

    $sql_cancel_unpaid = "
        UPDATE bookings
        SET status = 'Cancelled', payment_status = 'Pending Exceeded'
        WHERE payment_status = 'Pending' 
        AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);
    ";
    
    if (mysqli_query($conn, $sql_cancel_unpaid)) {
        $rows_affected = mysqli_affected_rows($conn);
        $log .= "SUCCESS: $rows_affected unpaid/pending bookings auto-cancelled.\n";
        
        // If a booking is auto-cancelled, we must also free up the room number immediately
        if ($rows_affected > 0) {
             // Find all room numbers from the cancelled bookings and set them back to 'Available'
             $sql_free_rooms = "
                 UPDATE room_numbers rn
                 JOIN bookings b ON rn.room_number = b.room_number
                 SET rn.status = 'Available'
                 WHERE b.payment_status = 'Pending Exceeded' 
                 AND rn.status = 'Occupied';
             ";
             mysqli_query($conn, $sql_free_rooms);
             $log .= "SUCCESS: Physical rooms associated with auto-cancelled bookings were freed.\n";
        }

    } else {
        $log .= "ERROR: Unpaid booking cancellation failed: " . mysqli_error($conn) . "\n";
    }


    $log .= "--- Room Cleanup Finished. ---\n";
    
    // Output log 
    echo nl2br($log); 
    
    return true;
}

// Execute the cleanup function
runCleanup($conn);
?>