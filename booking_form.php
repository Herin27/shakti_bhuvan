<?php
session_start();
include 'db.php'; // if needed for additional checks

function post($key, $default = null) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// Step 1: Initial booking from view_details.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['room_id'])) {
    $room_id    = intval(post('room_id'));
    $room_name  = post('room_name', '');
    $checkin    = post('checkin', '');
    $checkout   = post('checkout', '');
    $roomPrice  = (float) post('room_price', 0);
    $discount   = (float) post('room_discount', 0);
    $taxFee     = (float) post('tax_fee', 500);
    $extraBeds  = max(0, intval(post('beds', 0))); // capture if sent

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

    $perNight = max(0, $roomPrice - $discount);
    $calculatedTotal = ($perNight * $calcNights) + $taxFee + ($extraBeds * 100);

    $_SESSION['booking'] = [
        'room_id'      => $room_id,
        'room_name'    => $room_name,
        'checkin'      => $checkin,
        'checkout'     => $checkout,
        'nights'       => $calcNights,
        'price'        => $roomPrice,
        'discount'     => $discount,
        'tax_fee'      => $taxFee,
        'extra_beds'   => $extraBeds,
        'total_price'  => $calculatedTotal
    ];
}

if (!isset($_SESSION['booking'])) {
    die("No booking data found. Please select room & dates first.");
}

$booking = $_SESSION['booking'];

// Step 2: Handle updates (like user entering beds here)
$extraBeds = isset($_POST['beds']) ? intval($_POST['beds']) : (isset($booking['extra_beds']) ? $booking['extra_beds'] : 0);

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
$totalPrice = ($perNight * $nights) + (float)$booking['tax_fee'] + ($extraBeds * 100);

// Update session again
$_SESSION['booking']['extra_beds'] = $extraBeds;
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

      <label>No. of Extra Bed</label>
      <input type="number" name="beds" class="date-input" min="0" value="<?= $extraBeds ?>">
      <button type="button" name="addBedBtn" class="add-extra-bed-btn">Add Extra Bed</button>
<br>

      <!-- <label>Coupon code</label>
      <div class="form-group">
  <input type="text" name="coupon" class="date-input" placeholder="Enter Coupon Code" class="form-control">
  <button type="button" id="applyCouponBtn" class="add-extra-bed-btn">Apply Coupon</button>
  <small id="couponMessage" style="color:red;"></small>
</div> -->

<input type="hidden" name="discount" value="0">


      <hr>

      <!-- Hidden booking details forwarded to booking_submit.php -->
      <input type="hidden" name="room_id" value="<?= htmlspecialchars($booking['room_id']) ?>">
      <input type="hidden" name="checkin" value="<?= htmlspecialchars($booking['checkin']) ?>">
      <input type="hidden" name="checkout" value="<?= htmlspecialchars($booking['checkout']) ?>">
      <input type="hidden" name="total_price" value="<?= htmlspecialchars($totalPrice) ?>">
      <input type="hidden" name="beds" value="<?= htmlspecialchars($extraBeds) ?>">

      <div class="price-details">
        <div class="row"><span>Room Rate (per night)</span><strong>₹<?= number_format($perNight,2) ?></strong></div>
        <div class="row"><span>Nights</span><strong><?= $nights ?></strong></div>
        <div class="row"><span>Extra Beds</span><strong><?= $extraBeds ?> × ₹100</strong></div>
<div class="row"><span>Discount</span><strong>₹0.00</strong></div>
<div class="row total"><span>Total:</span><strong>₹<?= number_format($totalPrice,2) ?></strong></div>

        
      </div>

      <button type="submit" class="book-btn2">Submit & Pay</button>
      <p class="note">Free cancellation up to 24 hours before check-in</p>
    </div>
  </form>
</div>

