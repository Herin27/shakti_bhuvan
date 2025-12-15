<?php
// update_contact.php
include 'db.php'; 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$redirect_url = 'admin_dashboard.php?section=settings-section';
$message = '';
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Define the expected fields and their corresponding setting keys
    $settings_map = [
        'phone' => 'phone_number',
        'email' => 'email_address',
        'address' => 'physical_address'
    ];

    mysqli_begin_transaction($conn);

    try {
        foreach ($settings_map as $post_key => $db_key) {
            if (isset($_POST[$post_key])) {
                $value = mysqli_real_escape_string($conn, $_POST[$post_key]);
                $key = mysqli_real_escape_string($conn, $db_key);

                // Use INSERT...ON DUPLICATE KEY UPDATE to handle existing or new settings efficiently
                $sql = "
                    INSERT INTO site_settings (setting_key, setting_value)
                    VALUES ('$key', '$value')
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ";

                if (!mysqli_query($conn, $sql)) {
                    throw new Exception("Database update failed for key: $db_key. Error: " . mysqli_error($conn));
                }
            }
        }
        
        // If loop completes successfully, commit the transaction
        mysqli_commit($conn);
        $message = "Contact information updated successfully!";

    } catch (Exception $e) {
        // Rollback on any failure
        mysqli_rollback($conn);
        $error = true;
        $message = "Update failed! " . $e->getMessage();
    }
    
    mysqli_close($conn);

    // Redirect back to the settings page
    $status = $error ? 'error' : 'success';
    header("Location: $redirect_url&status=$status&msg=" . urlencode($message));
    exit();
}

// If accessed directly without POST data
header("Location: $redirect_url");
exit();
?>