<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Add Gallery Images</title>
  <link rel="stylesheet" href="./assets/css/navbar.css"> <!-- navbar css -->
  <link rel="icon" href="assets/images/logo.jpg" type="image/x-icon">
  <style>
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
    /* Fix navbar overlap if it's fixed */
    header.navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
    /* Scoped Admin Page Styles */
    .admin-page {
        font-family: Arial, sans-serif;
        background: #f4f4f9;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .admin-page main {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    .admin-page .container {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        width: 100%;
        max-width: 500px;
        text-align: center;
    }

    .admin-page h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .admin-page form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .admin-page label {
        text-align: left;
        font-weight: bold;
        color: #444;
        margin-bottom: 5px;
        display: block;
    }

    .admin-page select,
    .admin-page input[type="file"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        width: 100%;
    }

    .admin-page button {
        padding: 12px;
        border: none;
        border-radius: 6px;
        background: #c4a36f;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .admin-page button:hover {
        background: #a57e3d;
    }
  </style>
</head>
<body class="admin-page">

<!-- HEADER -->
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
        <a href="gallery.php">Gallery</a>
        <a href="contact.php">Contact</a>
        <a href="admin_gallery.php" class="active">Admin</a>
    </nav>

    <div class="contact-info">
        <span><i class="fas fa-phone"></i> +91 98765 43210</span>
        <span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
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

          <button type="submit">Upload</button>
      </form>
  </div>
</main>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

</body>
</html>
