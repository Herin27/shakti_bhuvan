<?php
include 'db.php';

// ✅ Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $type = $_POST['image_type'];

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . "_" . basename($name);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFilePath)) {
                $stmt = $conn->prepare("INSERT INTO gallery (image_url, image_type) VALUES (?, ?)");
                $stmt->bind_param("ss", $targetFilePath, $type);
                $stmt->execute();
            }
        }
    }
}

// ✅ Handle image delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $image_id = intval($_POST['image_id']);

    // Get image path
    $stmt = $conn->prepare("SELECT image_url FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    // Delete file from server
    if ($image_url && file_exists($image_url)) {
        unlink($image_url);
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
}

// ✅ Fetch all images
$result = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
$images = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Add Gallery Images</title>
  <link rel="stylesheet" href="./assets/css/navbar.css">
  <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
  <style>
    html, body { height: 100%; margin: 0; }
    body { font-family: Arial, sans-serif; background: #f4f4f9; }

    main { padding-top: 120px; max-width: 1200px; margin: auto; }

    .container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); }

    h2 { margin-bottom: 20px; color: #333; }

    form { margin-bottom: 40px; }

    label { display: block; font-weight: bold; margin-bottom: 5px; }

    select, input[type="file"] {
        padding: 10px; border: 1px solid #ccc; border-radius: 6px; width: 100%;
    }

    button { padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; }
    .upload-btn { background: #c4a36f; color: white; margin-top: 10px; }
    .upload-btn:hover { background: #a57e3d; }
    .delete-btn { background: #e74c3c; color: white; margin-top: 5px; }
    .delete-btn:hover { background: #c0392b; }

    .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
    .gallery-item { background: #fff; padding: 10px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
    .gallery-item img { max-width: 100%; border-radius: 8px; height: 150px; object-fit: cover; }
    .gallery-item p { margin: 10px 0; font-weight: bold; }

    .contact-info a {
    color: inherit;
    text-decoration: none;
    }

    .contact-info a:hover {
    text-decoration: none;
    }
  </style>
</head>
<body class="admin-page">

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
            <a href="index.php" class="active">Home</a>
            <a href="rooms.php">Rooms</a>
            <a href="gallery.php">Gallery</a>
            <a href="contact.php">Contact</a>
            <a href="admin.php">admin</a>
        </nav>

        <div class="contact-info">
        <span>
            <i class="fas fa-phone"></i>
            <a href="tel:+919265900219">+91 92659 00219</a>
        </span>

        <span>
            <i class="fas fa-envelope"></i>
            <a href="mailto:shaktibhuvanambaji.com">shaktibhuvanambaji.com</a>
        </span>

        <a href="rooms.php" class="book-btn">Book Now</a>
        </div>
    </header>

<main>
  <div class="container">
      <h2>Add Gallery Images</h2>
      <form method="post" enctype="multipart/form-data">
          <div>
              <label for="image_type">Image Category:</label>
              <select name="image_type" id="image_type" required>
                  <option value="">-- Select Category --</option>
                  <option value="Hotel View">Hotel View</option>
                  <option value="Luxury Suite">Luxury Suite</option>
                  <option value="Deluxe Room">Deluxe Room</option>
                  <option value="Standard Room">Standard Room</option>
              </select>
          </div>

          <div>
              <label for="images">Select Images:</label>
              <input type="file" name="images[]" id="images" multiple required>
          </div>

          <button type="submit" name="upload" class="upload-btn">Upload</button>
      </form>

      <h2>Uploaded Images</h2>
      <div class="gallery">
          <?php foreach ($images as $img): ?>
              <div class="gallery-item">
                  <img src="<?php echo $img['image_url']; ?>" alt="Gallery Image">
                  <p><?php echo htmlspecialchars($img['image_type']); ?></p>
                  <form method="post" onsubmit="return confirm('Are you sure you want to delete this image?');">
                      <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                      <button type="submit" name="delete" class="delete-btn">Delete</button>
                  </form>
              </div>
          <?php endforeach; ?>
      </div>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>