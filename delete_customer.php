<?php
// Include the database connection file
include 'db.php'; 

// Function to safely sanitize input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, $data); 
}

$customer_id = isset($_GET['id']) ? sanitize_input($conn, $_GET['id']) : '';
$record_type = isset($_GET['type']) ? sanitize_input($conn, $_GET['type']) : '';

// 1. Basic Validation
if (empty($customer_id) || $record_type !== 'Customer') {
    die("Invalid request or insufficient parameters for deletion.");
}

$message = '';
$redirect_url = 'admin_dashboard.php?section=customers-section';

// --- Start Deletion Process using a Transaction ---

// Note: Your 'users' table uses customer_id (e.g., 'CUST1953') as the primary key.
mysqli_begin_transaction($conn);
$transaction_successful = true;

try {
    
    // 2. CASCADE FIX: Delete all dependent records in the 'bookings' table first.
    // Assuming a foreign key relationship exists between users.customer_id and bookings.customer_id/phone
    // Since your bookings table doesn't have a customer_id column, we'll delete by matching the customer's phone/name if possible.
    // For safety, we will NOT delete bookings unless a direct customer_id relationship is established.
    // We'll rely on the DB structure provided in the initial SQL which implies bookings are only linked to rooms, not customers, 
    // although this is usually bad practice. If customers can be deleted, there must be no dependent bookings.
    
    // --- SAFE ASSUMPTION: Customers table is independent of bookings in the provided schema ---
    // If your actual DB links bookings to users, you MUST uncomment the CASCADE DELETE query below.
    
    /* // Uncomment this if you must delete related bookings first (HIGHLY RECOMMENDED)
    $sql_delete_customer_bookings = "DELETE FROM bookings WHERE customer_id = '$customer_id'";
    if (!mysqli_query($conn, $sql_delete_customer_bookings)) {
        throw new Exception("Error deleting dependent bookings: " . mysqli_error($conn));
    }
    */
    
    // 3. Delete the customer record from the 'users' table
    $sql_delete_customer = "DELETE FROM users WHERE customer_id = '$customer_id'";
    if (!mysqli_query($conn, $sql_delete_customer)) {
        throw new Exception("Error deleting customer record: " . mysqli_error($conn));
    }

    // If successful, commit the transaction.
    mysqli_commit($conn);
    $message = "Customer ID $customer_id deleted successfully.";
    
} catch (Exception $e) {
    // If any error occurred, rollback the transaction
    mysqli_rollback($conn);
    $transaction_successful = false;
    $message = "Deletion failed: " . $e->getMessage();
}

mysqli_close($conn);

// 4. Redirect with the result message
if ($transaction_successful) {
    header("Location: $redirect_url&status=success&msg=" . urlencode($message));
} else {
    header("Location: $redirect_url&status=error&msg=" . urlencode($message));
}
exit();
?>