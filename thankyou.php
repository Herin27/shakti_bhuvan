<?php
session_start();
include 'db.php';

// PHPMailer ફાઈલો ઈમ્પોર્ટ કરો
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_GET['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);

// ડેટાબેઝમાંથી વિગતો લો - આમાં razorpay_id પણ આવી જશે
$sql = "SELECT * FROM bookings WHERE id = $booking_id";
$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);

if ($booking) {
    $user_email = $booking['email'];
    $user_name = $booking['customer_name'];
    $payment_ref_id = $booking['razorpay_id']; // Razorpay Reference ID

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'herin7151@gmail.com';
        $mail->Password   = 'ksyc bikf goha txie';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('herin7151@gmail.com', 'Hotel Royal');
        $mail->addAddress($user_email);

        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation - #' . $booking_id;
        // ઈમેલની બોડીમાં Reference ID ઉમેર્યો છે
        $mail->Body    = "<h3>Hello $user_name,</h3>
                          <p>Your booking has been successfully confirmed.</p>
                          <p><b>Booking ID:</b> #$booking_id</p>
                          <p><b>Payment Ref. : #</b> $payment_ref_id</p>
                          <p>Thank you for choosing Hotel Royal!</p>";

        $mail->send();
    } catch (Exception $e) {
        // Error handling
    }
}
?>

<!DOCTYPE html>
<html lang="gu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Booking Confirmed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', sans-serif;
    }

    /* આ કોડ સૌથી ઉપર મૂકવો */
    .header-divider {
        display: block !important;
        width: 100% !important;
        height: 2px !important;
        /* જાડાઈ વધારી છે જેથી સ્પષ્ટ દેખાય */
        background-color: #d5931f !important;
        /* ગોલ્ડન કલર */
        margin: 0 0 20px 0 !important;
        padding: 0 !important;
        position: relative !important;
        z-index: 9999 !important;
        clear: both !important;
    }

    .thankyou-card {
        max-width: 600px;
        margin: 60px auto;
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .success-icon {
        font-size: 70px;
        color: #28a745;
        margin-bottom: 20px;
    }

    .info-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
        text-align: left;
    }

    .info-label {
        font-weight: bold;
        color: #555;
    }

    .info-value {
        float: right;
        color: #333;
        font-family: monospace;
        font-size: 1.1rem;
    }

    .btn-home {
        background-color: #007bff;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-home:hover {
        background-color: #0056b3;
        color: white;
    }
    </style>
</head>

<body>
    <div class="header-divider"></div>
    <div class="container">
        <div class="thankyou-card">
            <div class="success-icon"><i class="fas fa-check-circle"></i></div>
            <h1 class="display-6 fw-bold">Payment Successful!</h1>
            <p class="lead text-muted">Your reservation is now confirmed.</p>

            <div class="info-box">
                <div class="mb-2">
                    <span class="info-label">Booking ID:</span>
                    <span class="info-value">#<?php echo $booking_id; ?></span>
                </div>
                <hr>
                <div class="mb-0">
                    <span class="info-label">Payment Ref ID:</span><br>
                    <span class="text-success fw-bold"
                        style="font-family: monospace;"><?php echo $booking['razorpay_id']; ?></span>
                </div>
            </div>

            <p class="text-secondary small">A confirmation email has been sent to
                <?php echo htmlspecialchars($booking['email']); ?>.</p>

            <div class="mt-4">
                <a href="index.php" class="btn-home"><i class="fas fa-home me-2"></i> Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>