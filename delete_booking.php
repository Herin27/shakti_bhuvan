<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // ૧. પહેલા ચેક કરો કે આ બુકિંગ અસ્તિત્વમાં છે કે નહીં
    $check_sql = "SELECT room_number FROM bookings WHERE id = '$id'";
    $result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $room_no = $row['room_number'];

        // ૨. બુકિંગ ડિલીટ કરો
        $delete_sql = "DELETE FROM bookings WHERE id = '$id'";
        
        if (mysqli_query($conn, $delete_sql)) {
            // ૩. જો જરૂર હોય તો રૂમનું સ્ટેટસ ફરી 'Available' કરી શકાય
            if ($room_no) {
                mysqli_query($conn, "UPDATE room_numbers SET status = 'Available' WHERE room_number = '$room_no'");
            }
            header("Location: admin_dashboard.php?section=bookings-section&msg=deleted");
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Booking not found.";
    }
}

mysqli_close($conn);
?>