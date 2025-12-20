<?php
include 'db.php';

if (isset($_POST['submit_offline'])) {
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    $checkin_date = mysqli_real_escape_string($conn, $_POST['checkin_date']);
    $checkout_date = mysqli_real_escape_string($conn, $_POST['checkout_date']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $created_at = date('Y-m-d H:i:s');

    mysqli_begin_transaction($conn);

    try {
        // ૧. બધી વિગતો સાથે ઇન્સર્ટ કરો
        $sql_offline = "INSERT INTO offline_booking (room_number, customer_name, phone, checkin_date, checkout_date, payment_status, created_at) 
                        VALUES ('$room_number', '$customer_name', '$phone', '$checkin_date', '$checkout_date', '$payment_status', '$created_at')";
        $res1 = mysqli_query($conn, $sql_offline);

        // ૨. રૂમ સ્ટેટસ અપડેટ
        $sql_update_room = "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$room_number'";
        $res2 = mysqli_query($conn, $sql_update_room);

        if ($res1 && $res2) {
            mysqli_commit($conn);
            header("Location: admin_dashboard.php?section=offline-bookings-section&msg=booked");
        } else {
            throw new Exception("ડેટા સેવ કરવામાં ભૂલ છે.");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: admin_dashboard.php");
}
mysqli_close($conn);
?>