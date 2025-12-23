<?php
include 'db.php';

$room = $_GET['room'];
$start = $_GET['start'];
$end = $_GET['end'];

// ઓનલાઇન અને ઓફલાઇન બંને ટેબલમાં ચેક કરો
$sql = "SELECT (
    (SELECT COUNT(*) FROM bookings 
     WHERE room_number = '$room' 
     AND status IN ('Confirmed', 'Checked-in') 
     AND NOT (checkout <= '$start' OR checkin >= '$end'))
    +
    (SELECT COUNT(*) FROM offline_booking 
     WHERE room_number = '$room' 
     AND NOT (checkout_date <= '$start' OR checkin_date >= '$end'))
) AS total_conflicts";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo json_encode(['is_booked' => ($row['total_conflicts'] > 0)]);
?>