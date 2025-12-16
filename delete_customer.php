<?php
// delete_customer.php

// Include the database connection file
include 'db.php'; 

// Check for a valid database connection immediately
if (!isset($conn) || $conn->connect_error) {
    $message = "Database connection failed. Check your db.php file.";
    $redirect_url = 'admin_dashboard.php?section=customers-section';
    header("Location: $redirect_url&alert_type=error&msg=" . urlencode($message));
    exit();
}

// Function to safely sanitize input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, $data); 
}

// Fetch the customer_id (which is a string, e.g., 'CUST1322')
$customer_id = isset($_GET['id']) ? sanitize_input($conn, $_GET['id']) : '';

$message = '';
$redirect_url = 'admin_dashboard.php?section=customers-section';
$transaction_successful = false;
$alert_type = 'error'; 

// 1. Basic Validation Check
// We ONLY check if the ID string is empty, ignoring the 'type' parameter which was causing the failure.
if (empty($customer_id)) {
    $message = "Invalid request or insufficient customer ID provided for deletion.";
    $alert_type = 'danger';
    header("Location: $redirect_url&alert_type=$alert_type&msg=" . urlencode($message));
    exit();
}

// --- Start Deletion Process using a Transaction ---
mysqli_begin_transaction($conn);

try {
    // NOTE: Your database schema does NOT link 'bookings' to 'users' via a Foreign Key, 
    // so we can delete the user directly. If you add that FK later, you must delete related bookings FIRST.
    
    // 2. Delete the customer record from the 'users' table
    $sql_delete_customer = "DELETE FROM users WHERE customer_id = '$customer_id'";
    
    if (!mysqli_query($conn, $sql_delete_customer)) {
        throw new Exception("Error deleting customer record: " . mysqli_error($conn));
    }
    
    $rows_deleted = mysqli_affected_rows($conn);
    
    if ($rows_deleted === 0) {
        // If query ran but 0 rows were deleted, the ID likely didn't exist
        throw new Exception("No customer found with ID: $customer_id. Deletion failed.");
    }

    // If successful, commit the transaction.
    mysqli_commit($conn);
    $transaction_successful = true;
    $alert_type = 'success';
    $message = "Customer ID $customer_id deleted successfully.";
    
} catch (Exception $e) {
    // If any error occurred, rollback the transaction
    mysqli_rollback($conn);
    $message = "Deletion failed (Rollback): " . $e->getMessage();
    $alert_type = 'danger';
}

mysqli_close($conn);

// 4. Redirect with the result message
if ($transaction_successful) {
    header("Location: $redirect_url&alert_type=$alert_type&msg=" . urlencode($message));
} else {
    header("Location: $redirect_url&alert_type=$alert_type&msg=" . urlencode($message));
}
exit();
?>