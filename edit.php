<?php
// Include the database connection file using MySQLi
include 'db.php'; 

$room_id = null;
$room = null;
$message = '';
$message_type = '';

// Check if a numerical ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $room_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // --- 1. Fetch Current Room Data ---
    $sql_fetch = "SELECT * FROM rooms WHERE id = '$room_id'";
    $result_fetch = mysqli_query($conn, $sql_fetch);
    
    if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
        $room = mysqli_fetch_assoc($result_fetch);
        // Split image string into an array for display
        $current_images = $room['image'] ? explode(',', $room['image']) : [];
    } else {
        $message = "Error: Room ID '$room_id' not found.";
        $message_type = 'danger';
        $room_id = null; // Clear ID to prevent form display
    }
} else {
    $message = "Error: Invalid room ID provided.";
    $message_type = 'danger';
}

// --- 2. Handle Form Submission (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $room_id) {
    
    // Sanitize and validate basic input fields
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $discount_price = mysqli_real_escape_string($conn, $_POST['discount_price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $bed_type = mysqli_real_escape_string($conn, $_POST['bed_type']);
    $guests = mysqli_real_escape_string($conn, $_POST['guests']);
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);
    $features = mysqli_real_escape_string($conn, $_POST['features']);
    $policies = mysqli_real_escape_string($conn, $_POST['policies']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Handle image updates and deletions
    $existing_images_to_keep = [];
    if (isset($_POST['existing_images'])) {
        $existing_images_to_keep = $_POST['existing_images'];
    }

    $new_images = [];
    $upload_dir = 'uploads/';
    
    // Process new file uploads
    if (!empty($_FILES['new_images']['name'][0])) {
        foreach ($_FILES['new_images']['name'] as $key => $filename) {
            $tmp_name = $_FILES['new_images']['tmp_name'][$key];
            $error = $_FILES['new_images']['error'][$key];
            
            if ($error === UPLOAD_ERR_OK) {
                // Generate a unique name for the uploaded file
                $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
                $target_file = $upload_dir . $new_filename;
                
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $new_images[] = $target_file;
                }
            }
        }
    }
    
    // Combine existing images to keep and newly uploaded images
    $final_images_array = array_merge($existing_images_to_keep, $new_images);
    $final_images_string = mysqli_real_escape_string($conn, implode(',', $final_images_array));

    // Cleanup: Delete images that were removed by the user
    $images_to_delete = array_diff($current_images, $existing_images_to_keep);
    foreach ($images_to_delete as $image_path) {
        if (file_exists($image_path) && !is_dir($image_path)) {
            // Uncomment the line below in production to physically delete the file:
            // unlink($image_path);
            error_log("Simulated deletion of file: " . $image_path);
        }
    }

    // --- 3. Execute UPDATE Query ---
    $sql_update = "
        UPDATE rooms SET 
            name = '$name',
            price = '$price',
            discount_price = '$discount_price',
            description = '$description',
            size = '$size',
            bed_type = '$bed_type',
            guests = '$guests',
            amenities = '$amenities',
            features = '$features',
            policies = '$policies',
            status = '$status',
            image = '$final_images_string'
        WHERE id = '$room_id'
    ";

    if (mysqli_query($conn, $sql_update)) {
        $message = "Room **$name** updated successfully!";
        $message_type = 'success';
        // Re-fetch updated data to refresh the form
        header("Location: edit.php?id=$room_id&status=success&msg=" . urlencode($message));
        exit();
    } else {
        $message = "Error updating record: " . mysqli_error($conn);
        $message_type = 'danger';
    }
}

// Check for redirection messages after a successful update
if (isset($_GET['status']) && $_GET['status'] === 'success' && isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
    $message_type = 'success';
    // Re-fetch data after redirect
    $sql_fetch = "SELECT * FROM rooms WHERE id = '$room_id'";
    $result_fetch = mysqli_query($conn, $sql_fetch);
    if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
        $room = mysqli_fetch_assoc($result_fetch);
        $current_images = $room['image'] ? explode(',', $room['image']) : [];
    }
}

// Close the MySQLi connection
mysqli_close($conn);

