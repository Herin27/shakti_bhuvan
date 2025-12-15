<?php
session_start();
include 'db.php';  // your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and ensure correct types
    $name       = mysqli_real_escape_string($conn, $_POST['name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $location   = mysqli_real_escape_string($conn, $_POST['location']);
    $guests     = intval($_POST['guests']);
    $room_id    = intval($_POST['room_id']);
    $checkin    = mysqli_real_escape_string($conn, $_POST['checkin']);
    $checkout   = mysqli_real_escape_string($conn, $_POST['checkout']);
    $totalPrice = floatval($_POST['total_price']);
    $createdAt  = date("Y-m-d H:i:s");
    
    $extraBeds  = isset($_POST['beds']) ? intval($_POST['beds']) : 0;
    $discount   = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
    
    // Default values for new user insertion
    $defaultBookings = 1;
    $defaultStatus = 'ACTIVE';
    $defaultRating = NULL; // Rating is DECIMAL(3,1) NULL, bind as NULL in query
    $rating_placeholder = NULL; // Needs to be a variable to pass to bind_param

    // Update session with final submitted values
    $_SESSION['booking']['customer_name'] = $name;
    $_SESSION['booking']['email'] = $email;
    $_SESSION['booking']['phone'] = $phone;
    $_SESSION['booking']['location'] = $location;
    $_SESSION['booking']['guests'] = $guests;
    $_SESSION['booking']['extra_beds'] = $extraBeds;
    $_SESSION['booking']['discount'] = $discount;
    $_SESSION['booking']['total_price'] = $totalPrice;


    // 1️⃣ Insert or Update user in users table
    $checkUser = $conn->prepare("SELECT customer_id FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $result = $checkUser->get_result();
    $checkUser->close(); // Close statement after use

    if ($result->num_rows > 0) {
        // Existing user → update bookings count & total spent
        $user = $result->fetch_assoc();
        $customer_id = $user['customer_id'];

        $updateUser = $conn->prepare("UPDATE users 
                                      SET bookings = bookings + 1, total_spent = total_spent + ? 
                                      WHERE customer_id = ?");
        $updateUser->bind_param("ds", $totalPrice, $customer_id); // d=decimal, s=string
        $updateUser->execute();
        $updateUser->close();

    } else {
        // New user → insert
        $customer_id = "CUST" . rand(1000, 9999);
        $defaultBookings = 1;
        $defaultStatus = 'ACTIVE';
        
        // FIX 1: Define the NULL variable for the 'rating' column (DECIMAL(3,1) NULL)
        $rating_null = NULL; 

        // FIX 2: Include all 9 placeholders in the INSERT statement.
        // We use CURDATE() outside the placeholders, and bind the rest.
        // FIX 3: Ensure 'rating' placeholder is included in the query and bound.
        $insertUser = $conn->prepare("INSERT INTO users 
            (customer_id, name, email, phone, location, member_since, bookings, total_spent, rating, status) 
            VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?)");
            
        // Bind types: s s s s s i d s s (9 total characters)
        // Bind order: customer_id, name, email, phone, location, bookings(1), total_spent, rating(NULL), status(ACTIVE)
        $bind_types = "sssssidss"; 

        // Bind parameters: 9 variables
        $insertUser->bind_param($bind_types, 
            $customer_id, 
            $name, 
            $email, 
            $phone, 
            $location, 
            $defaultBookings, 
            $totalPrice,       // d: decimal/double
            $rating_null,      // s: NULL (Must be bound as type 's' in MySQLi)
            $defaultStatus     // s: string
        );

        if (!$insertUser->execute()) {
             // If user insert fails, log the error and stop
             echo "Error inserting new user: " . $insertUser->error;
             $insertUser->close();
             exit();
        }
        $insertUser->close();
    }

    // 2️⃣ Insert into bookings table
    $status = "Confirmed";            // Setting directly to Confirmed as payment redirect is next
    $payment_status = "Pending";    

    $insertBooking = $conn->prepare("INSERT INTO bookings 
        (customer_name, phone, guests, room_id, checkin, checkout, total_price, created_at, status, payment_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
    // Bind types: s s i i s s d s s s 
    $insertBooking->bind_param("ssiissdsss", 
        $name, 
        $phone, 
        $guests, 
        $room_id, 
        $checkin, 
        $checkout, 
        $totalPrice, 
        $createdAt, 
        $status, 
        $payment_status
    );

    if ($insertBooking->execute()) {
        $booking_id = $conn->insert_id; // Get the booking ID for payment tracking

        // Redirect to payment and pass booking ID
        header("Location: payment.php?booking_id=" . $booking_id);
        exit();
    } else {
        echo "Error inserting booking: " . $insertBooking->error;
    }

    $insertBooking->close();

}
?>