<?php
include 'db.php';
// include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // --- Data Collection ---
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $size = $_POST['size'];
    $bed_type = $_POST['bed_type'];
    $guests = $_POST['guests'];
    $rating = $_POST['rating'];
    $reviews = $_POST['reviews'];
    
    // --- NEW FIELDS ---
    $floor = $_POST['floor'];
    $extra_bed_price = $_POST['extra_bed_price'];
    $ac_status = $_POST['ac_status'];
    $room_numbers_list = $_POST['room_numbers_list']; // New: Comma-separated room numbers
    // --------------------

    // Convert array inputs to comma-separated strings
    $amenities = isset($_POST['amenities']) ? implode(',', $_POST['amenities']) : '';
    $features  = isset($_POST['features']) ? implode(',', $_POST['features']) : '';
    $policies  = isset($_POST['policies']) ? implode(',', $_POST['policies']) : '';
    $max_extra_beds = isset($_POST['max_extra_beds']) ? (int)$_POST['max_extra_beds'] : 0;

    // --- Image Upload ---
    $uploaded_images = [];
    if (!empty($_FILES['images']['name'][0])) {
        // Create the 'uploads' directory if it doesn't exist (good practice)
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        foreach ($_FILES['images']['name'] as $key => $val) {
            // Sanitize and create a unique file name
            $file_extension = pathinfo($val, PATHINFO_EXTENSION);
            $safe_file_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($val, "." . $file_extension));
            $file_name = time() . "_" . $key . "_" . $safe_file_name . "." . $file_extension;
            $target = "uploads/" . $file_name;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                $uploaded_images[] = $file_name;
            } else {
                // Optional: Error logging for failed upload
                // echo "Failed to upload file: " . $val . "<br>";
            }
        }
    }
    $images_str = implode(',', $uploaded_images);
    
    // --- SQL Insertion for Room Type ---
    
    // Sanitize string inputs
    $name = mysqli_real_escape_string($conn, $name);
    $description = mysqli_real_escape_string($conn, $description);
    $size = mysqli_real_escape_string($conn, $size);
    $bed_type = mysqli_real_escape_string($conn, $bed_type);
    $guests = mysqli_real_escape_string($conn, $guests);
    $floor = mysqli_real_escape_string($conn, $floor);
    $ac_status = mysqli_real_escape_string($conn, $ac_status);
    
    // Numeric inputs
    $price = (float)$price;
    $discount_price = (float)$discount_price;
    $extra_bed_price = (float)$extra_bed_price;
    $rating = (float)$rating;
    $reviews = (int)$reviews;

    // INSERT main room type data
    // --- Data Collection --- (આ વિભાગમાં લાઈન ૨૨ આસપાસ ઉમેરો)

