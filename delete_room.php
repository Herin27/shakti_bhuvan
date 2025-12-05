<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];

    // First delete bookings for this room
    $stmt1 = $conn->prepare("DELETE FROM bookings WHERE room_id = ?");
    $stmt1->bind_param("i", $room_id);
    $stmt1->execute();

    // Now delete the room
    $stmt2 = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt2->bind_param("i", $room_id);

    if ($stmt2->execute()) {
        header("Location: admin_deshboard.php");
        exit;
    } else {
        echo "Error deleting room: " . $conn->error;
    }
}
?>
