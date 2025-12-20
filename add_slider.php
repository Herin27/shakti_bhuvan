<?php
// Database connection
include 'db.php';
include 'header.php';

$message = "";

// ✅ Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bg_image'])) {
    $fileName = $_FILES['bg_image']['name'];
    $fileTmp = $_FILES['bg_image']['tmp_name'];
    $uploadDir = "uploads/";
    $uploadPath = $uploadDir . basename($fileName);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $stmt = $conn->prepare("INSERT INTO hero_section (background_image) VALUES (?)");
        $stmt->bind_param("s", $uploadPath);
        $stmt->execute();
        $message = "<p class='success'>✅ Image uploaded successfully!</p>";
    } else {
        $message = "<p class='error'>❌ Image upload failed!</p>";
    }
}

// ✅ Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);

    // Get file path from DB
    $res = $conn->query("SELECT background_image FROM hero_section WHERE id = $deleteId");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $filePath = $row['background_image'];

        // Delete file from server
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from DB
        $conn->query("DELETE FROM hero_section WHERE id = $deleteId");
        $message = "<p class='success'>🗑️ Image deleted successfully!</p>";
    }
}

// Fetch all images
$result = $conn->query("SELECT * FROM hero_section ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Upload Background - Shakti Bhuvan</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/view_details.css">
  <style>
    /* html, body { height: 100%; margin: 0; display: flex; flex-direction: column; font-family: 'Inter', sans-serif; } */
    
    .container { flex:1; }

    /* Upload Section */
    .upload-box { max-width: 600px; margin: 50px auto; padding: 35px; background: linear-gradient(135deg, #ffffff, #fdfbf6); border-radius: 16px; box-shadow: 0 6px 20px rgba(0,0,0,0.1); text-align: center; }
    .upload-box h2 { margin-bottom: 20px; font-weight: 700; font-size: 22px; color: #333; }
    .upload-box label { display: block; margin-bottom: 10px; font-weight: 600; color: #444; }
    .upload-box input[type="file"] { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 10px; background: #fafafa; cursor: pointer; }
    .upload-box button { width: 100%; padding: 14px; font-size: 16px; font-weight: 600; border: none; border-radius: 10px; background: #0077ff; color: #fff; cursor: pointer; }
    .upload-box button:hover { background: #005ec4; }
    .success { color: green; text-align:center; margin-bottom:15px; }
    .error { color: red; text-align:center; margin-bottom:15px; }

    /* Gallery Section */
    .slider-gallery { max-width: 1100px; margin: 60px auto; padding: 30px; background: #fff; border-radius: 16px; box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
    .slider-gallery h3 { text-align: center; font-size: 20px; font-weight: 700; margin-bottom: 25px; color: #333; }
    .slider-images { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; }
    .slider-card { position: relative; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .slider-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 12px; }
    .delete-btn {
      position: absolute; top: 10px; right: 10px;
      background: rgba(255,0,0,0.8); color: #fff;
      border: none; padding: 6px 10px;
      border-radius: 6px; cursor: pointer;
      font-size: 14px; font-weight: 600;
      transition: background 0.3s ease;
    }
    .delete-btn:hover { background: red; }
    .logo-icon img { width:60px; height:auto; border-radius:50%; margin-right:10px; }
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
            <a href="index.php" class="active">Home</a>
            <a href="rooms.php">Rooms</a>
            <a href="gallery.php">Gallery</a>
            <a href="contact.php">Contact</a>
            <a href="admin.php">admin</a>
        </nav>

        <div class="contact-info">
            <span><i class="fas fa-phone"></i> +91 92659 00219</span>
            <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
            <a href="rooms.php" class="book-btn">Book Now</a>
        </div>
    </header> -->

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

  <!-- Show all uploaded slider images -->
  <div class="slider-gallery">
    <h3>All Uploaded Slider Images</h3>
    <div class="slider-images">
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="slider-card">
          <img src="<?= $row['background_image'] ?>" alt="Slider Image">
          <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this image?')">
            <button type="button" class="delete-btn">Delete</button>
          </a>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
