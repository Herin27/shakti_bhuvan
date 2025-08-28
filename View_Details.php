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

// Convert comma separated values into arrays
$amenities = array_map('trim', explode(',', $room['amenities']));
$features  = array_map('trim', explode(',', $room['features']));
$policies  = array_map('trim', explode(',', $room['policies']));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room['name']; ?> - Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/view_details.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>
<body>
<header class="navbar">
    <div class="logo">
        <div class="logo-icon">S</div>
        <div class="logo-text">
            <h1>Shakti Bhuvan</h1>
            <span>Premium Stays</span>
        </div>
    </div>

    <nav class="nav-links">
        <a href="index.php" class="active">Home</a>
        <a href="/rooms.php">Rooms</a>
        <a href="contact.php">Contact</a>
    </nav>

    <div class="contact-info">
        <span><i class="fas fa-phone"></i> +91 98765 43210</span>
        <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
        <a href="booking.php" class="book-btn">Book Now</a>
    </div>
</header>
<div class="container">

  <!-- Left: Room Info -->
  <div>
    <div class="room-image">
      <img src="uploads/<?php echo $room['image']; ?>" alt="<?php echo $room['name']; ?>">
    </div>

    <h1 class="room-title"><?php echo $room['name']; ?></h1>
    <div class="meta">
      <?php echo $room['size']; ?> • <?php echo $room['bed_type']; ?> • Up to <?php echo $room['guests']; ?> guests
    </div>

    <div class="price-box">
      ₹<?php echo $room['price']; ?> 
      <del>₹<?php echo $room['discount_price']; ?></del>
    </div>

    <p class="desc"><?php echo $room['description']; ?></p>

    <div class="card">
      <h3>Room Amenities</h3>
      <ul>
        <?php foreach($amenities as $a): ?>
          <li><?php echo htmlspecialchars($a); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="card">
      <h3>Room Features</h3>
      <ul>
        <?php foreach($features as $f): ?>
          <li><?php echo htmlspecialchars($f); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="card">
      <h3>Hotel Policies</h3>
      <ul>
        <?php foreach($policies as $p): ?>
          <li><?php echo htmlspecialchars($p); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>






















  

  <!-- Right: Booking Box -->

<form method="post" action="book_room.php">
  <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
  <input type="hidden" name="checkin" id="checkin">
  <input type="hidden" name="checkout" id="checkout">

  <div class="booking-box">
    <h3><i class="fa fa-calendar"></i> Book Your Stay</h3>

    <!-- Calendar -->
    <div class="calendar-box">
      <label>Select Dates</label>
      <input type="text" id="dateRange" name="dateRange" placeholder="Select Dates">
    </div>

    <!-- Price Details -->
    <div class="price-details">
      <div class="row">
        <span>Room rate (per night)</span>
        <strong id="roomRate">₹<?php echo $room['price']; ?></strong>
      </div>
      <div class="row discount">
        <span>Discount</span>
        <strong id="roomDiscount">-₹<?php echo ($room['discount_price'] > 0 ? $room['discount_price'] : 0); ?></strong>
      </div>
      <div class="row">
        <span>Taxes & fees</span>
        <strong id="roomTax">₹500</strong>
      </div>
      <hr>
      <div class="row total">
        <span>Total</span>
        <strong id="totalPrice">₹<?php echo ($room['price'] - $room['discount_price']) + 500; ?></strong>
      </div>
    </div>

    <!-- Book Button -->
    <button type="submit" class="book-btn">Book Now</button>
    <p class="note">Free cancellation up to 24 hours before check-in</p>
  </div>
</form>

<script>
  document.getElementById('dateRange').addEventListener('change', function() {
      let dates = this.value.split(" to ");
      if (dates.length === 2) {
          let checkin  = new Date(dates[0]);
          let checkout = new Date(dates[1]);

          // ✅ Set hidden fields
          document.getElementById("checkin").value = dates[0];
          document.getElementById("checkout").value = dates[1];

          // ✅ Calculate number of nights
          let timeDiff = checkout.getTime() - checkin.getTime();
          let nights = timeDiff / (1000 * 3600 * 24);

          if (nights > 0) {
              let rate     = <?php echo $room['price']; ?>;
              let discount = <?php echo ($room['discount_price'] > 0 ? $room['discount_price'] : 0); ?>;
              let tax      = 500;

              let total = ((rate - discount) * nights) + tax;

              document.getElementById("totalPrice").innerText = "₹" + total;
          }
      }
  });
</script>






</div>

<footer class="footer">
  <div class="footer-container">
    
    <!-- About -->
    <div class="footer-col">
      <h3 class="logo"><span class="logo-icon">S</span> Shakti Bhuvan</h3>
      <p>
        Experience luxury and comfort in our premium rooms with exceptional hospitality and modern amenities.
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
        <li><a href="#">Home</a></li>
        <li><a href="#">Our Rooms</a></li>
        <li><a href="#">Contact Us</a></li>
        <li><a href="#">Amenities</a></li>
      </ul>
    </div>
    
    <!-- Contact Info -->
    <div class="footer-col">
      <h4>Contact Info</h4>
      <ul>
        <li>📍 123 Luxury Lane, Hotel District, Mumbai, MH 400001</li>
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
<!-- Flatpickr JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> -->
<script>
flatpickr("#dateRange", {
  mode: "range",
  inline: true,
  dateFormat: "Y-m-d",
  minDate: "today",
  onClose: function(selectedDates) {
    if (selectedDates.length === 2) {
      document.getElementById("checkin").value = selectedDates[0].toISOString().split('T')[0];
      document.getElementById("checkout").value = selectedDates[1].toISOString().split('T')[0];
    }
  }
});

document.getElementById('dateRange').addEventListener('change', function() {
      let dates = this.value.split(" - ");
      document.getElementById("checkin").value = dates[0];
      document.getElementById("checkout").value = dates[1];
  });
</script>





<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>




</body>
</html>