// --- SQL Insertion --- (તમારી SQL ક્વેરી આ રીતે અપડેટ કરો)
$sql_room = "INSERT INTO rooms 
    (name, description, price, discount_price, size, bed_type, guests, rating, reviews, image, amenities, features, policies, floor, extra_bed_price, ac_status, max_extra_beds) 
    VALUES 
    ('$name', '$description', '$price', '$discount_price', '$size', '$bed_type', '$guests', '$rating', '$reviews', '$images_str', '$amenities', '$features', '$policies', '$floor', '$extra_bed_price', '$ac_status', '$max_extra_beds')";
    
    if (mysqli_query($conn, $sql_room)) {
        $room_id = mysqli_insert_id($conn); // Get the ID of the newly inserted room type
        $success = true;

        // --- SQL Insertion for Room Numbers ---
        $room_numbers_array = array_map('trim', explode(',', $room_numbers_list));
        $room_numbers_array = array_filter($room_numbers_array); // Remove empty values

        $room_insert_count = 0;
        // Loop ની અંદર (લાઈન ૯૦ આસપાસ)
foreach ($room_numbers_array as $room_num) {
    $safe_room_num = mysqli_real_escape_string($conn, $room_num);
    
    // ચેક કરો કે આ ફ્લોર પર આ રૂમ નંબર પહેલેથી છે કે નહીં
    $check_exists = mysqli_query($conn, "SELECT id FROM room_numbers WHERE room_number = '$safe_room_num' AND floor = '$floor'");
    
    if (mysqli_num_rows($check_exists) == 0) {
        $sql_num = "INSERT INTO room_numbers (room_type_id, floor, room_number, status) 
                    VALUES ('$room_id', '$floor', '$safe_room_num', 'Available')";
        if (mysqli_query($conn, $sql_num)) {
            $room_insert_count++;
        }
    } else {
        // જો રૂમ પહેલેથી હોય તો અહીં મેસેજ સેટ કરી શકાય
    }
}
        
        echo "<script>alert('Room Type Added Successfully! " . $room_insert_count . " physical rooms added.');</script>";
        // Optional: Redirect
        // header("Location: admin_dashboard.php");
        // exit;

    } else {
        echo "<script>alert('Error adding room type: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room - Shakti Bhuvan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/view_details.css">
    <style>
    /* New style to handle the back button and title layout */
    .header-section {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-section .room-title {
        margin-bottom: 0;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        padding: 8px 15px;
        margin-right: 20px;
        background-color: #f7f7f7;
        color: #333;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.2s, box-shadow 0.2s;
        border: 1px solid #ddd;
    }

    .back-btn:hover {
        background-color: #e6e6e6;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .back-btn svg {
        margin-right: 8px;
        width: 20px;
        height: 20px;
        fill: currentColor;
    }


    /* Existing styles from your provided code */
    .logo-icon img {
        width: 60px;
        height: auto;
        border-radius: 50%;
        margin-right: 10px;
    }

    /* extra tweaks for form only */
    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 15px;
        display: flow;
    }

    .form-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        max-width: 1200px;
        width: 100%;
        margin: auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #0a7d5f;
        box-shadow: 0 0 0 2px rgba(10, 125, 95, 0.15);
    }

    .checkbox-group,
    .radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 8px;
        margin: 10px 0 20px;
    }

    .checkbox-group label,
    .radio-group label {
        background: #f7f7f7;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .checkbox-group input,
    .radio-group input {
        margin-right: 6px;
    }

    .submit-btn {
        background: #0a7d5f;
        color: #fff;
        font-weight: 600;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 16px;
        transition: 0.3s;
        display: block;
        margin: 20px auto 0;
    }

    .submit-btn:hover {
        background: #05684c;
    }
    </style>
</head>

<body>

    <div class="container">

        <div class="header-section">
            <a href="admin_dashboard.php" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M12.707 17.293l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L9.414 12l4.707 4.707a1 1 0 01-1.414 1.414z" />
                </svg>
                Back
            </a>
            <h1 class="room-title">Add New Room Type</h1>

        </div>
        <p class="desc">Fill in the details below to add a new room type to Shakti Bhuvan.</p><br>

        <div class="form-card">
            <form method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Room Name (Type)</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>Default Floor for this Type</label>
                        <select name="floor" required>
                            <option value="">Select Floor</option>
                            <option value="Ground Floor">Ground Floor</option>
                            <option value="1st Floor">1st Floor</option>
                            <option value="2nd Floor">2nd Floor</option>
                            <option value="3rd Floor">3rd Floor</option>
                            <option value="4th Floor">4th Floor</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Total Price</label>
                        <input type="number" name="price" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Discount Price </label>
                        <input type="number" name="discount_price" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>Extra Bed Price</label>
                        <input type="number" name="extra_bed_price" step="0.01" value="0.00">
                    </div>

                    <div class="form-group">
                        <label>No. of Extra Beds Allowed</label>
                        <input type="number" name="max_extra_beds" min="0" value="0">
                        <small>Maximum number of extra beds allowed.</small>
                    </div>

                    <div class="form-group">
                        <label>Room Size</label>
                        <input type="text" name="size">
                    </div>
                    <div class="form-group">
                        <label>Bed Type</label>
                        <input type="text" name="bed_type">
                    </div>
                    <div class="form-group">
                        <label>Guests</label>
                        <input type="number" name="guests" min="1">
                    </div>
                    <div class="form-group">
                        <label>Rating</label>
                        <input type="number" name="rating" step="0.1" min="0" max="5">
                    </div>
                    <div class="form-group">
                        <label>Reviews</label>
                        <input type="number" name="reviews" min="0">
                    </div>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Physical Room Numbers (Comma-Separated)</label>
                    <textarea name="room_numbers_list" rows="2" placeholder="Example: 101, 102, 205, 301"></textarea>
                    <small>Enter the individual physical room numbers that correspond to this room type. Each number
                        should be separated by a comma.</small>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>

                <h3>AC Status</h3>
                <div class="radio-group">
                    <label><input type="radio" name="ac_status" value="AC" required> AC Room</label>
                    <label><input type="radio" name="ac_status" value="Non-AC" required> Non-AC Room</label>
                </div>


                <div class="form-group">
                    <label>Upload Room Images</label>
                    <input type="file" name="images[]" multiple accept="image/*">
                    <small>You can select multiple images</small>
                </div>


                <h3>Amenities</h3>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="amenities[]" value="Free Wi-Fi"> Free Wi-Fi</label>
                    <label><input type="checkbox" name="amenities[]" value="AC"> AC</label>
                    <label><input type="checkbox" name="amenities[]" value="Room Service"> Room Service</label>
                    <label><input type="checkbox" name="amenities[]" value="TV"> TV</label>
                    <label><input type="checkbox" name="amenities[]" value="Mini Bar"> Mini Bar</label>
                    <label><input type="checkbox" name="amenities[]" value="Parking"> Parking</label>
                    <label><input type="checkbox" name="amenities[]" value="Swimming Pool"> Swimming Pool</label>
                    <label><input type="checkbox" name="amenities[]" value="Gym"> Gym</label>
                </div>

                <h3>Features</h3>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="features[]" value="Mountain View"> Mountain View</label>
                    <label><input type="checkbox" name="features[]" value="Balcony"> Balcony</label>
                    <label><input type="checkbox" name="features[]" value="Jacuzzi"> Jacuzzi</label>
                    <label><input type="checkbox" name="features[]" value="Smart TV"> Smart TV</label>
                    <label><input type="checkbox" name="features[]" value="Work Desk"> Work Desk</label>
                </div>

                <h3>Policies</h3>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="policies[]" value="No Smoking"> No Smoking</label>
                    <label><input type="checkbox" name="policies[]" value="Pet Friendly"> Pet Friendly</label>
                    <label><input type="checkbox" name="policies[]" value="Free Cancellation"> Free Cancellation</label>
                    <label><input type="checkbox" name="policies[]" value="Check-in after 12 PM"> Check-in after 12
                        PM</label>
                    <label><input type="checkbox" name="policies[]" value="Check-out before 11 AM"> Check-out before 11
                        AM</label>
                </div>

                <button type="submit" class="submit-btn">➕ Add Room Type & Physical Rooms</button>
            </form>
        </div>
    </div>

    <?php
include 'footer.php';
?>

</body>

</html>