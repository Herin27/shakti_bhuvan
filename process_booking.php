<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rooms.php');
    exit;
}

// --- 1. Data Collection ---
$room_id = intval($_POST['room_id']);
$customer_name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
$email = mysqli_real_escape_string($conn, trim($_POST['email']));
$guests = isset($_POST['guests']) ? intval($_POST['guests']) : 0;
$checkin = mysqli_real_escape_string($conn, $_POST['checkin']);
$checkout = mysqli_real_escape_string($conn, $_POST['checkout']);

// UPDATED: Get the actual count of extra beds
$extra_bed_included = isset($_POST['extra_bed']) ? intval($_POST['extra_bed']) : 0;
$extra_bed_count = ($extra_bed_included == 1) ? intval($_POST['extra_bed_count']) : 0;

$notes = mysqli_real_escape_string($conn, trim($_POST['notes']));

// --- New Availability Check Logic ---

// --- Updated Availability Check Logic (Online + Offline) ---

// ૧. આ રૂમ ટાઈપના કુલ કેટલા રૂમ છે તે શોધો
$sql_total_rooms = "SELECT COUNT(*) as total FROM room_numbers WHERE room_type_id = $room_id";
$res_total = mysqli_query($conn, $sql_total_rooms);
$total_rooms = mysqli_fetch_assoc($res_total)['total'];

// ૨. પસંદ કરેલી તારીખ વચ્ચે ONLINE કેટલા રૂમ બુક છે તે શોધો
$sql_online_booked = "SELECT room_number FROM bookings 
                      WHERE room_id = $room_id 
                      AND status IN ('Confirmed', 'Checked-in') 
                      AND room_number IS NOT NULL
                      AND NOT (checkout <= '$checkin' OR checkin >= '$checkout')";
$res_online = mysqli_query($conn, $sql_online_booked);

$booked_room_list = [];
while ($row = mysqli_fetch_assoc($res_online)) {
    $booked_room_list[] = $row['room_number'];
}

// ૩. પસંદ કરેલી તારીખ વચ્ચે OFFLINE કેટલા રૂમ બુક છે તે શોધો
// નોંધ: આપણે એ જ રૂમ નંબર્સ લેવાના જે આ ચોક્કસ $room_id ના હોય
$sql_offline_booked = "SELECT room_number FROM offline_booking 
                       WHERE NOT (checkout_date <= '$checkin' OR checkin_date >= '$checkout')";
$res_offline = mysqli_query($conn, $sql_offline_booked);

while ($row = mysqli_fetch_assoc($res_offline)) {
    $off_room = $row['room_number'];
    // ચેક કરો કે આ ઓફલાઇન રૂમ આ જ રૂમ ટાઇપ (Category) નો છે?
    $check_type = mysqli_query($conn, "SELECT id FROM room_numbers WHERE room_number = '$off_room' AND room_type_id = $room_id");
    if (mysqli_num_rows($check_type) > 0) {
        $booked_room_list[] = $off_room;
    }
}

// ૪. યુનિક રૂમ નંબર્સનું લિસ્ટ બનાવો અને ગણતરી કરો
$total_booked_count = count(array_unique($booked_room_list));

// ૫. જો બુક થયેલા રૂમ અને કુલ રૂમ સરખા હોય, તો રૂમ ખાલી નથી
if ($total_booked_count >= $total_rooms) {
    echo "<script>
            alert('Sorry, this room type is fully booked (Online/Offline) for the selected dates.');
            window.location.href = 'booking.php?room_id=$room_id';
          </script>";
    exit;
}

// --- 2. Price Calculation ---
$sql_rate = "SELECT name, discount_price, extra_bed_price FROM rooms WHERE id = $room_id";
$result_rate = mysqli_query($conn, $sql_rate);
$room_data = mysqli_fetch_assoc($result_rate);

$room_name = $room_data['name'];
$room_rate_per_night = (float)$room_data['discount_price'];
$extra_bed_rate_per_night = (float)$room_data['extra_bed_price'];



// Calculate Nights first
$date1 = new DateTime($checkin);
$date2 = new DateTime($checkout);
$nights = $date1->diff($date2)->days;
if($nights <= 0) $nights = 1;

// STEP 1: Calculate the Subtotal BEFORE checking tax brackets
$subtotal = ($room_rate_per_night + ($extra_bed_count * $extra_bed_rate_per_night)) * $nights;

// STEP 2: DYNAMIC GST LOGIC based on the calculated subtotal
$tax_rate = 0; // Default decimal rate
$display_tax_pct = 0; // For session display

