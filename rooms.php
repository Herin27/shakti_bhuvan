<?php
include 'db.php';

// Fetch rooms
$result = mysqli_query($conn, "SELECT * FROM rooms");
function getAmenityIcon($name) {
    $name = strtolower(trim($name));
    if (strpos($name, 'wifi') !== false) return 'fa-wifi';
    if (strpos($name, 'ac') !== false) return 'fa-snowflake';
    if (strpos($name, 'tv') !== false) return 'fa-tv';
    if (strpos($name, 'service') !== false) return 'fa-concierge-bell';
    if (strpos($name, 'parking') !== false) return 'fa-car';
    return 'fa-check-circle'; // Default icon
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Rooms & Suites</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/rooms.css">
    <link rel="stylesheet" href="./assets/css/navbar.css">
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    <style>
    .search-section {
        text-align: center;
        padding: 40px 20px 20px 20px;
        background: #f5f0e6;
    }

    .rooms-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding: 40px;
        max-width: 1200px;
        margin-bottom: 150px;
    }

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
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 50%;
        font-size: 18px;
    }

    .slider-btn.prev {
        left: 8px;
    }

    .slider-btn.next {
        right: 8px;
    }

    @media (max-width: 768px) {
        .search-box {
            flex-direction: column;
            padding: 20px;
            gap: 15px;
        }

        .search-box input,
        .search-box select,
        .search-box button {
            width: 100% !important;
            min-width: unset;
        }
    }
    </style>
</head>

<body>
    <!-- <header class="navbar">
  <div class="logo">
    <div class="logo-icon">
        <a href="index.php"><img src="assets/images/logo.png" alt="Shakti Bhuvan Logo"></a>
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
      <span><i class="fas fa-phone"></i> +91 92659 00219</span>
      <span><i class="fas fa-envelope"></i> shaktibhuvanambaji@gmail.com</span>
      <a href="rooms.php" class="book-btn">Book Now</a>
  </div>
</header> -->
    <?php include 'header.php'; ?>

    <!-- ===== Search Bar Section ===== -->
    <section class="search-section">
        <h2>Rooms & Suites</h2>
        <p>Choose from our selection of comfortable and luxurious accommodations</p>
        <form action="search.php" method="POST">
            <div class="search-box">
                <input type="date" name="checkin" id="checkin" required>
                <input type="date" name="checkout" id="checkout" required>

                <select name="guests" required>
                    <option value="">Total Guests</option>
                    <?php for($i=1; $i<=30; $i++) echo "<option value='$i'>$i Guest".($i>1?'s':'')."</option>"; ?>
                </select>

                <select name="rooms_needed" required>
                    <option value="1">1 Room</option>
                    <?php for($i=2; $i<=10; $i++) echo "<option value='$i'>$i Rooms</option>"; ?>
                </select>

                <button type="submit">Search Rooms</button>
            </div>
        </form>

        <script>
        // ✅ Set default check-in to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').value = today;
        document.getElementById('checkin').setAttribute('min', today);

        // ✅ Set checkout default to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        document.getElementById('checkout').value = tomorrowStr;
        document.getElementById('checkout').setAttribute('min', tomorrowStr);
        </script>

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
                <!-- <button class="slider-btn prev">&#10094;</button> -->
                <!-- <button class="slider-btn next">&#10095;</button> -->
            </div>

            <div class="room-content">
                <div class="room-header">
                    <h3><?php echo $row['name']; ?></h3>
                    <span class="rating">⭐ <?php echo $row['rating']; ?></span>
                </div>

                <p class="room-desc">
                    <?php echo substr($row['description'], 0, 70); ?>...
                </p>

                <div class="features" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                    <?php 
                    $amenities = !empty($row['amenities']) ? explode(',', $row['amenities']) : [];
                    foreach(array_slice($amenities, 0, 4) as $amenity): 
                ?>
                    <span class="tag"
                        style="background: #5a4636; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; display: flex; align-items: center; gap: 5px;">
                        <i class="fas <?php echo getAmenityIcon($amenity); ?>" style="color: #fff;"></i>
                        <?php echo htmlspecialchars(trim($amenity)); ?>
                    </span>
                    <?php endforeach; ?>
                </div>

                <div class="room-footer">
                    <span class="price">₹<?php echo (int)$row['discount_price']; ?><small> Per Night</small></span>
                    <a href="View_Details.php?id=<?php echo $row['id']; ?>" class="view_btn">View Details</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>

    </div>

    <?php include 'footer.php'; ?>

    <!-- <footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <h3 class="logo"><span class="logo-icon">S</span> Shakti Bhuvan</h3>
      <p>Experience luxury and comfort in our premium rooms with exceptional hospitality and modern amenities.</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="rooms.php">Rooms</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Contact Info</h4>
      <ul>
        <li>📍 Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110</li>
        <li>📞 +91 92659 00219</li>
        <li>✉️ shaktibhuvanambaji@gmail.com</li>
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
</footer> -->

    <script>
    document.querySelectorAll('.room-slider').forEach(function(slider) {
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