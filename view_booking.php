<?php
include 'db.php';

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Use your actual primary key column here
$sql = "SELECT * FROM bookings WHERE id = $booking_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<h3>Booking not found!</h3>";
    exit;
}

$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Booking Details</h2>
    <table class="table table-bordered mt-3">
        <tr>
            <th>Booking ID</th>
            <td><?= $booking['id'] ?></td>
        </tr>
        <tr>
            <th>Customer Name</th>
            <td><?= htmlspecialchars($booking['customer_name']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($booking['email']) ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= htmlspecialchars($booking['phone']) ?></td>
        </tr>
        <tr>
            <th>Room</th>
            <td><?= htmlspecialchars($booking['room_id']) ?></td>
        </tr>
        <tr>
            <th>Check-in Date</th>
            <td><?= htmlspecialchars($booking['checkin_date']) ?></td>
        </tr>
        <tr>
            <th>Check-out Date</th>
            <td><?= htmlspecialchars($booking['checkout_date']) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= htmlspecialchars($booking['status']) ?></td>
        </tr>
    </table>
    <a href="admin_dashboard.php" class="btn btn-primary mt-3">Back to Bookings</a>
</div>
</body>
</html>
