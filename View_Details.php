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
$discountPrice = (float)$room['discount_price']; // FINAL PRICE

// ----------------------------------------------------------------------
// FIX 1: Correctly process image paths from the database.
// The database saves the path (e.g., 'uploads/image.jpg'). We split the string,
// clean it up, and remove any preceding 'uploads/' if it exists to ensure
// the final HTML path is correct.
// ----------------------------------------------------------------------
$raw_images = array_filter(array_map('trim', explode(',', $room['image'])));
$images = [];

foreach ($raw_images as $path) {
    // Check if the path starts with 'uploads/' and remove it for the second time prepended later
    if (strpos($path, 'uploads/') === 0) {
        $images[] = $path; // Keep the full path as saved in DB
    } else {
        // If the path somehow only saved the filename, prepend 'uploads/'
        $images[] = 'uploads/' . $path;
    }
}
// Note: We use the full path saved in the DB in the HTML below.
// ----------------------------------------------------------------------
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
  <link rel="icon" href="assets/images/logo.png" type="image/x-icon">

  <style>
    .logo-icon img { width: 60px; height: auto; border-radius: 50%; margin-right: 10px; }

    /* Slider styles */
    .room-slider { position: relative; max-width: 900px; height: 370px; margin-bottom: 20px; overflow: hidden; border-radius: 8px; }
    .slider-wrapper { display: flex; transition: transform 0.4s ease-in-out; }
    .slider-wrapper img { width: 100%; flex-shrink: 0; object-fit: cover; border-radius: 8px; height: 350px; }
    .slider-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; padding: 10px 14px; cursor: pointer; border-radius: 50%; font-size: 20px; }
    .slider-btn.prev { left: 10px; }
    .slider-btn.next { right: 10px; }
  </style>
</head>
<body>

<header class="navbar">
  <div class="logo">
    <div class="logo-icon">
      <img src="assets/images/logo.png" alt="Shakti Bhuvan Logo">
    </div>
    <div class="logo-text">
      <h1>Shakti Bhuvan</h1>
      <span>Premium Stays</span>
    </div>
  </div>

  <nav class="nav-links">
    <a href="index.php">Home</a>
    <a href="rooms.php" class="active">Rooms</a>
    <a href="gallery.php">Gallery</a>
    <a href="contact.php">Contact</a>
    <a href="admin.php">Admin</a>
  </nav>

  <div class="contact-info">
    <span><i class="fas fa-phone"></i> +91 98765 43210</span>
    <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
    <!-- <a href="rooms.php" class="book-btn">Book Now</a> -->
  </div>
</header>

<div class="container">
  <div>
    <div class="room-slider">
      <div class="slider-wrapper">
        <?php 
        // FIX 2: Use the full path from the $images array without prepending 'uploads/'
        if (!empty($images)): 
            foreach($images as $img_path): 
        ?>
            <img src="<?php echo htmlspecialchars($img_path); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
        <?php 
            endforeach;
        else:
        // Fallback image if no images are found
        ?>
            <img src="assets/images/placeholder_room.jpg" alt="No Room Image Available">
        <?php endif; ?>
      </div>
      <button class="slider-btn prev">&#10094;</button>
      <button class="slider-btn next">&#10095;</button>
    </div>

    <h1 class="room-title"><?php echo htmlspecialchars($room['name']); ?></h1>
    <div class="meta"><?php echo $room['size']; ?> • <?php echo $room['bed_type']; ?> • Up to <?php echo $room['guests']; ?> guests</div>

    <div class="price-box">
      ₹<?php echo number_format($discountPrice,2); ?> 
      <del>₹<?php echo number_format($roomPrice,2); ?></del>
    </div>
    <p class="desc"><?php echo $room['description']; ?></p>

    <div class="card">
      <h3>Room Amenities</h3>
      <ul>
        <?php foreach(array_map('trim', explode(',', $room['amenities'])) as $a): ?>
          <li><?php echo htmlspecialchars($a); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <form method="post" action="booking_form.php" id="bookNowForm">
    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
    <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room['name']); ?>">
    <input type="hidden" name="room_price" id="room_price" value="<?php echo $roomPrice; ?>">
    <input type="hidden" name="room_discount" id="room_discount" value="<?php echo $discountPrice; ?>">
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
        <div class="row discount">
          <span>Room rate (per night)</span>
          <strong id="roomDiscount">₹<?php echo number_format($discountPrice,2); ?></strong>
        </div>

        <div class="row">
          <span>You save</span>
          <strong id="roomRate">₹<?php echo number_format($roomPrice - $discountPrice,2); ?></strong>
        </div>

        <div class="row">
          <span>Nights</span>
          <strong id="showNights">—</strong>
        </div>

        <div class="row">
          <span id="taxLabel">Taxes & fees (—)</span>
          <strong id="roomTax">₹0.00</strong>
        </div>
        <hr>

        <div class="row total">
          <span>Total</span>
          <strong id="totalPrice">₹0.00</strong>
        </div>
      </div>

      <button type="submit" id="bookNowBtn" class="book-btn" disabled>Book Now</button>
      <p class="note">Free cancellation up to 24 hours before check-in</p>
    </div>
  </form>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const fmtINR = (value) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value);

