<?php
include 'db.php';

if (isset($_GET['id']) && isset($_GET['room'])) {
    $offline_id = intval($_GET['id']);
    $room_number = mysqli_real_escape_string($conn, $_GET['room']);

    // --- STEP 1: ટ્રાન્ઝેક્શન શરૂ કરો જેથી બંને અપડેટ સાથે થાય ---
    mysqli_begin_transaction($conn);

    try {
        // ૧. ઓફલાઇન બુકિંગ રેકોર્ડને ડિલીટ કરો (અથવા સ્ટેટસ 'Checked-out' કરો)
        // જો તમારે રેકોર્ડ રાખવો હોય તો UPDATE વાપરો, નહીંતર DELETE
        $sql_delete = "DELETE FROM offline_booking WHERE id = $offline_id";
        $res1 = mysqli_query($conn, $sql_delete);

        // ૨. room_numbers ટેબલમાં તે રૂમનું સ્ટેટસ 'Available' કરો
        $sql_update_room = "UPDATE room_numbers SET status = 'Available' WHERE room_number = '$room_number'";
        $res2 = mysqli_query($conn, $sql_update_room);

        if ($res1 && $res2) {
            mysqli_commit($conn);
            // સફળ થયા પછી મેસેજ સાથે પાછા મોકલો
            header("Location: admin_dashboard.php?section=offline-bookings-section&msg=success");
        } else {
            throw new Exception("Query failed");
        }

    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: admin_dashboard.php?section=offline-bookings-section&msg=error");
    }
} else {
    header("Location: admin_dashboard.php");
}

mysqli_close($conn);
exit;
?>