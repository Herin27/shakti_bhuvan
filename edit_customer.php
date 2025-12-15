<?php
include 'db.php'; 

$customer_id = null;
$customer = null;
$message = '';
$message_type = '';

// Check if a customer ID is provided
if (isset($_GET['id'])) {
    $customer_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // --- 1. Fetch Current Customer Data ---
    $sql_fetch = "SELECT * FROM users WHERE customer_id = '$customer_id'";
    $result_fetch = mysqli_query($conn, $sql_fetch);
    
    if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
        $customer = mysqli_fetch_assoc($result_fetch);
    } else {
        $message = "Error: Customer ID '$customer_id' not found.";
        $message_type = 'danger';
        $customer_id = null; 
    }
} else {
    $message = "Error: Invalid customer ID provided.";
    $message_type = 'danger';
}

// --- 2. Handle Form Submission (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $customer_id) {
    
    // Sanitize and validate input fields
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Optional fields (often read-only in admin, but allow editing)
    $total_spent = mysqli_real_escape_string($conn, $_POST['total_spent']);
    $bookings_count = mysqli_real_escape_string($conn, $_POST['bookings_count']);
    
    // --- 3. Execute UPDATE Query ---
    $sql_update = "
        UPDATE users SET 
            name = '$name',
            email = '$email',
            phone = '$phone',
            location = '$location',
            status = '$status',
            bookings = '$bookings_count',
            total_spent = '$total_spent'
        WHERE customer_id = '$customer_id'
    ";

    if (mysqli_query($conn, $sql_update)) {
        $message = "Customer **$customer_id** updated successfully!";
        $message_type = 'success';
        // Redirect to prevent form resubmission and show status
        header("Location: edit_customer.php?id=$customer_id&status=success&msg=" . urlencode($message));
        exit();
    } else {
        $message = "Error updating customer: " . mysqli_error($conn);
        $message_type = 'danger';
    }
}

// Re-fetch data after redirection (if any)
if (isset($_GET['status'])) {
    $sql_fetch = "SELECT * FROM users WHERE customer_id = '$customer_id'";
    $result_fetch = mysqli_query($conn, $sql_fetch);
    if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
        $customer = mysqli_fetch_assoc($result_fetch);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer: <?php echo $customer ? htmlspecialchars($customer['customer_id']) : 'N/A'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 700px; margin-top: 30px; margin-bottom: 50px; }
        .card { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="mb-4">
            <a href="admin_panel.php?section=customers-section" class="text-secondary me-2"><i class="fas fa-arrow-left"></i></a>
            Edit Customer Details
        </h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($customer): ?>
        <form method="POST" action="edit_customer.php?id=<?php echo htmlspecialchars($customer_id); ?>">
            <div class="card p-4 mb-4">
                <h4 class="card-title">Customer ID: <?php echo htmlspecialchars($customer['customer_id']); ?></h4>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($customer['location']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="ACTIVE" <?php echo ($customer['status'] == 'ACTIVE') ? 'selected' : ''; ?>>ACTIVE</option>
                            <option value="INACTIVE" <?php echo ($customer['status'] == 'INACTIVE') ? 'selected' : ''; ?>>INACTIVE</option>
                            <option value="VIP" <?php echo ($customer['status'] == 'VIP') ? 'selected' : ''; ?>>VIP</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <h4 class="card-title">Loyalty Data (Read-Only/Editable)</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="bookings_count" class="form-label">Total Bookings</label>
                        <input type="number" class="form-control" id="bookings_count" name="bookings_count" value="<?php echo htmlspecialchars($customer['bookings']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="total_spent" class="form-label">Total Spent (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="total_spent" name="total_spent" value="<?php echo htmlspecialchars($customer['total_spent']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Member Since</label>
                        <input type="text" class="form-control" value="<?php echo date('Y-m-d', strtotime($customer['member_since'])); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rating</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($customer['rating'] ?? 'N/A'); ?>" disabled>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100" style="background-color: #a0522d; border-color: #a0522d;">
                <i class="fas fa-save me-2"></i> Save Customer Changes
            </button>
        </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>