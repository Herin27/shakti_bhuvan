<style>
    .footer {
  background: #8a6642;
  color: white;
  padding: 50px 20px 20px;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 40px;
  margin-bottom: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Footer Col */
.footer-col h3,
.footer-col h4 {
  margin-bottom: 15px;
  font-weight: 700;
}

.footer-col p {
  color: #e0e0e0;
  margin-bottom: 15px;
}

.footer-col ul {
  list-style: none;
  padding: 0;
}

.footer-col ul li {
  margin-bottom: 10px;
  color: #e0e0e0;
}

.footer-col a {
  color: #f5d179;
  text-decoration: none;
  transition: 0.3s;
}

.footer-col a:hover {
  text-decoration: underline;
}

/* Social Icons */
.social-icons a {
  margin-right: 10px;
  font-size: 1.2rem;
}

/* Footer Bottom */
.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 15px;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  font-size: 0.9rem;
  max-width: 1200px;
  margin: 0 auto;
}

.footer-bottom a {
  color: #f5d179;
}


.location {
  font-size: 0.9rem;
  color: #777;
}
</style>

<footer class="footer">
  <div class="footer-container">

    <!-- About Us -->
    <div class="footer-col">
      <h4>About Us</h4>
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
        <li>📍 Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110</li>
        <li>📞 +91 98765 43210</li>
        <li>✉️ herin7151@gmail.com </li>
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
      <p>© 2024 MyHotel. All rights reserved.</p>
      <div>
        <a href="#">Privacy Policy</a> | 
        <a href="#">Terms of Service</a>
      </div>
    </div>
</footer>
</body>
</html>