const roomPrice = parseFloat(document.getElementById('room_price').value);
const roomDiscount = parseFloat(document.getElementById('room_discount').value); // FINAL PRICE

const checkinEl = document.getElementById('checkin');
const checkoutEl = document.getElementById('checkout');
const nightsEl = document.getElementById('nights');
const hiddenTotalEl = document.getElementById('hiddenTotalPrice');
const showNightsEl = document.getElementById('showNights');
const totalPriceEl = document.getElementById('totalPrice');
const roomTaxEl = document.getElementById('roomTax');
const bookBtn = document.getElementById('bookNowBtn');
const taxLabel = document.getElementById('taxLabel');

// GST slabs as per govt
function getGstRate(price) {
  if (price < 1000) return 0;
  else if (price <= 7500) return 5; 
  else return 18;
}

function updateTotals(checkinDate, checkoutDate) {
  if (!checkinDate || !checkoutDate) {
    showNightsEl.innerText = '—';

    const gstRate = getGstRate(roomDiscount);
    const gstAmount = (roomDiscount * gstRate) / 100;

    taxLabel.innerText = `Taxes & fees (${gstRate}%)`;
    roomTaxEl.innerText = fmtINR(gstAmount);

    const total = roomDiscount + gstAmount;
    totalPriceEl.innerText = fmtINR(total);

    hiddenTotalEl.value = total.toFixed(2);
    bookBtn.disabled = true;
    return;
  }

  const diffMs = checkoutDate - checkinDate;
  const nights = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (nights <= 0) {
    showNightsEl.innerText = 'Invalid';
    totalPriceEl.innerText = fmtINR(0);
    bookBtn.disabled = true;
    return;
  }

  const perNight = roomDiscount;
  const subtotal = perNight * nights;

  const gstRate = getGstRate(roomDiscount);
  const gstAmount = (subtotal * gstRate) / 100;
  const total = subtotal + gstAmount;

  showNightsEl.innerText = nights;
  taxLabel.innerText = `Taxes & fees (${gstRate}%)`;
  roomTaxEl.innerText = fmtINR(gstAmount);
  totalPriceEl.innerText = fmtINR(total);

  nightsEl.value = nights;
  hiddenTotalEl.value = total.toFixed(2);
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
      updateTotals(null, null);
    }
  }
});

// Slider
const slider = document.querySelector('.room-slider');
const wrapper = slider.querySelector('.slider-wrapper');
const slides = wrapper.querySelectorAll('img');
let index = 0;

slider.querySelector('.next').addEventListener('click', () => {
  index = (index + 1) % slides.length;
  wrapper.style.transform = `translateX(-${index * 100}%)`;
});
slider.querySelector('.prev').addEventListener('click', () => {
  index = (index - 1 + slides.length) % slides.length;
  wrapper.style.transform = `translateX(-${index * 100}%)`;
});

updateTotals(null, null);
</script>

</body>
</html>