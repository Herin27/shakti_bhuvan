<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $status  = $_POST['status'];

    $stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $room_id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Status+Updated");
        exit;
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
?>
