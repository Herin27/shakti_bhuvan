<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Room+Deleted");
        exit;
    } else {
        echo "Error deleting room: " . $conn->error;
    }
}
?>