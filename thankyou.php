<?php
session_start();
include 'db.php';

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

// ડેટાબેઝમાંથી સંપૂર્ણ વિગતો લો (રૂમની વિગત સાથે)
$sql = "SELECT b.*, r.name as room_name, r.ac_status 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.id = $booking_id";
$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);

if ($booking) {
    $user_email = $booking['email'];
    $admin_email = 'shaktibhuvanambaji@gmail.com'; // એડમિન ઈમેલ

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'herin7151@gmail.com';
        $mail->Password   = 'ksyc bikf goha txie';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('herin7151@gmail.com', 'Shakti Bhuvan');
        
        // બંનેને મેઈલ મોકલવા માટે
        $mail->addAddress($user_email); 
        $mail->addAddress($admin_email); 

        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmed - #' . $booking_id . ' (Shakti Bhuvan)';

        // Attractive HTML Email Template
        $mailContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
            <div style='background: #d5931f; color: white; padding: 20px; text-align: center;'>
                <h2>Booking Confirmation</h2>
            </div>
            <div style='padding: 20px;'>
                <p>Hello <strong>{$booking['customer_name']}</strong>,</p>
                <p>Your booking at <strong>Shakti Bhuvan</strong> has been successfully confirmed. Below are your details:</p>
                
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <tr style='background: #f9f9f9;'>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Booking ID</strong></td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>#{$booking_id}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Room Type</strong></td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$booking['room_name']} ({$booking['ac_status']})</td>
                    </tr>
                    <tr style='background: #f9f9f9;'>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Check-in</strong></td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$booking['checkin']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Check-out</strong></td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$booking['checkout']}</td>
                    </tr>
                    <tr style='background: #f9f9f9;'>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Total Price</strong></td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>₹" . number_format($booking['total_price'], 2) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Transaction ID</strong></td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; color: green;'>{$booking['razorpay_id']}</td>
                    </tr>
                    <tr>
    <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Phone</strong></td>
    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$booking['phone']}</td>
</tr>
                </table>

                <div style='background: #fff8e1; padding: 15px; border-radius: 5px; border: 1px solid #ffe082;'>
                    <p style='margin: 0;'><strong>Note:</strong> Please carry a valid ID proof during check-in.</p>
                </div>

                <p style='margin-top: 20px;'>Thank you for choosing Shakti Bhuvan!</p>
                <p>Regards,<br>Team Shakti Bhuvan</p>
            </div>
            <div style='background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #777;'>
                Shakti Bhuvan, Ambaji, Gujarat.
            </div>
        </div>";

        $mail->Body = $mailContent;
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