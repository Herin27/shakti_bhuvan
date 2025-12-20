<?php
// Database connection
include 'db.php'; 


// Fetch all hero images
$result = $conn->query("SELECT background_image FROM hero_section");
$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row['background_image'];
}

// Fetch top 3 rooms
// ✅ Fetch featured rooms from database
$query = "SELECT * FROM rooms WHERE status='Available' ORDER BY id DESC LIMIT 3";
$result = mysqli_query($conn, $query); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shakti Bhuvan</title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">

</head>

<style>
    /* Make all font-awesome icons white */
.amenity .fas, 
.testimonial-user .fas, 
.cta .fas, 
.footer-col .fas, 
.footer-col .fab {
    color: #ffffff;
    margin-top: 20px;
    margin-right: 0px;
}

.amenity .icon {
    font-size: 2rem;
    margin-bottom: 0px;
    display: block;
}
.hero-section {
    position: relative;
    height: 100vh;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    background-size: cover;
    background-position: center;
    transition: background 1s ease-in-out;
}
.footer-col a {
    color: inherit;
    text-decoration: none;
}

.footer-col a:hover {
    text-decoration: none;
}

.hero-buttons button {
    margin: 10px;
    padding: 12px 24px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
}

.explore-btn {
    background: #f1c45f;
}

.contact-btn {
    background: #444;
    color: white;
}

.search-box {
    background: rgba(255, 255, 255, 0.9);
    padding: 15px;
    margin-top: 20px;
    border-radius: 8px;
    display: flex;
    gap: 10px;
}

