<?php
session_start();
include 'db.php'; // if needed for additional checks (not strictly required here)

// A small helper to safely fetch POST data
function post($key, $default = null) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// If user POSTed from view_details.php, create/overwrite session booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['room_id'])) {
    // sanitize incoming values
    $room_id    = intval(post('room_id'));
    $room_name  = post('room_name', '');
    $checkin    = post('checkin', '');
    $checkout   = post('checkout', '');
    $nights     = max(0, intval(post('nights', 0)));
    $postedTotal= (float) post('total_price', 0);
    $roomPrice  = (float) post('room_price', 0);
    $discount   = (float) post('room_discount', 0);
    $taxFee     = (float) post('tax_fee', 500);

    // Server-side validation: ensure dates valid and nights > 0
    try {
        $d1 = new DateTime($checkin);
        $d2 = new DateTime($checkout);
        $calcNights = $d1->diff($d2)->days;
    } catch (Exception $e) {
        die("Invalid dates provided. Please go back and select valid check-in/check-out dates.");
    }

    if ($calcNights <= 0) {
        die("Check-out date must be after check-in date. Please go back and choose valid dates.");
    }

    // Recalculate on server (do not trust client POST entirely)
    $perNight = max(0, $roomPrice - $discount);
    $calculatedTotal = ($perNight * $calcNights) + $taxFee;

    // Save booking data into session (use server-calculated totals)
    $_SESSION['booking'] = [
        'room_id'      => $room_id,
        'room_name'    => $room_name,
        'checkin'      => $checkin,
        'checkout'     => $checkout,
        'nights'       => $calcNights,
        'price'        => $roomPrice,
        'discount'     => $discount,
        'tax_fee'      => $taxFee,
        'total_price'  => $calculatedTotal
    ];

    // after setting session we continue to render the confirmation page
}

// If no session booking is set, we cannot proceed
if (!isset($_SESSION['booking'])) {
    die("No booking data found. Please select room & dates first.");
}

$booking = $_SESSION['booking'];

// Extra safety: recompute server-side again
try {
    $d1 = new DateTime($booking['checkin']);
    $d2 = new DateTime($booking['checkout']);
    $nights = $d1->diff($d2)->days;
} catch (Exception $e) {
    die("Booking dates invalid.");
}
if ($nights <= 0) {
    die("Check-out date must be after check-in date. Please go back and choose valid dates.");
}
$perNight = max(0, (float)$booking['price'] - (float)$booking['discount']);
$totalPrice = ($perNight * $nights) + (float)$booking['tax_fee'];

// Update session with server-truth
$_SESSION['booking']['nights'] = $nights;
$_SESSION['booking']['total_price'] = $totalPrice;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Book Now - Shakti Bhuvan</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/view_details.css">
</head>
<body>
<header class="navbar">
  <div class="logo"><div class="logo-icon">S</div><div class="logo-text"><h1>Shakti Bhuvan</h1><span>Premium Stays</span></div></div>
  <nav class="nav-links"><a href="index.php">Home</a><a href="rooms.php" class="active">Rooms</a><a href="contact.php">Contact</a></nav>
  <div class="contact-info"><span>+91 98765 43210</span><span>info@shaktibhuvan.com</span><a href="booking.php" class="book-btn">Book Now</a></div>
</header>

<div class="container">
  <div>
    <h1 class="room-title">Booking Confirmation</h1>
    <p class="desc">Please review your booking details below and complete your information to confirm your stay.</p>

    <div class="card">
      <h3>Booking Summary</h3>
      <ul>
        <li><strong>Room:</strong> <?= htmlspecialchars($booking['room_name']) ?></li>
        <li><strong>Check-in:</strong> <?= htmlspecialchars($booking['checkin']) ?></li>
        <li><strong>Check-out:</strong> <?= htmlspecialchars($booking['checkout']) ?></li>
        <li><strong>Total Nights:</strong> <?= $nights ?></li>
        <li><strong>Total Price:</strong> ₹<?= number_format($totalPrice, 2) ?></li>
      </ul>
    </div>
  </div>

  <form action="booking_submit.php" method="POST">
    <div class="booking-box">
      <h3>Guest Information</h3>

      <label>Name</label>
      <input type="text" name="name" class="date-input" required>

      <label>Email</label>
      <input type="email" name="email" class="date-input" required>

      <label>Phone</label>
      <input type="text" name="phone" class="date-input" required>

      <label>Location</label>
      <input type="text" name="location" class="date-input">

      <label>Guests</label>
      <input type="number" name="guests" class="date-input" min="1" value="1">

      <!-- Hidden booking details forwarded to booking_submit.php -->
      <input type="hidden" name="room_id" value="<?= htmlspecialchars($booking['room_id']) ?>">
      <input type="hidden" name="checkin" value="<?= htmlspecialchars($booking['checkin']) ?>">
      <input type="hidden" name="checkout" value="<?= htmlspecialchars($booking['checkout']) ?>">
      <input type="hidden" name="total_price" value="<?= htmlspecialchars($totalPrice) ?>">

      <div class="price-details">
        <div class="row"><span>Room Rate (per night)</span><strong>₹<?= number_format($perNight,2) ?></strong></div>
        <div class="row"><span>Nights</span><strong><?= $nights ?></strong></div>
        <div class="row"><span>Taxes & Fees</span><strong>₹<?= number_format($booking['tax_fee'],2) ?></strong></div>
        <div class="row total"><span>Total:</span><strong>₹<?= number_format($totalPrice,2) ?></strong></div>
      </div>

      <button type="submit" class="book-btn2">Submit & Pay</button>
      <p class="note">Free cancellation up to 24 hours before check-in</p>
    </div>
  </form>
</div>

<footer class="footer"> ... </footer>
</body>
</html>
