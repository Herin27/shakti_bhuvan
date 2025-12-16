<?php
// Start session at the very beginning of the script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rooms.php'); // Redirect if not a form submission
    exit;
}

// --- 1. Data Collection and Validation ---
$room_id = intval($_POST['room_id']);
$customer_name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
$email = mysqli_real_escape_string($conn, trim($_POST['email']));
$guests = intval($_POST['guests']);
$checkin = mysqli_real_escape_string($conn, $_POST['checkin']);
$checkout = mysqli_real_escape_string($conn, $_POST['checkout']);
$physical_room_number = mysqli_real_escape_string($conn, $_POST['physical_room_number']);
$extra_bed_included = isset($_POST['extra_bed']) ? intval($_POST['extra_bed']) : 0;
$notes = mysqli_real_escape_string($conn, trim($_POST['notes']));

// Basic validation
if ($room_id <= 0 || empty($customer_name) || empty($phone) || empty($checkin) || empty($checkout) || empty($physical_room_number) || $guests <= 0) {
    die("<script>alert('Error: Missing required booking details.'); window.history.back();</script>");
}

// Ensure checkout is after checkin for calculation
if (strtotime($checkin) >= strtotime($checkout)) {
    die("<script>alert('Error: Check-out date must be after Check-in date.'); window.history.back();</script>");
}

// --- 2. Calculate Total Price (Re-calculate for security) ---

$sql_rate = "SELECT name, discount_price, extra_bed_price FROM rooms WHERE id = $room_id";
$result_rate = mysqli_query($conn, $sql_rate);
$room_data = mysqli_fetch_assoc($result_rate);

if (!$room_data) {
    die("<script>alert('Error: Room rate could not be fetched.'); window.history.back();</script>");
}

$room_name = $room_data['name']; // Store room name for session
$room_rate_per_night = (float)$room_data['discount_price'];
$extra_bed_rate_per_night = (float)$room_data['extra_bed_price'];
$tax_rate = 0.05; // 5%

// Calculate Nights
$date1 = new DateTime($checkin);
$date2 = new DateTime($checkout);
$interval = $date1->diff($date2);
$nights = $interval->days;

if ($nights <= 0) {
     die("<script>alert('Error: Booking must be for at least one night.'); window.history.back();</script>");
}

// Calculation
$room_charge = $room_rate_per_night * $nights;
$extra_bed_charge = $extra_bed_included ? ($extra_bed_rate_per_night * $nights) : 0;
$subtotal = $room_charge + $extra_bed_charge;
$taxes = $subtotal * $tax_rate;
$total_price = $subtotal + $taxes;


// --- 3. Manage Customer Data (Insert or Update User) ---
// Using phone number as the primary unique key for customer lookups

$customer_id = null;
$sql_check_user = "SELECT customer_id, bookings, total_spent FROM users WHERE phone = '$phone'";
$result_check_user = mysqli_query($conn, $sql_check_user);

if (mysqli_num_rows($result_check_user) > 0) {
    // User found
    $user = mysqli_fetch_assoc($result_check_user);
    $customer_id = $user['customer_id'];
    
    // *** FIX IS HERE ***
    // Only update the email field if the submitted email is NOT empty.
    $email_update_part = !empty($email) ? ", email = '$email'" : "";
    
    // Update user stats
    $sql_update_user = "UPDATE users 
                        SET name = '$customer_name', 
                            bookings = bookings + 1,
                            total_spent = total_spent + $total_price 
                            $email_update_part 
                        WHERE customer_id = '$customer_id'";
    mysqli_query($conn, $sql_update_user);
    
} else {
    // New user
    $unique = false;
    while(!$unique) {
        $new_customer_id = 'CUST' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $check_id = mysqli_query($conn, "SELECT customer_id FROM users WHERE customer_id = '$new_customer_id'");
        if(mysqli_num_rows($check_id) == 0) {
            $unique = true;
        }
    }
    
    // Note: If $email is empty, this insert might still fail if you have a NOT NULL constraint on email.
    // Assuming 'email' is NULLABLE, otherwise you must default it to NULL if empty.
    $insert_email = empty($email) ? 'NULL' : "'$email'";
    
    $sql_insert_user = "INSERT INTO users (customer_id, name, email, phone, member_since, bookings, total_spent, status)
                        VALUES ('$new_customer_id', '$customer_name', $insert_email, '$phone', CURDATE(), 1, $total_price, 'ACTIVE')";
                        
    if (mysqli_query($conn, $sql_insert_user)) {
        $customer_id = $new_customer_id;
    } else {
        die("<script>alert('Error: Could not create new customer record. Check if phone/email unique constraints are met.'); window.history.back();</script>");
    }
}


// --- 4. Insert Booking Record ---

$sql_insert_booking = "INSERT INTO bookings 
    (customer_name, phone, email, guests, room_id, room_number, checkin, checkout, total_price, extra_bed_included, status, payment_status, notes)
    VALUES 
    ('$customer_name', '$phone', '$email', '$guests', '$room_id', '$physical_room_number', '$checkin', '$checkout', '$total_price', '$extra_bed_included', 'Pending', 'Pending', '$notes')";

if (mysqli_query($conn, $sql_insert_booking)) {
    $new_booking_id = mysqli_insert_id($conn);
    
    // --- 5. IMMEDIATE Status Update for Double-Booking Prevention ---
    $sql_update_room_status = "UPDATE room_numbers SET status = 'Occupied' WHERE room_number = '$physical_room_number'";
    mysqli_query($conn, $sql_update_room_status);

    // --- 6. Set SESSION and Redirect to Payment Page ---
    
    // Store essential booking data in session for payment processing
    $_SESSION['booking'] = [
        'booking_id' => $new_booking_id,
        'customer_id' => $customer_id,
        'customer_name' => $customer_name,
        'email' => $email,
        'phone' => $phone,
        'room_name' => $room_name,
        'room_id' => $room_id,
        'room_number' => $physical_room_number,
        'checkin' => $checkin,
        'checkout' => $checkout,
        'nights' => $nights,
        'total_price' => $total_price,
        'room_rate' => $room_rate_per_night,
        'extra_bed_included' => $extra_bed_included
    ];

    header("Location: payment.php");
    exit;
    
} else {
    die("<script>alert('Error: Booking insertion failed: " . mysqli_error($conn) . "'); window.history.back();</script>");
}

?>