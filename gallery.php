<?php
include 'db.php';

// Fetch all images grouped by type
$result = $conn->query("SELECT * FROM gallery ORDER BY image_type, created_at DESC");

$gallery = [];
while ($row = $result->fetch_assoc()) {
    $gallery[$row['image_type']][] = $row;
}
$categories = ['Hotel View', 'Luxury Suite', 'Deluxe Room', 'Standard Room'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotel Gallery</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="./assets/css/navbar.css">
  <link rel="icon" href="assets/images/logo.jpg" type="image/x-icon">
  <style>
    /* Reset + layout */
    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    body {
        font-family: Arial, sans-serif;
    }

    main {
        flex: 1; /* push footer down */
        padding-top: 120px; /* space for fixed navbar */
        max-width: 1800px;
        margin: 0 auto;
        width: 100%;
    }

    /* Category buttons */
    .category-buttons {
        text-align: center;
        margin: 20px 0;
    }
    .category-buttons button {
        margin: 5px;
        padding: 10px 20px;
        border: none;
        background: #c4a36f;
        color: white;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background 0.3s;
    }
    .category-buttons button:hover,
    .category-buttons button.active {
        background: #a57e3d;
    }

    /* Gallery grid */
    .gallery-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    .gallery-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    .gallery-item:hover {
        transform: scale(1.05);
    }
    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .gallery-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.6);
        color: #fff;
        padding: 10px;
        text-align: center;
        font-size: 16px;
    }

    /* Gallery section toggle */
    .gallery-section {
        display: none;
    }
    .gallery-section.active {
        display: block;
    }

    /* Fix navbar overlap if it's fixed */
    header.navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
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
        <a href="gallery.php" class="active">Gallery</a>
        <a href="contact.php">Contact</a>
        <a href="admin.php">Admin</a>
    </nav>

    <div class="contact-info">
        <span><i class="fas fa-phone"></i> +91 98765 43210</span>
        <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
        <a href="rooms.php" class="book-btn">Book Now</a>
    </div>
</header>

<main>
  <h1 style="text-align:center;">Hotel Gallery</h1>
  <p style="text-align:center;">Explore our hotel photos by categories</p>

  <!-- Category Buttons -->
  <div class="category-buttons">
    <?php foreach($categories as $cat): ?>
      <button onclick="showCategory('<?php echo $cat; ?>')"><?php echo $cat; ?></button>
    <?php endforeach; ?>
  </div>

  <!-- Gallery Sections -->
  <?php foreach($gallery as $type => $images): ?>
    <div class="gallery-section" id="<?php echo $type; ?>">
      <div class="gallery-container">
        <?php foreach($images as $img): ?>
          <div class="gallery-item">
            <img src="<?php echo $img['image_url']; ?>" alt="<?php echo htmlspecialchars($type); ?>">
            <div class="gallery-caption"><?php echo htmlspecialchars($type); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</main>

<?php include 'footer.php'; ?>

<script>
    function showCategory(category) {
        // Hide all sections
        document.querySelectorAll('.gallery-section').forEach(sec => {
            sec.classList.remove('active');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.category-buttons button').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected category
        document.getElementById(category).classList.add('active');
        
        // Mark clicked button active
        event.target.classList.add('active');
    }

    // Auto-show first category when page loads
    document.addEventListener("DOMContentLoaded", () => {
        const firstBtn = document.querySelector(".category-buttons button");
        if(firstBtn){
            firstBtn.click();
        }
    });
</script>
</body>
</html>
