<?php
session_start();
include 'db.php'; // Your DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];

    // ✅ Split date range "2025-08-28 to 2025-08-30"
    if (!empty($_POST['dateRange'])) {
        $dates = explode(" to ", $_POST['dateRange']);
        $checkin  = trim($dates[0]);
        $checkout = trim($dates[1]);
    } else {
        $checkin  = $_POST['checkin'];
        $checkout = $_POST['checkout'];
    }

    // Validate dates
    try {
        $date1 = new DateTime($checkin);
        $date2 = new DateTime($checkout);
    } catch (Exception $e) {
        die("Invalid date format.");
    }

    $nights = $date1->diff($date2)->days;

    if ($nights <= 0) {
        echo "<script>alert('Check-out date must be after check-in date.'); window.history.back();</script>";
        exit;
    }

    // ✅ Fetch room details
    $stmt = $conn->prepare("SELECT id, name, price, discount_price FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();

    if ($room) {
        $price_per_night = $room['price'];
        $discount        = $room['discount_price'] ?? 0;
        $tax             = 500;

        // ✅ Total Calculation
        $final_price = (($price_per_night - $discount) * $nights) + $tax;

        // ✅ Insert booking into DB
        $stmt2 = $conn->prepare("INSERT INTO bookings (room_id, checkin, checkout, total_price) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("issd", $room_id, $checkin, $checkout, $final_price);

        if ($stmt2->execute()) {
            // ✅ Update room status to Occupied
            $update = $conn->prepare("UPDATE rooms SET status = 'Occupied' WHERE id = ?");
            $update->bind_param("i", $room_id);
            $update->execute();

            // ✅ Save booking details in SESSION for payment.php
            $_SESSION['booking'] = [
                'room_id'     => $room['id'],
                'room_name'   => $room['name'],
                'checkin'     => $checkin,
                'checkout'    => $checkout,
                'nights'      => $nights,
                'total_price' => $final_price
            ];

            header("Location: payment.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Room not found.";
    }
}

// ✅ Auto-release rooms whose checkout date has passed
$today = date("Y-m-d");
$conn->query("UPDATE rooms r 
              LEFT JOIN bookings b ON r.id = b.room_id 
              SET r.status = 'Available' 
              WHERE b.checkout < '$today'");
?>
