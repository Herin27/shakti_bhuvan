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

    // ૧. તારીખની વેલિડિટી ચેક કરો (Check-out તારીખ Check-in થી વધારે હોવી જોઈએ)
    if (strtotime($checkout_date) <= strtotime($checkin_date)) {
        die("<script>alert('Error: Check-out date must be after Check-in date!'); window.history.back();</script>");
    }

    // ૨. ડબલ બુકિંગ ચેક કરો (Online અને Offline બંને ટેબલમાં)
    // એવી બુકિંગ શોધો જે નવી તારીખ સાથે ઓવરલેપ થતી હોય
    $check_overlap_sql = "
        SELECT room_number FROM (
            SELECT room_number, checkin as cin, checkout as cout FROM bookings WHERE status IN ('Confirmed', 'Checked-in')
            UNION
            SELECT room_number, checkin_date as cin, checkout_date as cout FROM offline_booking
        ) as all_bookings
        WHERE room_number = '$room_number'
        AND NOT (cout <= '$checkin_date' OR cin >= '$checkout_date')
    ";

    $overlap_result = mysqli_query($conn, $check_overlap_sql);

    if (mysqli_num_rows($overlap_result) > 0) {
        // જો ઓવરલેપ મળે તો એરર બતાવી પાછા મોકલો
        echo "<script>
                alert('Error: Room $room_number is already booked for the selected dates! Please check the Room Dashboard.');
                window.location.href = 'admin_dashboard.php?section=room-dashboard-section';
              </script>";
        exit();
    }

    // ૩. જો કોઈ ઓવરલેપ નથી, તો ટ્રાન્ઝેક્શન શરૂ કરો
    mysqli_begin_transaction($conn);

    try {
        // ઇન્સર્ટ ઓફલાઇન બુકિંગ
        $sql_offline = "INSERT INTO offline_booking (room_number, customer_name, phone, checkin_date, checkout_date, payment_status, created_at) 
                        VALUES ('$room_number', '$customer_name', '$phone', '$checkin_date', '$checkout_date', '$payment_status', '$created_at')";
        $res1 = mysqli_query($conn, $sql_offline);

        // રૂમ સ્ટેટસ અપડેટ (જો આજની તારીખમાં ચેક-ઇન હોય તો જ 'Occupied' કરવું વધુ સારું છે)
        $today = date('Y-m-d');
        if($checkin_date <= $today) {
            $sql_update_room = "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$room_number'";
            mysqli_query($conn, $sql_update_room);
        }

        if ($res1) {
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