// Helper function for amenity visualization (simple example)
function getAmenityIcon(string $amenity): string {
    $amenity = strtolower(trim($amenity));
    if (strpos($amenity, 'wi-fi') !== false) return '<i class="fas fa-wifi me-1 text-primary"></i>';
    if (strpos($amenity, 'jacuzzi') !== false) return '<i class="fas fa-hot-tub me-1 text-info"></i>';
    if (strpos($amenity, 'pet friendly') !== false) return '<i class="fas fa-paw me-1 text-success"></i>';
    if (strpos($amenity, 'ac') !== false) return '<i class="fas fa-snowflake me-1 text-info"></i>';
    return '<i class="fas fa-check me-1 text-muted"></i>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room: <?php echo $room ? htmlspecialchars($room['name']) : 'N/A'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; margin-top: 30px; margin-bottom: 50px; }
        .card { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
        .image-preview-container { 
            position: relative; 
            display: inline-block; 
            margin: 10px; 
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            background-color: #fff;
        }
        .image-preview-container img { 
            width: 150px; 
            height: 100px; 
            object-fit: cover; 
            display: block;
        }
        .image-delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="mb-4">
            <a href="admin_dashboard.php" class="text-secondary me-2"><i class="fas fa-arrow-left"></i></a>
            Edit Room Details
        </h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($room): ?>
        <form method="POST" action="edit.php?id=<?php echo $room_id; ?>" enctype="multipart/form-data">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

            <div class="card p-4 mb-4">
                <h4 class="card-title">Basic Information</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Room Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($room['name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="Available" <?php echo ($room['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                            <option value="Occupied" <?php echo ($room['status'] == 'Occupied') ? 'selected' : ''; ?>>Occupied</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($room['description']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <h4 class="card-title">Pricing & Capacity</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label">Price/Night (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($room['price']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="discount_price" class="form-label">Discount Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="discount_price" name="discount_price" value="<?php echo htmlspecialchars($room['discount_price']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="guests" class="form-label">Max Guests</label>
                        <input type="number" class="form-control" id="guests" name="guests" value="<?php echo htmlspecialchars($room['guests']); ?>" required>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="size" class="form-label">Room Size (e.g., 350 sq ft)</label>
                        <input type="text" class="form-control" id="size" name="size" value="<?php echo htmlspecialchars($room['size']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="bed_type" class="form-label">Bed Type (e.g., King, 2 Doubles)</label>
                        <input type="text" class="form-control" id="bed_type" name="bed_type" value="<?php echo htmlspecialchars($room['bed_type']); ?>">
                    </div>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <h4 class="card-title">Amenities and Features (Comma Separated)</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="amenities" class="form-label">Amenities</label>
                        <input type="text" class="form-control" id="amenities" name="amenities" value="<?php echo htmlspecialchars($room['amenities']); ?>">
                        <small class="text-muted">Example: Free Wi-Fi, AC, Minibar</small>
                    </div>
                    <div class="col-12">
                        <label for="features" class="form-label">Special Features</label>
                        <input type="text" class="form-control" id="features" name="features" value="<?php echo htmlspecialchars($room['features']); ?>">
                        <small class="text-muted">Example: Jacuzzi, Balcony, Sea View</small>
                    </div>
                    <div class="col-12">
                        <label for="policies" class="form-label">Policies</label>
                        <textarea class="form-control" id="policies" name="policies" rows="2"><?php echo htmlspecialchars($room['policies']); ?></textarea>
                        <small class="text-muted">Example: Pet Friendly, Non-smoking</small>
                    </div>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <h4 class="card-title">Room Images</h4>
                
                <p class="mb-2">Current Images (Uncheck to delete):</p>
                <div id="current-images-list" class="d-flex flex-wrap mb-3">
                    <?php 
                    if (!empty($current_images)):
                        foreach ($current_images as $image_path):
                            if (!empty($image_path)):
                    ?>
                    <div class="image-preview-container">
                        <input type="checkbox" name="existing_images[]" value="<?php echo htmlspecialchars($image_path); ?>" 
                               style="display: none;" id="img-<?php echo basename($image_path); ?>" checked>
                        
                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Room Image">
                        
                        <button type="button" class="image-delete-btn" 
                                onclick="document.getElementById('img-<?php echo basename($image_path); ?>').checked = false; this.closest('.image-preview-container').style.opacity = '0.4';">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <?php
                            endif;
                        endforeach;
                    else:
                        echo '<p class="text-muted">No images currently uploaded.</p>';
                    endif;
                    ?>
                </div>

                <label for="new_images" class="form-label mt-3">Upload New Images (Select multiple files)</label>
                <input type="file" class="form-control" id="new_images" name="new_images[]" multiple accept="image/*">
                <small class="text-muted">New images will be added to the existing ones unless you delete the old ones above.</small>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100" style="background-color: #a0522d; border-color: #a0522d;">
                <i class="fas fa-save me-2"></i> Save Changes
            </button>
        </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>