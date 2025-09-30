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
  <link rel="icon" href="assets/images/logo.jpg" type="image/x-icon">
  <style>
    .room-slider {
      position: relative;
      width: 100%;
      max-width: 350px;
      overflow: hidden;
      border-radius: 8px;
    }
    .slider-wrapper {
      display: flex;
      transition: transform 0.4s ease-in-out;
    }
    .slider-wrapper img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      flex-shrink: 0;
      border-radius: 8px;
    }
    .slider-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0,0,0,0.5);
      color: white;
      border: none;
      padding: 8px 12px;
      cursor: pointer;
      border-radius: 50%;
      font-size: 18px;
    }
    .slider-btn.prev { left: 8px; }
    .slider-btn.next { right: 8px; }
  </style>
</head>
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
      <a href="index.php">Home</a>
      <a href="rooms.php" class="active">Rooms</a>
      <a href="gallery.php">Gallery</a>
      <a href="contact.php">Contact</a>
      <a href="admin.php">Admin</a>
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
  <form action="search.php" method="POST">
    <div class="search-box">
        <input type="date" name="checkin" required>
        <input type="date" name="checkout" required>
        <select name="guests" required>
            <option value="1">1 Guest</option>
            <option value="2">2 Guests</option>
            <option value="3">3 Guests</option>
            <option value="4">4 Guests</option>
        </select>
        <button type="submit">Search Rooms</button>
    </div>
  </form>
</section>

<!-- ===== Rooms Listing ===== -->
<div class="rooms-container">

<?php while($row = mysqli_fetch_assoc($result)): ?>
  <div class="room-card">
    <!-- Room Slider -->
    <div class="room-slider">
      <div class="slider-wrapper">
        <?php 
          $images = explode(',', $row['image']); 
          foreach($images as $img): 
              $img = trim($img);
              if (!empty($img)): ?>
                <img src="uploads/<?php echo $img; ?>" alt="<?php echo $row['name']; ?>">
              <?php endif; 
          endforeach; 
        ?>
      </div>
      <button class="slider-btn prev">&#10094;</button>
      <button class="slider-btn next">&#10095;</button>
    </div>

    <div class="room-content">
      <div class="room-header">
        <h3><?php echo $row['name']; ?></h3>
        <span class="rating">⭐ <?php echo $row['rating']; ?></span>
      </div>

      <p class="room-desc">
        <?php echo substr($row['description'], 0, 70); ?>...
      </p>

      <div class="features">
        <?php 
          $amenities = !empty($row['amenities']) ? explode(',', $row['amenities']) : [];
          foreach($amenities as $amenity): ?>
            <span class="tag"><?php echo trim($amenity); ?></span>
        <?php endforeach; ?>
      </div>

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
  <div class="footer-bottom">
    <p>© 2025 Shakti Bhuvan. All rights reserved.</p>
    <div>
      <a href="#">Privacy Policy</a> | 
      <a href="#">Terms of Service</a>
    </div>
  </div>
</footer>

<script>
document.querySelectorAll('.room-slider').forEach(function(slider){
  const wrapper = slider.querySelector('.slider-wrapper');
  const prevBtn = slider.querySelector('.prev');
  const nextBtn = slider.querySelector('.next');
  const slides = wrapper.querySelectorAll('img');
  let index = 0;

  function updateSlider() {
    wrapper.style.transform = `translateX(-${index * 100}%)`;
  }

  nextBtn.addEventListener('click', () => {
    index = (index + 1) % slides.length;
    updateSlider();
  });

  prevBtn.addEventListener('click', () => {
    index = (index - 1 + slides.length) % slides.length;
    updateSlider();
  });
});
</script>

</body>
</html>
