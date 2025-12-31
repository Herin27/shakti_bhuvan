<?php
include 'db.php';

if (isset($_POST['submit_offline'])) {
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    // નવો ઉમેરાયેલ વેરીએબલ
    $room_type_id = mysqli_real_escape_string($conn, $_POST['room_type_id']); 
    
    $checkin_date = mysqli_real_escape_string($conn, $_POST['checkin_date']);
    $checkout_date = mysqli_real_escape_string($conn, $_POST['checkout_date']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $created_at = date('Y-m-d H:i:s');

    if (strtotime($checkout_date) <= strtotime($checkin_date)) {
        die("<script>alert('Error: Check-out date must be after Check-in date!'); window.history.back();</script>");
    }

    // ૨. સુધારેલ ડબલ બુકિંગ ચેક - હવે room_type_id સાથે ચેક કરશે
    $check_overlap_sql = "
        SELECT room_number FROM (
            SELECT room_number, room_id as type_id, checkin as cin, checkout as cout FROM bookings WHERE status IN ('Confirmed', 'Checked-in')
            UNION
            SELECT ob.room_number, rn.room_type_id as type_id, ob.checkin_date as cin, ob.checkout_date as cout 
            FROM offline_booking ob
            JOIN room_numbers rn ON ob.room_number = rn.room_number
        ) as all_bookings
        WHERE room_number = '$room_number' 
        AND type_id = '$room_type_id'
        AND NOT (cout <= '$checkin_date' OR cin >= '$checkout_date')
    ";

    $overlap_result = mysqli_query($conn, $check_overlap_sql);

    if (mysqli_num_rows($overlap_result) > 0) {
        echo "<script>
                alert('Error: Room $room_number in this category is already booked for the selected dates!');
                window.location.href = 'admin_dashboard.php?section=room-dashboard-section';
              </script>";
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // ઇન્સર્ટમાં કોઈ ફેરફારની જરૂર નથી જો તમે offline_booking ટેબલમાં room_type_id નથી રાખવા માંગતા
        $sql_offline = "INSERT INTO offline_booking (room_number, customer_name, phone, checkin_date, checkout_date, payment_status, created_at) 
                        VALUES ('$room_number', '$customer_name', '$phone', '$checkin_date', '$checkout_date', '$payment_status', '$created_at')";
        $res1 = mysqli_query($conn, $sql_offline);

        $today = date('Y-m-d');
        if($checkin_date <= $today) {
            // અહીં પણ room_type_id ચેક કરવો જરૂરી છે
            $sql_update_room = "UPDATE room_numbers SET status = 'Occupied' 
                                WHERE room_number = '$room_number' AND room_type_id = '$room_type_id'";
            mysqli_query($conn, $sql_update_room);
        }

        if ($res1) {
            mysqli_commit($conn);
            header("Location: admin_dashboard.php?section=offline-bookings-section&msg=booked");
        } else {
            throw new Exception("Error saving data.");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}