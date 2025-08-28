<?php
include 'db.php';

// Fetch rooms
$result = mysqli_query($conn, "SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Rooms & Suites</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/css/rooms.css">
  <link rel="stylesheet" href="./assets/css/navbar.css">
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
        <a href="index.php">Home</a>
        <a href="/rooms.php" class="active">Rooms</a>
        <a href="contact.php">Contact</a>
    </nav>

    <div class="contact-info">
        <span><i class="fas fa-phone"></i> +91 98765 43210</span>
        <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
        <a href="rooms.php" class="book-btn">Book Now</a>
    </div>
</header>
<!-- ===== Search Bar Section ===== -->
<section class="search-section">
  <h2>Our Rooms & Suites</h2>
  <p>Choose from our selection of comfortable and luxurious accommodations</p>
  <form action="rooms.php" method="GET" class="search-bar">
    <div class="form-group">
      <label>Check-in</label>
      <input type="date" name="checkin" required>
    </div>
    <div class="form-group">
      <label>Check-out</label>
      <input type="date" name="checkout" required>
    </div>
    <div class="form-group">
      <label>Guests</label>
      <select name="guests">
        <option value="1">1 Guest</option>
        <option value="2">2 Guests</option>
        <option value="3">3 Guests</option>
        <option value="4">4 Guests</option>
        <option value="5+">5+ Guests</option>
      </select>
    </div>
    <button type="submit" class="btn">🔍 Search</button>
  </form>
</section>

<!-- ===== Rooms Listing ===== -->
<div class="rooms-container">

    <?php while($row = mysqli_fetch_assoc($result)): ?>
  <div class="room-card">
    <!-- Room Image -->
    <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="room-img">

    <div class="room-content">
      <!-- Room Title + Rating -->
      <div class="room-header">
        <h3><?php echo $row['name']; ?></h3>
        <span class="rating">⭐ <?php echo $row['rating']; ?></span>
      </div>

      <!-- Room Description -->
      <p class="room-desc">
        <?php echo substr($row['description'], 0, 70); ?>...
      </p>

      <!-- Amenities (tags like your design) -->
      <div class="features">
        <?php 
          $amenities = !empty($row['amenities']) ? explode(',', $row['amenities']) : [];
          foreach($amenities as $amenity): ?>
            <span class="tag"><?php echo trim($amenity); ?></span>
        <?php endforeach; ?>
      </div>

      <!-- Price + Button -->
      <div class="room-footer">
        <span class="price">₹<?php echo $row['price']; ?><small>/night</small></span>
        <a href="View_Details.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
      </div>
    </div>
  </div>
<?php endwhile; ?>
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
        <!-- <li><a href="#">Amenities</a></li> -->
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

</body>
</html>
