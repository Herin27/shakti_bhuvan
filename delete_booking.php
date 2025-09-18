<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']); // sanitize

    // Change 'id' to match your actual column name in bookings table
    $sql = "DELETE FROM bookings WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ./admin_deshboard.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
