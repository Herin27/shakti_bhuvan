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
$customer_id = null;
$sql_check_user = "SELECT customer_id FROM users WHERE phone = '$phone'";
$result_check_user = mysqli_query($conn, $sql_check_user);

if (mysqli_num_rows($result_check_user) > 0) {
    $user = mysqli_fetch_assoc($result_check_user);
    $customer_id = $user['customer_id'];
    mysqli_query($conn, "UPDATE users SET bookings = bookings + 1, total_spent = total_spent + $total_price WHERE customer_id = '$customer_id'");
} else {
    $new_customer_id = 'CUST' . mt_rand(1000, 9999);
    $insert_email = empty($email) ? "NULL" : "'$email'";
    mysqli_query($conn, "INSERT INTO users (customer_id, name, email, phone, member_since, bookings, total_spent) VALUES ('$new_customer_id', '$customer_name', $insert_email, '$phone', CURDATE(), 1, $total_price)");
    $customer_id = $new_customer_id;
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