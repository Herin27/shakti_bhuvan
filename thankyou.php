<?php
session_start();
include 'db.php';

// જો બુકિંગ આઈડી ન હોય તો હોમ પેજ પર રીડાયરેક્ટ કરો
if (!isset($_GET['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);

// બુકિંગની વિગતો મેળવવા માટે ક્વેરી (વૈકલ્પિક)
$sql = "SELECT * FROM bookings WHERE id = $booking_id";
$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);
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
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .thankyou-card {
        max-width: 600px;
        margin: 80px auto;
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .success-icon {
        font-size: 80px;
        color: #28a745;
        margin-bottom: 20px;
    }

    .booking-id {
        background: #e9ecef;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        display: inline-block;
        margin: 15px 0;
        color: #333;
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

    <div class="container">
        <div class="thankyou-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="display-5 fw-bold">Thank You!</h1>
            <p class="lead text-muted">Your hotel booking has been successfully confirmed.</p>

            <div class="booking-id">
                Booking ID: #<?php echo $booking_id; ?>
            </div>

            <!-- <p class="text-secondary">
            Your room number <strong><?php echo $booking['room_number']; ?></strong> will be sent to you shortly.
        </p> -->

            <hr class="my-4">

            <p>If you have any questions, please Contact.</p>

            <div class="mt-4">
                <a href="index.php" class="btn-home">
                    <i class="fas fa-home me-2"></i> Go to Home Page
                </a>
            </div>
        </div>
    </div>

</body>

</html>