<?php
include 'db.php';

if (isset($_GET['id'])) {
    $room_id = intval($_GET['id']);

    // ટ્રાન્ઝેક્શન શરૂ કરો જેથી જો કોઈ એક ક્વેરી ફેલ થાય તો ડેટા સુરક્ષિત રહે
    mysqli_begin_transaction($conn);

    try {
        // ૧. પેલા આ રૂમ ટાઈપ સાથે જોડાયેલા રૂમ નંબર્સ ડિલીટ કરો (Foreign Key constraints ને કારણે)
        mysqli_query($conn, "DELETE FROM room_numbers WHERE room_type_id = $room_id");

        // ૨. મુખ્ય રૂમ ટાઈપ ડિલીટ કરો
        $sql_delete = "DELETE FROM rooms WHERE id = $room_id";
        
        if (mysqli_query($conn, $sql_delete)) {
            mysqli_commit($conn);
            echo "<script>
                    alert('room is successfully deleted');
                    window.location.href = 'admin_dashboard.php?section=manage-rooms-section';
                  </script>";
        } else {
            throw new Exception("Error deleting record");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>
                alert('Error: This room type cannot be deleted. It may have active bookings.');
                window.location.href = 'admin_dashboard.php?section=manage-rooms-section';
              </script>";
    }
}

mysqli_close($conn);
?>