<!-- Footer -->
    <footer class="footer">
        <div class="footer-container">

            <!-- About -->
            <div class="footer-col">
                <h3 class="logo"><span class="logo-icon">S</span> Shakti Bhuvan</h3>
                <p>
                    Experience luxury and comfort in our premium rooms with exceptional hospitality and modern
                    amenities.
                </p>
                <div class="social-icons">
                    <a href="#">🌐</a>

                    <a href="#">📘</a>
                    <a href="#">🐦</a>
                    <a href="#">📸</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="rooms.php">Our Rooms</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-col">
                <h4>Contact Info</h4>
                <ul>
                    <li>📍 Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110</li>
                    <li>📞 +91 98765 43210</li>
                    <li>✉️ info@shaktibhuvan.com</li>
                </ul>
            </div>

            <!-- Services -->
            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li>24/7 Room Service</li>
                    <li>Free Wi-Fi</li>
                    <li>Airport Pickup</li>
                    <li>Laundry Service</li>
                    <li>Concierge</li>
                </ul>
            </div>

        </div>

        <!-- Bottom -->
        <div class="footer-bottom">
            <p>© 2025 Shakti Bhuvan. All rights reserved.</p>
            <div>
                <a href="#">Privacy Policy</a> |
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const bedsInput = document.querySelector("input[name='beds']");
    const couponInput = document.querySelector("input[name='coupon']");
    const applyCouponBtn = document.querySelector("#applyCouponBtn");
    const couponMessage = document.querySelector("#couponMessage");

    const totalPriceField = document.querySelector(".row.total strong");
    const extraBedRow = document.querySelector(".row:nth-child(3) strong"); 
    const discountRow = document.querySelector(".row:nth-child(4) strong"); 

    const hiddenTotal = document.querySelector("input[name='total_price']");
    const hiddenBeds = document.querySelector("input[name='beds']");
    const hiddenDiscount = document.querySelector("input[name='discount']");

    // Values from PHP
    const perNight = <?= $perNight ?>;
    const nights = <?= $nights ?>;
    const taxFee = <?= $booking['tax_fee'] ?>;

    let discountValue = 0;
    let discountPercent = 0;

    function updateTotal() {
        let beds = parseInt(bedsInput.value) || 0;
        let baseTotal = (perNight * nights) + taxFee + (beds * 100);

        discountValue = (discountPercent / 100) * baseTotal;
        let finalTotal = baseTotal - discountValue;

        // Update DOM
        extraBedRow.textContent = beds + " × ₹100";
        discountRow.textContent = "₹" + discountValue.toLocaleString("en-IN", {minimumFractionDigits: 2});
        totalPriceField.textContent = "₹" + finalTotal.toLocaleString("en-IN", {minimumFractionDigits: 2});

        // Update hidden fields
        hiddenTotal.value = finalTotal;
        hiddenBeds.value = beds;
        hiddenDiscount.value = discountValue;
    }

    // Beds update
    bedsInput.addEventListener("input", updateTotal);

    const addBedBtn = document.querySelector("button[name='addBedBtn']");
    if (addBedBtn) {
        addBedBtn.addEventListener("click", function(e) {
            e.preventDefault();
            bedsInput.value = (parseInt(bedsInput.value) || 0) + 1;
            updateTotal();
        });
    }

    // Coupon Apply (AJAX call)
    applyCouponBtn.addEventListener("click", function() {
        let code = couponInput.value.trim();
        if (!code) {
            couponMessage.textContent = "Please enter a coupon code";
            return;
        }

        fetch("apply_coupon.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "code=" + encodeURIComponent(code)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                discountPercent = data.discount_percent;
                couponMessage.style.color = "green";
                couponMessage.textContent = "Coupon applied: " + discountPercent + "% off";
            } else {
                discountPercent = 0;
                couponMessage.style.color = "red";
                couponMessage.textContent = data.message;
            }
            updateTotal();
        })
        .catch(() => {
            couponMessage.style.color = "red";
            couponMessage.textContent = "Error applying coupon";
        });
    });

    // Initial calculation
    updateTotal();
});
</script>





</body>
</html>
