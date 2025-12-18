<?php
// Optionally fetch booking details if you want to show them
// include 'db_connect.php';
// $booking_id = $_GET['id'] ?? null;
// if ($booking_id) {
//     $stmt = $conn->prepare("SELECT b.*, r.name, r.price FROM bookings b JOIN rooms r ON b.room_id=r.id WHERE b.id=?");
//     $stmt->bind_param("i", $booking_id);
//     $stmt->execute();
//     $booking = $stmt->get_result()->fetch_assoc();
// }
$status = $_GET['status'] ?? 'failed';
$booking_id = $_GET['booking_id'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You - Booking Confirmed</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f8fa;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .thankyou-box {
      background: #fff;
      padding: 40px;
      max-width: 600px;
      text-align: center;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    .thankyou-box .icon {
      font-size: 70px;
      color: #f1c45f;
      margin-bottom: 20px;
    }
    h1 {
      color: #333;
      margin-bottom: 10px;
    }
    p {
      color: #666;
      margin-bottom: 20px;
      line-height: 1.6;
    }
    .details {
      background: #f9f9f9;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      text-align: left;
    }
    .details strong {
      color: #333;
    }
    .btn {
      display: inline-block;
      padding: 12px 25px;
      background: #f1c45f;
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 8px;
      transition: 0.3s;
    }
    .btn:hover {
      background: #d4a93d;
    }
  </style>
</head>
<body>
  <div class="thankyou-box">
    <?php if($status === 'success'): ?>
        <div class="icon" style="color: #28a745;"><i class="fa fa-check-circle"></i></div>
        <h1>Booking Confirmed!</h1>
        <p>Payment successful.</p>
    <?php else: ?>
        <div class="icon" style="color: #dc3545;"><i class="fa fa-times-circle"></i></div>
        <h1>Payment Failed</h1>
        <p>There was an issue processing your payment. Please try again.</p>
    <?php endif; ?>
    <a href="index.php" class="btn">Back to Home</a>
</div>
</body>
</html>
