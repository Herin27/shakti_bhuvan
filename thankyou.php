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
    <div class="icon"><i class="fa fa-check-circle"></i></div>
    <h1>Thank You for Your Booking!</h1>
    <p>Your reservation has been successfully confirmed. We look forward to hosting you.</p>

    <!-- Example if booking details are fetched -->
    <!--
    <div class="details">
      <p><strong>Room:</strong> <?php echo $booking['name']; ?></p>
      <p><strong>Check-in:</strong> <?php echo $booking['checkin']; ?></p>
      <p><strong>Check-out:</strong> <?php echo $booking['checkout']; ?></p>
      <p><strong>Total Paid:</strong> ₹<?php echo $booking['total_price']; ?></p>
    </div>
    -->

    <a href="index.php" class="btn">Back to Home</a>
  </div>
</body>
</html>
