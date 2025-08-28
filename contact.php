<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/contact.css">
  <title>Contact Us Section</title>
  <style>
    body {
      margin: 0;
      font-family: "Inter", sans-serif;
      background-color: #faf7f1; /* Beige background */
    }

    .contact-section {
      text-align: center;
      padding: 60px 20px;
      background-color: #faf7f1;
    }

    .contact-section h2 {
      font-size: 32px;
      color: #7b5c3d; /* Elegant brown */
      margin-bottom: 15px;
      font-weight: 600;
    }

    .contact-section p {
      font-size: 18px;
      color: #6c6c6c;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.6;
    }
  </style>
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

  <section class="contact-section">
    <h2>Contact Us</h2>
    <p>
      We're here to help make your stay exceptional. Reach out to us for reservations, inquiries,
      or any assistance you may need.
    </p>
  </section>

<section class="contact-wrapper">
  <!-- Left: Contact Form -->
  <div class="contact-form">
  <h3>📩 Send us a Message</h3>
  <form action="save_message.php" method="POST">
    <div class="form-row">
      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="fullname" placeholder="Enter your full name" required>
      </div>
      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="Enter your phone number">
      </div>
      <div class="form-group">
        <label>Subject</label>
        <input type="text" name="subject" placeholder="What is this regarding?">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Check-in Date (Optional)</label>
        <input type="date" name="checkin">
      </div>
      <div class="form-group">
        <label>Check-out Date (Optional)</label>
        <input type="date" name="checkout">
      </div>
    </div>

    <div class="form-group full">
      <label>Message *</label>
      <textarea name="message" rows="4" placeholder="Tell us how we can help you..." required></textarea>
    </div>

    <button type="submit" class="btn-send">✉️ Send Message</button>
  </form>
</div>


  <!-- Right: Contact Info -->
  <div class="contact-info-box">
    <h3>📞 Get in Touch</h3>
    <p>
      Whether you’re planning a stay, have questions about our services, or need
      assistance with an existing reservation, our dedicated team is here to help.
      We pride ourselves on providing exceptional service and personalized
      attention to every guest.
    </p>

    <!-- Info Cards -->
    <div class="info-card">
      <div class="icon">📱</div>
      <div>
        <h4>Phone</h4>
        <p>+91 98765 43210 <br> +91 98765 43211</p>
        <span>Available 24/7 for reservations</span>
      </div>
    </div>

    <div class="info-card">
      <div class="icon">✉️</div>
      <div>
        <h4>Email</h4>
        <p>info@shaktibhuvan.com <br> reservations@shaktibhuvan.com</p>
        <span>We’ll respond within 2 hours</span>
      </div>
    </div>

    <div class="info-card">
      <div class="icon">📍</div>
      <div>
        <h4>Address</h4>
        <p>123 Luxury Lane, Hotel District, Mumbai, MH 400001</p>
        <span>Prime location in the city center</span>
      </div>
    </div>

    <div class="info-card">
      <div class="icon">🕒</div>
      <div>
        <h4>Reception Hours</h4>
        <p>24/7 Front Desk <br> Check-in: 2:00 PM | Check-out: 12:00 PM</p>
        <span>Always here to assist you</span>
      </div>
    </div>

    <!-- Map Box -->
    <div class="map-box">
      <div class="map-placeholder">
        <span>📍 Our Location</span>
        <p>Interactive map coming soon</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h4>Quick Actions</h4>
      <ul>
        <li>📞 Call for Immediate Assistance</li>
        <li>📧 Email Our Reservations Team</li>
        <li>💬 Live Chat Support</li>
      </ul>
    </div>
  </div>
</section>






  
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
</body>
</html>
