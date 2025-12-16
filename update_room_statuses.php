<?php
// update_room_statuses.php
include 'db.php'; // Include your database connection

// Log file for tracking updates (optional, but highly recommended)
$log_file = 'room_status_log.txt';

function write_log($message) {
    global $log_file;
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}

write_log("--- Starting Room Status Update ---");

// --- 1. Find Bookings that have been "Checked-out" and clear the associated room_number ---

// This query identifies physical rooms whose associated booking is marked 'Checked-out' 
// AND whose checkout date is in the past (or today, if the script runs late).
// We update the room_numbers table status to 'Available'.

$sql_update_rooms = "
    UPDATE room_numbers rn
    INNER JOIN bookings b ON rn.room_number = b.room_number
    SET rn.status = 'Available'
    WHERE b.checkout <= CURDATE() 
      AND b.status = 'Checked-out'
      AND rn.status = 'Occupied';
";

if (mysqli_query($conn, $sql_update_rooms)) {
    $rows_updated = mysqli_affected_rows($conn);
    write_log("SUCCESS: Cleared status for $rows_updated physical rooms (Bookings status: Checked-out).");
} else {
    write_log("ERROR: Failed to update rooms based on Checked-out bookings: " . mysqli_error($conn));
}


// --- 2. Find Bookings that are overdue (checkout passed) but still marked 'Confirmed' or 'Checked-in' ---

// This query changes the *Booking* status to 'Checked-out - Overdue' for tracking.
// Note: We DO NOT change the room_numbers status here, as the guest might still be occupying the room.
// Manual admin review is required for these cases.

$sql_update_overdue_bookings = "
    UPDATE bookings 
    SET status = 'Checked-out - Overdue'
    WHERE checkout < CURDATE()
      AND status IN ('Confirmed', 'Checked-in');
";

if (mysqli_query($conn, $sql_update_overdue_bookings)) {
    $rows_overdue = mysqli_affected_rows($conn);
    write_log("NOTICE: Marked $rows_overdue bookings as 'Checked-out - Overdue'. Admin action needed.");
} else {
    write_log("ERROR: Failed to mark overdue bookings: " . mysqli_error($conn));
}


write_log("--- Room Status Update Complete ---");

// Display log result only if run manually in a browser
echo "<h1>Room Status Update Script Run</h1>";
echo "<p>Completed at: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>See room_status_log.txt for detailed results.</p>";

// Close connection (optional, as PHP does this automatically)
mysqli_close($conn);

?>