.search-box input,
.search-box select {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.search-box button {
    background: #f1c45f;
    border: none;
    padding: 10px 15px;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
}

.hero-section {
    position: relative;
    height: 80vh;
    color: #fff;
    overflow: hidden;
}

.hero-slider {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.hero-slider .slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.hero-slider .slide.active {
    opacity: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    top: 50%;
    transform: translateY(-50%);
}

.hero-section::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

.hero-buttons {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 15px;
}
</style>

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
            <a href="index.php" class="active">Home</a>
            <a href="rooms.php">Rooms</a>
            <a href="gallery.php">Gallery</a>
            <a href="contact.php">Contact Us</a>
            <a href="admin.php">Admin</a>
        </nav>

        <div class="contact-info">
            <span><i class="fas fa-phone"></i> +91 92659 00219</span>
            <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
            <a href="rooms.php" class="book-btn">Book Now</a>
        </div>
    </header> -->
    <?php include 'header.php'; ?>

    <div class="hero-section" id="hero-section">
        <!-- Slider Images -->
        <div class="hero-slider">
            <?php if (!empty($images)): ?>
            <?php foreach ($images as $index => $image): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>"
                style="background-image: url('<?php echo $image; ?>');"></div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="slide active" style="background-image: url('assets/images/default-hero.jpg');"></div>
            <?php endif; ?>
        </div>

        <!-- Hero Content -->
        <div class="hero-content">
            <h1>Welcome to <br><span style="color: #f1c45f;">Shakti Bhuvan</span></h1>
            <p>Experience luxury and comfort in our premium rooms with exceptional hospitality and modern amenities</p>

            <div class="hero-buttons">
                <button class="explore-btn">
                    <a href="rooms.php" style="color: #fff; text-decoration: none;">Explore Rooms</a>
                </button>
                <button class="contact-btn">
                    <a href="contact.php" style="color: #fff; text-decoration: none;">Contact Us</a>
                </button>
            </div>

            <!-- Search form -->
            <form action="search.php" method="POST">
                <div class="search-box">
                    <input type="date" name="checkin" id="checkin" required>

                    <input type="date" name="checkout" id="checkout" required>

                    <select name="guests" required>
                        <option value="1">1 Guest</option>
                        <option value="2">2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
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

        </div>
    </div>
    <!-- Slider Script -->
    <script>
    let slides = document.querySelectorAll(".hero-slider .slide");
    let slideIndex = 0;

    function showNextSlide() {
        slides[slideIndex].classList.remove("active");
        slideIndex = (slideIndex + 1) % slides.length;
        slides[slideIndex].classList.add("active");
    }
    if (slides.length > 1) {
        setInterval(showNextSlide, 4000); // Change every 4 sec
    }
    </script>

    <section class="featured-rooms">
        <h2 class="section-title">Our Featured Rooms</h2>
        <p class="section-subtitle">
            Discover our carefully curated rooms designed for comfort, luxury, and unforgettable experiences
        </p>

        <div class="rooms-container">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php 
                    // ✅ Handle multiple images
                    $images = !empty($row['image']) ? explode(',', $row['image']) : [];
                    $firstImage = !empty($images[0]) ? trim($images[0]) : 'default.jpg';
                ?>
            <div class="room-card">
                <!-- Room Image -->
                <img src="uploads/<?php echo htmlspecialchars($firstImage); ?>"
                    alt="<?php echo htmlspecialchars($row['name']); ?>" class="room-img">

                <div class="room-content">
                    <!-- Title + Rating -->
                    <div class="room-header">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <span class="rating">⭐ <?php echo htmlspecialchars($row['rating'] ?? '4.5'); ?></span>
                    </div>

                    <!-- Description -->
                    <p class="room-desc">
                        <?php echo htmlspecialchars(substr($row['description'], 0, 70)); ?>...
                    </p>

                    <!-- Amenities -->
                    <div class="features">
                        <?php 
                            $amenities = !empty($row['amenities']) ? explode(',', $row['amenities']) : [];
                            foreach($amenities as $amenity): ?>
                        <span class="tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price + Button -->
                    <div class="room-footer">
                        <span class="price">₹<?php echo (int)$row['discount_price']; ?><small>/night</small></span>
                        <a href="View_Details.php?id=<?php echo $row['id']; ?>" class="view_btn">View Details</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p class="no-rooms">No rooms found at the moment. Please check back later!</p>
            <?php endif; ?>
        </div>

        <div class="view-all">
            <a href="rooms.php" class="view_btn">View All Rooms</a>
        </div>
    </section>

    <section class="amenities">
    <h2 class="section-title">Premium Amenities</h2>
    <p class="section-subtitle">
        Enjoy world-class facilities and services designed to make your stay comfortable and memorable
    </p>

    <div class="amenities-container">
        <div class="amenity">
            <div class="icon"><i class="fas fa-wifi"></i></div>
            <h3>Free Wi-Fi</h3>
            <p>High-speed internet throughout</p>
        </div>
        <div class="amenity">
            <div class="icon"><i class="fas fa-car"></i></div>
            <h3>Free Parking</h3>
            <p>Secure parking for guests</p>
        </div>
        <div class="amenity">
            <div class="icon"><i class="fas fa-utensils"></i></div>
            <h3>Restaurant</h3>
            <p>In-house dining options</p>
        </div>
        <div class="amenity">
            <div class="icon"><i class="fas fa-concierge-bell"></i></div>
            <h3>Concierge</h3>
            <p>24/7 guest services</p>
        </div>
        <div class="amenity">
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
            <h3>Security</h3>
            <p>Round-the-clock security</p>
        </div>
        <div class="amenity">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <h3>24/7 Service</h3>
            <p>Always here for you</p>
        </div>
    </div>
</section>
    <section class="testimonials">
        <h2 class="section-title">What Our Guests Say</h2>
        <p class="section-subtitle">
            Read testimonials from our valued guests who have experienced the Shakti Bhuvan hospitality
        </p>

        <div class="testimonials-container">

            <!-- Testimonial Card 1 -->
            <div class="testimonial-card">
                <div class="testimonial-header">
                    <span class="quote">❝</span>
                    <span class="stars">★★★★★</span>
                </div>
                <p class="testimonial-text">
                    "Exceptional service and beautiful rooms. The warm hospitality made our stay memorable."
                </p>
                <div class="testimonial-user">
                    <div class="user-icon">♡</div>
                    <div>
                        <h4>Priya Sharma</h4>
                        <span class="location">📍 ahmedabad</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial Card 2 -->
            <div class="testimonial-card">
                <div class="testimonial-header">
                    <span class="quote">❝</span>
                    <span class="stars">★★★★★</span>
                </div>
                <p class="testimonial-text">
                    "Perfect location and amazing amenities. Highly recommend for business travelers."
                </p>
                <div class="testimonial-user">
                    <div class="user-icon">♡</div>
                    <div>
                        <h4>Rajesh Kumar</h4>
                        <span class="location">📍 mahesana</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial Card 3 -->
            <div class="testimonial-card">
                <div class="testimonial-header">
                    <span class="quote">❝</span>
                    <span class="stars">★★★★☆</span>
                </div>
                <p class="testimonial-text">
                    "Clean, comfortable, and great value for money. Will definitely stay here again."
                </p>
                <div class="testimonial-user">
                    <div class="user-icon">♡</div>
                    <div>
                        <h4>Anita Patel</h4>
                        <span class="location">📍 porbandar</span>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- Call to Action -->
    <section class="cta">
        <h2>Ready for Your Perfect Stay?</h2>
        <p>
            Book your room today and experience the luxury and comfort that awaits you at Shakti Bhuvan
        </p>
        <div class="cta-buttons">
            <a href="rooms.php" class="btn book">✨ Book Now</a>
            <a href="tel:+9192659 00219" class="btn call">📞 Call Us</a>
        </div>
    </section>

    <!-- Footer -->
    <!-- <footer class="footer">
        <div class="footer-container">

            <div class="footer-col">
                <div class="logo">
                    <div class="logo-icon">
                        <a href="index.php"><img src="assets/images/logo.png" alt="Shakti Bhuvan Logo"></a>
                    </div>
                    <div class="logo-text">
                        <h1 style="color: #fff;">Shakti Bhuvan</h1>
                        <span>Premium Stays</span>
                    </div>
                </div>
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
                <li>
                    📍 Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110
                </li>
                <li>
                    📞 <a href="tel:+919265900219">+91 92659 00219</a>
                </li>
                <li>
                    ✉️ <a href="mailto:shaktibhuvanambaji@gmail.com">shaktibhuvanambaji@gmail.com</a>
                </li>
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
            <p>© 2025 Shakti Bhuvan Powerd By <span><a style="text-decoration: none;" href="https://www.veloxgroup.co.in/">Velox Group</a></span>. All rights reserved.</p>
            <div>
                <a href="#">Privacy Policy</a> |
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer> -->
    <?php include 'footer.php'; ?>



    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Pass PHP array to JS
    let images = <?php echo json_encode($images); ?>;
    let heroSection = document.getElementById('hero-section');
    let index = 0;

    // Function to change background
    function changeBackground() {
        if (images.length > 0) {
            heroSection.style.background = `url('${images[index]}') center center/cover no-repeat`;
            index = (index + 1) % images.length; // Loop images
        }
    }

    // Initial load
    changeBackground();

    // Change every 5 seconds
    setInterval(changeBackground, 5000);
    </script>

</body>

</html>