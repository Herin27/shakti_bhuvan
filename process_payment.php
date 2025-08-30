<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['payment_method'];

    // Save payment method (for demo only)
    $_SESSION['payment'] = [
        'method' => $method,
        'status' => 'success'
    ];

    // Redirect to thank you page
    header("Location: payment.php");
    exit;
} else {
    echo "Invalid request.";
}
?>
