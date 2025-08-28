<?php
include 'db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $size = $_POST['size'];
    $bed_type = $_POST['bed_type'];
    $guests = $_POST['guests'];
    $rating = $_POST['rating'];
    $reviews = $_POST['reviews'];

    // ✅ Convert selected checkboxes into comma-separated string
    $amenities = isset($_POST['amenities']) ? implode(',', $_POST['amenities']) : '';
    $features  = isset($_POST['features']) ? implode(',', $_POST['features']) : '';
    $policies  = isset($_POST['policies']) ? implode(',', $_POST['policies']) : '';

    // Image upload
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);

    $sql = "INSERT INTO rooms 
        (name, description, price, discount_price, size, bed_type, guests, rating, reviews, image, amenities, features, policies) 
        VALUES 
        ('$name','$description','$price','$discount_price','$size','$bed_type','$guests','$rating','$reviews','$image','$amenities','$features','$policies')";
    
    mysqli_query($conn,$sql);
    echo "Room Added Successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link rel="stylesheet" href="./assets/css/add_rooms.css">
</head>
<body>
<form method="post" enctype="multipart/form-data">
    <h2>Add New Room</h2>
    <input type="text" name="name" placeholder="Room Name"><br>
    <textarea name="description" placeholder="Description"></textarea><br>
    <input type="number" name="price" placeholder="Price"><br>
    <input type="number" name="discount_price" placeholder="Discount Price"><br>
    <input type="text" name="size" placeholder="Room Size"><br>
    <input type="text" name="bed_type" placeholder="Bed Type"><br>
    <input type="text" name="guests" placeholder="Guests"><br>
    <input type="text" name="rating" placeholder="Rating"><br>
    <input type="text" name="reviews" placeholder="Reviews"><br>
    <input type="file" name="image"><br><br>

    <!-- Amenities -->
    <h3>Amenities</h3>
    <label><input type="checkbox" name="amenities[]" value="Free Wi-Fi"> Free Wi-Fi</label><br>
    <label><input type="checkbox" name="amenities[]" value="AC"> AC</label><br>
    <label><input type="checkbox" name="amenities[]" value="Room Service"> Room Service</label><br>
    <label><input type="checkbox" name="amenities[]" value="TV"> TV</label><br>
    <label><input type="checkbox" name="amenities[]" value="Mini Bar"> Mini Bar</label><br>
    <label><input type="checkbox" name="amenities[]" value="Parking"> Parking</label><br>
    <label><input type="checkbox" name="amenities[]" value="Swimming Pool"> Swimming Pool</label><br>
    <label><input type="checkbox" name="amenities[]" value="Gym"> Gym</label><br>

    <!-- Features -->
    <h3>Features</h3>
    <label><input type="checkbox" name="features[]" value="Sea View"> Sea View</label><br>
    <label><input type="checkbox" name="features[]" value="Balcony"> Balcony</label><br>
    <label><input type="checkbox" name="features[]" value="Jacuzzi"> Jacuzzi</label><br>
    <label><input type="checkbox" name="features[]" value="Smart TV"> Smart TV</label><br>
    <label><input type="checkbox" name="features[]" value="Work Desk"> Work Desk</label><br>

    <!-- Policies -->
    <h3>Policies</h3>
    <label><input type="checkbox" name="policies[]" value="No Smoking"> No Smoking</label><br>
    <label><input type="checkbox" name="policies[]" value="Pet Friendly"> Pet Friendly</label><br>
    <label><input type="checkbox" name="policies[]" value="Free Cancellation"> Free Cancellation</label><br>
    <label><input type="checkbox" name="policies[]" value="Check-in after 12 PM"> Check-in after 12 PM</label><br>
    <label><input type="checkbox" name="policies[]" value="Check-out before 11 AM"> Check-out before 11 AM</label><br>

    <br>
    <button type="submit">Add Room</button>
</form>
</body>
</html>
