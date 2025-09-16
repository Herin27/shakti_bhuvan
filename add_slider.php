<?php
// Database connection
include 'db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bg_image'])) {
    $fileName = $_FILES['bg_image']['name'];
    $fileTmp = $_FILES['bg_image']['tmp_name'];
    $uploadDir = "uploads/";
    $uploadPath = $uploadDir . basename($fileName);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $conn->query("TRUNCATE hero_section");
        $stmt = $conn->prepare("INSERT INTO hero_section (background_image) VALUES (?)");
        $stmt->bind_param("s", $uploadPath);
        $stmt->execute();
        $message = "<p class='success'>✅ Background updated successfully!</p>";
    } else {
        $message = "<p class='error'>❌ Image upload failed!</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Upload Background - Shakti Bhuvan</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/view_details.css"> <!-- Reuse same CSS -->
  <style>
    /* Make page take full height */
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 50px;
    background-color: #fdfbf6;
    width: 100%;
    margin: 0 auto;

}

    /* Main content grows to push footer down */
    .container {
      flex: 1;
    }

    .upload-box {
      max-width: 500px;
      margin: 40px auto;
      padding: 30px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .upload-box h2 {
      margin-bottom: 20px;
      font-weight: 600;
      text-align: center;
    }
    .upload-box input[type="file"] {
      display: block;
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #f9f9f9;
    }
    .upload-box button {
      display: block;
      width: 100%;
      padding: 12px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      background: #0077ff;
      color: #fff;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .upload-box button:hover {
      background: #005ec4;
    }
    .success { color: green; text-align:center; margin-bottom:15px; }
    .error { color: red; text-align:center; margin-bottom:15px; }

    /* Footer always at bottom */
    .footer {
      
      color: #fff;
      padding: 20px 0;
    }
    .footer-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      max-width: 1100px;
      margin: auto;
      padding: 0 20px;
    }
    .footer-col {
      flex: 1;
      min-width: 250px;
      margin: 15px 0;
    }
    .footer-bottom {
      text-align: center;
      margin-top: 15px;
      border-top: 1px solid rgba(255,255,255,0.2);
      padding-top: 10px;
    }
    .footer a { color: #fff; text-decoration: none; }
    .footer a:hover { text-decoration: underline; }
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
    <a href="rooms.php">Rooms</a>
    <a href="contact.php">Contact</a>
  </nav>
  <div class="contact-info">
    <span>+91 98765 43210</span>
    <span>info@shaktibhuvan.com</span>
    <a href="booking.php" class="book-btn">Book Now</a>
  </div>
</header>

<div class="container">
  <div class="upload-box">
    <h2>Upload Background Image</h2>
    <?= $message ?>
    <form method="POST" enctype="multipart/form-data">
      <label>Select Background Image:</label>
      <input type="file" name="bg_image" required>
      <button type="submit">Save Background</button>
    </form>
  </div>
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
        <li><a href="#">Home</a></li>
        <li><a href="#">Our Rooms</a></li>
        <li><a href="#">Contact Us</a></li>
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
  </div>
  <div class="footer-bottom">
    <p>© 2024 Shakti Bhuvan. All rights reserved.</p>
  </div>
</footer>
</body>
</html>
