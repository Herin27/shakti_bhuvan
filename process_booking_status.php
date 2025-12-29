<?php
include 'db.php';

if (isset($_GET['booking_id']) && isset($_GET['action'])) {
    $booking_id = intval($_GET['booking_id']);
    $action = $_GET['action'];

    // ૧. રૂમ નંબર મેળવો
    $res = mysqli_query($conn, "SELECT room_number FROM bookings WHERE id = $booking_id");
    $row = mysqli_fetch_assoc($res);
    $rm_no = $row['room_number'];

    if ($action == 'checkout') {
        // --- માત્ર બુકિંગ સ્ટેટસ બદલાશે, રૂમ હજુ 'Occupied' જ રહેશે અથવા સ્ટેટસ બદલાશે નહીં ---
        mysqli_query($conn, "UPDATE bookings SET status = 'Checked-out' WHERE id = $booking_id");
        
        // જો તમે ઈચ્છો કે રૂમ નંબર્સ ટેબલમાં પણ સ્ટેટસ 'Cleaning' કે એવું કંઈક દેખાય:
        if (!empty($rm_no)) {
            mysqli_query($conn, "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$rm_no'");
        }
        
        $msg = "checkout_done";
    } 
    elseif ($action == 'available') {
        // --- જ્યારે Available પર ક્લિક કરો ત્યારે જ રૂમ ખરેખર ખાલી થશે ---
        
        // ૧. ચેક કરો કે બુકિંગ ખરેખર Checked-out છે કે નહીં (Security Check)
        $status_check = mysqli_query($conn, "SELECT status FROM bookings WHERE id = $booking_id");
        $status_row = mysqli_fetch_assoc($status_check);
        
        if ($status_row['status'] == 'Checked-out') {
            if (!empty($rm_no)) {
                // રૂમ નંબર્સ ટેબલમાં સ્ટેટસ 'Available' કરો
                mysqli_query($conn, "UPDATE room_numbers SET status = 'Available' WHERE room_number = '$rm_no'");
            }
            $msg = "room_freed";
        } else {
            $msg = "error_not_checked_out";
        }
    }

    header("Location: admin_dashboard.php?section=bookings-section&msg=$msg");
    exit;
}
?>