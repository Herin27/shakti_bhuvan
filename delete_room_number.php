<?php
include 'db.php';

$rn_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($rn_id > 0) {
    // Before deleting, get the room number for the alert message
    $sql_name = "SELECT room_number FROM room_numbers WHERE id = $rn_id";
    $result_name = mysqli_query($conn, $sql_name);
    $room_number_data = mysqli_fetch_assoc($result_name);
    $room_number = htmlspecialchars($room_number_data['room_number'] ?? 'N/A');

    // SQL to delete the physical room number
    $sql_delete = "DELETE FROM room_numbers WHERE id = $rn_id";
    
    if (mysqli_query($conn, $sql_delete)) {
        $message = "Physical Room #$room_number (ID: $rn_id) has been permanently deleted.";
        $alert_type = 'success';
    } else {
        $message = "Error deleting physical room: " . mysqli_error($conn);
        $alert_type = 'danger';
    }
} else {
    $message = "Error: Invalid Room Number ID provided for deletion.";
    $alert_type = 'warning';
}

mysqli_close($conn);

// Redirect back to the admin panel with a message
header("Location: admin_dashboard.php?section=manage-room-numbers-section&alert_type=$alert_type&message=" . urlencode($message));
exit;
?>