<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Pehla room numbers delete karva padshe (Foreign key issue na ave etle)
    mysqli_query($conn, "DELETE FROM room_numbers WHERE room_type_id = '$id'");

    // Have main room type delete karo
    $sql = "DELETE FROM rooms WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Room Type Deleted Successfully'); window.location.href='admin_dashboard.php?section=manage-rooms-section';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>