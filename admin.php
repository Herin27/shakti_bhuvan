<?php
// Database connection
// $conn = new mysqli("localhost", "root", "", "shakti_bhuvan");
include 'db.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bg_image'])) {
    $fileName = $_FILES['bg_image']['name'];
    $fileTmp = $_FILES['bg_image']['tmp_name'];
    $uploadDir = "uploads/";
    $uploadPath = $uploadDir . basename($fileName);

    // Create uploads folder if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        // Save to DB (replace previous image)
        $conn->query("TRUNCATE hero_section");
        $stmt = $conn->prepare("INSERT INTO hero_section (background_image) VALUES (?)");
        $stmt->bind_param("s", $uploadPath);
        $stmt->execute();
        echo "<p style='color:green;'>Background updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>Image upload failed!</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Upload Background Image:</label><br><br>
    <input type="file" name="bg_image" required>
    <br><br>
    <button type="submit">Save</button>
</form>
