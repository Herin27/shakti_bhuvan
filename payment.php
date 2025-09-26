<?php
session_start();
if (!isset($_SESSION['booking'])) {
    echo "No booking found. Please book a room first.";
    exit;
}
$booking = $_SESSION['booking'];

// Razorpay config
$keyId = "rzp_test_RCCShYsW1Qsowp";   // replace with your Test Key ID
$keySecret = "OzAJu511WKdQoEZWl5jXu1bA"; // replace with your Test Secret

require('razorpay-php-master/Razorpay.php'); // ✅ install SDK via composer require razorpay/razorpay
use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

// Amount must be in paise (₹100 = 10000)
$amount = $booking['total_price'] * 100;

// Create order in Razorpay
$orderData = [
    'receipt'         => 'RCPT_' . rand(1000,9999),
    'amount'          => $amount,
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];
$razorpayOrder = $api->order->create($orderData);
$orderId = $razorpayOrder['id']; // ✅ real Razorpay order_id

// if ($paymentSuccess) {
//     $booking_id = $_GET['booking_id'];

//     // Update booking status to Confirmed + Paid
//     $updateBooking = $conn->prepare("UPDATE bookings 
//                                      SET status = 'Confirmed', payment_status = 'Paid' 
//                                      WHERE id = ?");
//     $updateBooking->bind_param("i", $booking_id);
//     $updateBooking->execute();
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - Shakti Bhuvan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/view_details.css">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="icon" href="assets/images/logo.jpg" type="image/x-icon">
</head>
<style>
    .logo-icon img {
    width: 60px;   /* adjust size */
    height: auto;
    border-radius: 50%; /* make circular if needed */
    margin-right: 10px;
}
</style>
<body>

<header class="navbar">
    <div class="logo">
        <div class="logo-icon">
            <img src="assets/images/logo.jpg" alt="Shakti Bhuvan Logo">
        </div>
        <div class="logo-text">
            <h1>Shakti Bhuvan</h1>
            <span>Premium Stays</span>
        </div>
    </div>

    <nav class="nav-links">
        <a href="index.php" >Home</a>
        <a href="rooms.php">Rooms</a>
        <a href="contact.php"class="active">Contact</a>
    </nav>

    <div class="contact-info">
            <span><i class="fas fa-phone"></i> +91 98765 43210</span>
            <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
            <a href="rooms.php" class="book-btn">Book Now</a>
        </div>
</header>

<div class="container">
  <!-- Left: Booking Summary -->
  <div>
    <h1 class="room-title">Payment</h1>
    <p class="desc">Complete your payment securely via Razorpay to confirm your booking.</p>

    <div class="card">
  <h3>Booking Summary</h3>
  <ul>
    <li><strong>Room:</strong> <?= htmlspecialchars($booking['room_name']); ?></li>
    <li><strong>Check-in:</strong> <?= htmlspecialchars($booking['checkin']); ?></li>
    <li><strong>Check-out:</strong> <?= htmlspecialchars($booking['checkout']); ?></li>
    <li><strong>Nights:</strong> <?= $booking['nights']; ?></li>
    <li><strong>Extra Beds:</strong> <?= $booking['extra_beds'] ?? 0; ?> × ₹100</li>
    <li><strong>Discount:</strong> ₹<?= number_format($booking['discount'] ?? 0, 2); ?></li>
    <li><strong>Total Price:</strong> ₹<?= number_format($booking['total_price'], 2); ?></li>
  </ul>
</div>


  </div>

  


  <!-- Right: Razorpay Payment Button -->
  <div class="booking-box">
    <h3>Proceed to Payment</h3>

    <div class="price-details">
      <div class="row total">
        <span>Total Payable:</span>
        <strong>₹<?php echo number_format($booking['total_price'], 2); ?></strong>
      </div>
    </div>

    <button id="payBtn" class="book-btn2">Pay with Razorpay</button>
    <p class="note">Secure payment powered by Razorpay</p>
  </div>
</div>

<footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <h3 class="logo"><span class="logo-icon">S</span> Shakti Bhuvan</h3>
      <p>Experience luxury and comfort in our premium rooms with exceptional hospitality and modern amenities.</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="rooms.php">Our Rooms</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
    </div>
    <div class="footer-col">
      <h4>Contact Info</h4>
      <ul>
        <li>📍 Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110</li>
        <li>📞 +91 98765 43210</li>
        <li>✉️ info@shaktibhuvan.com</li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 Shakti Bhuvan. All rights reserved.</p>
  </div>
</footer>

<script>
document.getElementById('payBtn').onclick = function(e){
    var options = {
        "key": "<?php echo $keyId; ?>",
        "amount": "<?php echo $amount; ?>",
        "currency": "INR",
        "name": "Shakti Bhuvan",
        "description": "Room Booking Payment",
        "order_id": "<?php echo $orderId; ?>", // ✅ real order_id
        "handler": function (response){
            window.location.href = "thank_you.php?payment_id=" + response.razorpay_payment_id + "&order_id=<?php echo $orderId; ?>";
        },
        "prefill": {
            "name": "<?php echo $_SESSION['booking']['customer_name'] ?? 'Guest'; ?>",
            "email": "<?php echo $_SESSION['booking']['email'] ?? ''; ?>",
            "contact": "<?php echo $_SESSION['booking']['phone'] ?? ''; ?>"
        },
        "theme": {
            "color": "#1e40af"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
    e.preventDefault();
}
</script>

</body>
</html>