if ($subtotal <= 1000) {
    $tax_rate = 0.00;
    $display_tax_pct = 0;
} elseif ($subtotal > 1000 && $subtotal <= 7500) {
    $tax_rate = 0.05;
    $display_tax_pct = 5;
} else {
    $tax_rate = 0.18;
    $display_tax_pct = 18;
}

// STEP 3: Final Total Calculation
$total_price = $subtotal * (1 + $tax_rate);

// Add display info to session for payment.php
$_SESSION['temp_tax_rate'] = $tax_rate;
$_SESSION['temp_tax_pct'] = $display_tax_pct;

// --- 3. User Management (No changes needed here) ---
// --- 3. User Management (Updated Based on your SQL Schema) ---
$customer_id = null;

// ફોન નંબર દ્વારા યુઝર પહેલેથી છે કે નહીં તે ચેક કરો
$sql_check_user = "SELECT customer_id FROM users WHERE phone = '$phone' LIMIT 1";
$result_check_user = mysqli_query($conn, $sql_check_user);

if ($result_check_user && mysqli_num_rows($result_check_user) > 0) {
    // કસ્ટમર પહેલેથી છે - તેનો ડેટા અપડેટ કરો
    $user = mysqli_fetch_assoc($result_check_user);
    $customer_id = $user['customer_id'];
    
    $update_user = "UPDATE users SET 
                    bookings = bookings + 1, 
                    total_spent = total_spent + $total_price 
                    WHERE customer_id = '$customer_id'";
    mysqli_query($conn, $update_user);
} else {
    // નવો કસ્ટમર છે - પણ પહેલા ઈમેલ ચેક કરવો જરૂરી છે કારણ કે તે UNIQUE છે
    $check_email = mysqli_query($conn, "SELECT customer_id FROM users WHERE email = '$email' LIMIT 1");
    
    if ($check_email && mysqli_num_rows($check_email) > 0) {
        // જો ઈમેલ મેચ થઈ જાય પણ ફોન અલગ હોય, તો તે જ કસ્ટમર આઈડી વાપરો (Error રોકવા માટે)
        $user_by_email = mysqli_fetch_assoc($check_email);
        $customer_id = $user_by_email['customer_id'];
        
        $update_user = "UPDATE users SET 
                        bookings = bookings + 1, 
                        total_spent = total_spent + $total_price 
                        WHERE customer_id = '$customer_id'";
        mysqli_query($conn, $update_user);
    } else {
        // ફોન અને ઈમેલ બંને નવા છે - હવે INSERT કરો
        $new_customer_id = 'CUST' . mt_rand(1000, 9999);
        $customer_id = $new_customer_id;
        
        // જો ઈમેલ ખાલી હોય તો ડેટાબેઝમાં એરર આવી શકે (કારણ કે તે NOT NULL હોઈ શકે)
        $val_email = empty($email) ? "temp_".mt_rand()."@shaktibhuvan.com" : "$email";
        
        $insert_user = "INSERT INTO users (customer_id, name, email, phone, member_since, bookings, total_spent, status) 
                        VALUES ('$customer_id', '$customer_name', '$val_email', '$phone', CURDATE(), 1, $total_price, 'ACTIVE')";
        
        if (!mysqli_query($conn, $insert_user)) {
            // ટેસ્ટિંગ માટે એરર જોવા: die(mysqli_error($conn));
        }
    }
}

// --- 4. Insert Booking ---
// UPDATED: Storing extra_bed_count in the notes or you can add a column 'extra_bed_count' to your DB table
$sql_insert_booking = "INSERT INTO bookings 
    (customer_name, phone, email, guests, room_id, room_number, checkin, checkout, total_price, extra_bed_included, status, payment_status, notes)
    VALUES 
    ('$customer_name', '$phone', '$email', '$guests', '$room_id', NULL, '$checkin', '$checkout', '$total_price', '$extra_bed_count', 'Pending', 'Pending', '$notes')";

if (mysqli_query($conn, $sql_insert_booking)) {
    $new_booking_id = mysqli_insert_id($conn);
    
    $_SESSION['booking'] = [
        'booking_id' => $new_booking_id,
        'customer_id' => $customer_id,
        'customer_name' => $customer_name,
        'room_id' => $room_id,
        'room_name' => $room_name,
        'total_price' => $total_price,
        'checkin' => $checkin,
        'checkout' => $checkout,
        'nights' => $nights,
        'phone' => $phone,
        'email' => $email,
        'extra_bed_included' => $extra_bed_count, // Store the count here
        'extra_bed_unit_price' => $extra_bed_rate_per_night, // Added for payment.php display
        'room_rate' => $room_rate_per_night
    ];

    header("Location: payment.php");
    exit;
}
?>