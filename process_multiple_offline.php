<?php
include 'db.php';

if (isset($_POST['bulk_book'])) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $payment_status = $_POST['payment_status'];
    $checkin = $_POST['checkin_date'];
    $checkout = $_POST['checkout_date'];
    $selected_rooms = $_POST['selected_rooms']; // Array of room numbers

    if (empty($selected_rooms)) {
        echo "<script>alert('મહેરબાની કરીને ઓછામાં ઓછો એક રૂમ પસંદ કરો.'); window.history.back();</script>";
        exit;
    }

    $success_count = 0;
    foreach ($selected_rooms as $room_no) {
        $room_no = mysqli_real_escape_string($conn, $room_no);
        
        // ૧. Offline Booking ટેબલમાં એન્ટ્રી
        $sql = "INSERT INTO offline_booking (room_number, customer_name, phone, checkin_date, checkout_date, payment_status, status) 
                VALUES ('$room_no', '$customer_name', '$phone', '$checkin', '$checkout', '$payment_status', 'Checked-in')";
        
        if (mysqli_query($conn, $sql)) {
            // ૨. રૂમનું સ્ટેટસ Occupied કરવું (વૈકલ્પિક, જો તમારા લોજિકમાં હોય તો)
            mysqli_query($conn, "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$room_no'");
            $success_count++;
        }
    }

    echo "<script>
            alert('$success_count રૂમ સફળતાપૂર્વક બુક થઈ ગયા છે.');
            window.location.href = 'admin_dashboard.php?section=offline-bookings-section';
          </script>";
}
?><?php
include 'db.php';

if (isset($_POST['bulk_book'])) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $payment_status = $_POST['payment_status'];
    $checkin = $_POST['checkin_date'];
    $checkout = $_POST['checkout_date'];
    $selected_rooms = $_POST['selected_rooms']; // Array of room numbers

    if (empty($selected_rooms)) {
        echo "<script>alert('Please select at least one room.'); window.history.back();</script>";
        exit;
    }

    $success_count = 0;
    foreach ($selected_rooms as $room_no) {
        $room_no = mysqli_real_escape_string($conn, $room_no);
        
        // ૧. Offline Booking ટેબલમાં એન્ટ્રી
        $sql = "INSERT INTO offline_booking (room_number, customer_name, phone, checkin_date, checkout_date, payment_status, status) 
                VALUES ('$room_no', '$customer_name', '$phone', '$checkin', '$checkout', '$payment_status', 'Checked-in')";
        
        if (mysqli_query($conn, $sql)) {
            // ૨. રૂમનું સ્ટેટસ Occupied કરવું (વૈકલ્પિક, જો તમારા લોજિકમાં હોય તો)
            mysqli_query($conn, "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$room_no'");
            $success_count++;
        }
    }

    echo "<script>
            alert('$success_count રૂમ સફળતાપૂર્વક બુક થઈ ગયા છે.');
            window.location.href = 'admin_dashboard.php?section=offline-bookings-section';
          </script>";
}
?>