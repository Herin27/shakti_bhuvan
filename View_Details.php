<?php
include 'db.php';

// get room id from URL
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM rooms WHERE id = $room_id";
$result = mysqli_query($conn, $sql);
$room = mysqli_fetch_assoc($result);

if(!$room){
    echo "Room not found!";
    exit;
}

// ensure numeric values
$roomPrice = (float)$room['price'];
$discountPrice = (float)$room['discount_price'];
$taxFee = 500; // same tax as earlier
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($room['name']); ?> - Details</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/view_details.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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

<!-- your header (same as before) -->
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
  <!-- Left: Room Info -->
  <div>
    <div class="room-image"><img src="uploads/<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>"></div>
    <h1 class="room-title"><?php echo htmlspecialchars($room['name']); ?></h1>
    <div class="meta"><?php echo $room['size']; ?> • <?php echo $room['bed_type']; ?> • Up to <?php echo $room['guests']; ?> guests</div>

    <div class="price-box">₹<?php echo number_format($roomPrice,2); ?> <del>₹<?php echo number_format($discountPrice,2); ?></del></div>
    <p class="desc"><?php echo $room['description']; ?></p>

    <div class="card">
      <h3>Room Amenities</h3>
      <ul>
        <?php foreach(array_map('trim', explode(',', $room['amenities'])) as $a): ?>
          <li><?php echo htmlspecialchars($a); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <!-- features & policies... (same as earlier) -->
  </div>

  <!-- Right: Booking Box (dynamic date selection + price calc) -->
  <form method="post" action="booking_form.php" id="bookNowForm">
    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
    <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room['name']); ?>">
    <input type="hidden" name="room_price" id="room_price" value="<?php echo $roomPrice; ?>">
    <input type="hidden" name="room_discount" id="room_discount" value="<?php echo $discountPrice; ?>">
    <input type="hidden" name="tax_fee" id="tax_fee" value="<?php echo $taxFee; ?>">

    <input type="hidden" name="checkin" id="checkin">
    <input type="hidden" name="checkout" id="checkout">
    <input type="hidden" name="nights" id="nights">
    <input type="hidden" name="total_price" id="hiddenTotalPrice">

    <div class="booking-box">
      <h3><i class="fa fa-calendar"></i> Book Your Stay</h3>

      <div class="calendar-box">
        <label>Select Dates</label>
        <input type="text" id="dateRange" name="dateRange" placeholder="Select Dates">
      </div>

      <div class="price-details">
        <div class="row">
          <span>Room rate (per night)</span>
          <strong id="roomRate">₹<?php echo number_format($roomPrice,2); ?></strong>
        </div>
        <div class="row discount">
          <span>Discount</span>
          <strong id="roomDiscount">-₹<?php echo number_format($discountPrice,2); ?></strong>
        </div>
        <div class="row">
          <span>Nights</span>
          <strong id="showNights">—</strong>
        </div>
        <div class="row">
          <span>Taxes & fees</span>
          <strong id="roomTax">₹<?php echo number_format($taxFee,2); ?></strong>
        </div>
        <hr>
        <div class="row total">
          <span>Total</span>
          <strong id="totalPrice">₹<?php 
              // default 1 night price shown
              $defaultTotal = (($roomPrice - $discountPrice) * 1) + $taxFee;
              echo number_format($defaultTotal,2);
            ?></strong>
        </div>
      </div>

      <button type="submit" id="bookNowBtn" class="book-btn" disabled>Book Now</button>
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
            <p>© 2024 Shakti Bhuvan. All rights reserved.</p>
            <div>
                <a href="#">Privacy Policy</a> |
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Formatting helper
const fmtINR = (value) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value);

// grab values from server (safe defaults)
const roomPrice = parseFloat(document.getElementById('room_price').value) || 0;
const roomDiscount = parseFloat(document.getElementById('room_discount').value) || 0;
const taxFee = parseFloat(document.getElementById('tax_fee').value) || 0;

const dateRangeEl = document.getElementById('dateRange');
const checkinEl = document.getElementById('checkin');
const checkoutEl = document.getElementById('checkout');
const nightsEl = document.getElementById('nights');
const hiddenTotalEl = document.getElementById('hiddenTotalPrice');
const showNightsEl = document.getElementById('showNights');
const totalPriceEl = document.getElementById('totalPrice');
const bookBtn = document.getElementById('bookNowBtn');

function updateTotals(checkinDate, checkoutDate) {
  if (!checkinDate || !checkoutDate) {
    showNightsEl.innerText = '—';
    totalPriceEl.innerText = fmtINR((roomPrice - roomDiscount) * 1 + taxFee);
    hiddenTotalEl.value = ((roomPrice - roomDiscount) * 1 + taxFee).toFixed(2);
    nightsEl.value = 1;
    bookBtn.disabled = true;
    return;
  }

  const diffMs = checkoutDate - checkinDate;
  const nights = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  if (isNaN(nights) || nights <= 0) {
    showNightsEl.innerText = 'Invalid';
    totalPriceEl.innerText = fmtINR(0);
    hiddenTotalEl.value = '0';
    nightsEl.value = 0;
    bookBtn.disabled = true;
    return;
  }

  const perNight = (roomPrice - roomDiscount);
  const total = (perNight * nights) + taxFee;

  showNightsEl.innerText = nights;
  totalPriceEl.innerText = fmtINR(total);
  hiddenTotalEl.value = total.toFixed(2);
  nightsEl.value = nights;
  bookBtn.disabled = false;
}

flatpickr("#dateRange", {
  mode: "range",
  inline: false,
  dateFormat: "Y-m-d",
  minDate: "today",
  onChange: function(selectedDates) {
    if (selectedDates.length === 2) {
      const ci = selectedDates[0];
      const co = selectedDates[1];

      checkinEl.value = ci.toISOString().split('T')[0];
      checkoutEl.value = co.toISOString().split('T')[0];

      updateTotals(ci, co);
    } else {
      // reset
      checkinEl.value = '';
      checkoutEl.value = '';
      updateTotals(null, null);
    }
  }
});

// initialize default totals for 1 night (no dates selected)
updateTotals(null, null);
</script>
</body>
</html>
