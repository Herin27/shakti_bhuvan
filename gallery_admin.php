<?php
// gallery_admin.php
include 'db.php'; 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';
$redirect_url = 'admin_dashboard.php?section=gallery-section';

// --- Function to sanitize input ---
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}

// =======================================================
// 1. Handle Image Upload (POST: upload)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $type = sanitize_input($conn, $_POST['image_type']);
    $targetDir = "uploads/";
    $uploaded_count = 0;

    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $error = $_FILES['images']['error'][$key];
            
            if ($error === UPLOAD_ERR_OK) {
                // Generate unique filename
                $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                $unique_filename = time() . "_" . uniqid() . "." . $file_ext;
                $targetFilePath = $targetDir . $unique_filename;
                
                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $image_path_db = sanitize_input($conn, $targetFilePath);

                    $stmt = $conn->prepare("INSERT INTO gallery (image_url, image_type) VALUES (?, ?)");
                    $stmt->bind_param("ss", $image_path_db, $type);
                    
                    if ($stmt->execute()) {
                        $uploaded_count++;
                    } else {
                        // Log database error and unlink file if DB fails
                        @unlink($targetFilePath);
                        $message_type = 'warning';
                        $message = "Database error inserting $name.";
                    }
                    $stmt->close();
                } else {
                    $message_type = 'warning';
                    $message = "Error moving uploaded file $name.";
                }
            }
        }
        
        if ($uploaded_count > 0) {
            $message_type = 'success';
            $message = "$uploaded_count image(s) uploaded and added to the gallery successfully!";
        }
    } else {
        $message_type = 'warning';
        $message = "No files selected for upload.";
    }

    // Redirect after operation to prevent form resubmission
    header("Location: $redirect_url&status=$message_type&msg=" . urlencode($message));
    exit();
}

// =======================================================
// 2. Handle Image Delete (POST: delete)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $image_id = intval($_POST['image_id']);
    
    // 1. Get image path
    $stmt = $conn->prepare("SELECT image_url FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    $error = false;
    
    if ($image_url) {
        // 2. Delete from database
        $stmt_delete_db = $conn->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt_delete_db->bind_param("i", $image_id);
        
        if ($stmt_delete_db->execute()) {
            // 3. Delete file from server
            if (file_exists($image_url) && !is_dir($image_url)) {
                @unlink($image_url);
            }
            $message = "Image ID $image_id deleted successfully.";
            $message_type = 'success';
        } else {
            $error = true;
            $message = "Error deleting from database: " . $stmt_delete_db->error;
        }
        $stmt_delete_db->close();
    } else {
        $error = true;
        $message = "Image ID $image_id not found.";
    }
    
    $status = $error ? 'error' : 'success';
    header("Location: $redirect_url&status=$status&msg=" . urlencode($message));
    exit();
}

// =======================================================
// 3. Fetch all images (for display)
// =======================================================
$result = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
$images = $result->fetch_all(MYSQLI_ASSOC);

// Check for redirection messages from previous operations
if (isset($_GET['status']) && isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
    $message_type = htmlspecialchars($_GET['status']);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Gallery Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    <style>
    body {
        font-family: 'Open Sans', sans-serif;
        background: #f4f4f9;
    }

    .container {
        padding-top: 30px;
        max-width: 900px;
        margin: auto;
    }

    .card {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
    }

    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .gallery-item {
        background: #fcfcfc;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .gallery-item img {
        max-width: 100%;
        border-radius: 6px;
        height: 120px;
        object-fit: cover;
    }

    .delete-btn {
        background: #e74c3c;
        color: white;
        margin-top: 5px;
    }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="mb-4">
            <a href="admin_dashboard.php?section=gallery-section" class="text-secondary me-2"><i
                    class="fas fa-arrow-left"></i></a>
            Gallery Management
        </h1>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card p-4 mb-4">
            <h2>Upload New Images</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="image_type" class="form-label">Image Category:</label>
                        <select name="image_type" id="image_type" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <option value="Hotel View">Hotel View</option>
                            <option value="Standard Room">Standard Non-AC</option>
                            <option value="Deluxe Room">Deluxe Room</option>
                            <option value="Standard Room">Standard Room</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="images" class="form-label">Select Images (Multiple):</label>
                        <input type="file" name="images[]" id="images" class="form-control" multiple required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="upload" class="btn btn-warning w-100"><i
                                class="fas fa-upload me-2"></i> Upload</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card p-4">
            <h2>Uploaded Images (<?php echo count($images); ?>)</h2>
            <div class="gallery">
                <?php if (!empty($images)): ?>
                <?php foreach ($images as $img): ?>
                <div class="gallery-item">
                    <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="Gallery Image">
                    <p class="small text-muted mb-1"><?php echo htmlspecialchars($img['image_type']); ?></p>

                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this image?');">
                        <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm w-100 delete-btn"><i
                                class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted text-center w-100">No images have been uploaded to the gallery yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>