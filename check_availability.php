<?php
// ભૂલ જોવા માટે આ ચાલુ કરો
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // ખાતરી કરો કે આ ફાઈલનું નામ સાચું છે
header('Content-Type: application/json');

$room_type_id = intval($_POST['room_id'] ?? 0);

if ($room_type_id <= 0) {
    echo json_encode(['available' => false, 'error' => 'Invalid Room ID']);
    exit;
}

$sql = "SELECT COUNT(*) as total FROM room_numbers 
        WHERE room_type_id = $room_type_id AND status = 'Available'";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row['total'] > 0) {
        echo json_encode(['available' => true]);
    } else {
        echo json_encode(['available' => false]);
    }
} else {
    echo json_encode(['available' => false, 'error' => mysqli_error($conn)]);
}
exit;