<?php
// update_hero.php
include 'db.php'; 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$redirect_url = 'admin_dashboard.php?section=settings-section';
$upload_dir = 'uploads/';

// --- Function to sanitize input ---
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}

// =======================================================
// A) Handle Image Deletion
// =======================================================
if (isset($_POST['delete_image'])) {
    $image_id = intval($_POST['delete_image']);

    // 1. Fetch the image path from the database
    $sql_fetch = "SELECT background_image FROM hero_section WHERE id = $image_id";
    $result_fetch = mysqli_query($conn, $sql_fetch);

    if ($result_fetch && $row = mysqli_fetch_assoc($result_fetch)) {
        $image_path = $row['background_image'];

        // 2. Delete the record from the database
        $sql_delete_db = "DELETE FROM hero_section WHERE id = $image_id";
        
        if (mysqli_query($conn, $sql_delete_db)) {
            
            // 3. Delete the physical file from the server
            if (file_exists($image_path) && !is_dir($image_path)) {
                @unlink($image_path); 
            }

            $message = "Hero image deleted successfully.";
            header("Location: $redirect_url&status=success&msg=" . urlencode($message));
            exit();
        } else {
            $message = "Error deleting image from database: " . mysqli_error($conn);
        }
    } else {
        $message = "Error: Image not found in database.";
    }

    header("Location: $redirect_url&status=error&msg=" . urlencode($message));
    exit();
}


// =======================================================
// B) Handle New Image Upload
// =======================================================
if (isset($_POST['add_image']) && !empty($_FILES['new_image']['name'])) {
    
    $file = $_FILES['new_image'];
    $tmp_name = $file['tmp_name'];
    $error = $file['error'];
    $file_name = $file['name'];

    if ($error === UPLOAD_ERR_OK) {
        // Ensure the uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate a unique filename and set the target path
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_filename = time() . "_" . uniqid() . "." . $file_ext;
        $target = $upload_dir . $unique_filename; 

        if (move_uploaded_file($tmp_name, $target)) {
            // Save the full relative path into the database
            $image_path_db = sanitize_input($conn, $target);

            $sql_insert = "INSERT INTO hero_section (background_image) VALUES ('$image_path_db')";
            
            if (mysqli_query($conn, $sql_insert)) {
                $message = "New hero image uploaded and added successfully.";
            } else {
                // If DB insertion fails, delete the file just uploaded
                @unlink($target);
                $message = "Error inserting image path into database: " . mysqli_error($conn);
            }
        } else {
            $message = "Error moving uploaded file.";
        }
    } else {
        $message = "File upload failed with error code: " . $error;
    }

    header("Location: $redirect_url&status=" . ($message ? 'error' : 'success') . "&msg=" . urlencode($message));
    exit();
}

// Redirect back if accessed directly without submission
header("Location: $redirect_url");
exit();

?>