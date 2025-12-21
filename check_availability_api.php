<?php
include 'db.php';

$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$checkin = isset($_GET['checkin']) ? mysqli_real_escape_string($conn, $_GET['checkin']) : '';
$checkout = isset($_GET['checkout']) ? mysqli_real_escape_string($conn, $_GET['checkout']) : '';

if ($room_id > 0 && !empty($checkin) && !empty($checkout)) {
    // ૧. આ રૂમ ટાઈપના કુલ કેટલા ફિઝિકલ રૂમ છે
    $sql_total = "SELECT COUNT(*) as total FROM room_numbers WHERE room_type_id = $room_id";
    $res_total = mysqli_query($conn, $sql_total);
    $total_rooms = mysqli_fetch_assoc($res_total)['total'];

    // ૨. પસંદ કરેલી તારીખ વચ્ચે કેટલા રૂમ ઓક્યુપાઈડ (Occupied) છે તે શોધો
    // લોજિક: જો નવું ચેક-ઈન જૂના ચેક-આઉટ પહેલા હોય અને નવું ચેક-આઉટ જૂના ચેક-ઈન પછી હોય, તો એ ઓવરલેપ ગણાય
    $sql_booked = "SELECT COUNT(*) as booked FROM bookings 
                   WHERE room_id = $room_id 
                   AND status IN ('Confirmed', 'Checked-in') 
                   AND NOT (checkout <= '$checkin' OR checkin >= '$checkout')";
    $res_booked = mysqli_query($conn, $sql_booked);
    $booked_count = mysqli_fetch_assoc($res_booked)['booked'];

    $available_count = $total_rooms - $booked_count;

    echo json_encode(['available' => $available_count]);
} else {
    echo json_encode(['available' => 0, 'error' => 'Invalid parameters']);
}
?>