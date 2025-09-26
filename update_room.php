<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = $_POST['room_id'];
    $name      = $_POST['name'];
    $type      = $_POST['type'];
    $price     = $_POST['price'];
    $capacity  = $_POST['capacity'];
    $amenities = $_POST['amenities'];
    $status    = $_POST['status'];

    $stmt = $conn->prepare("UPDATE rooms 
        SET name=?, bed_type=?, price=?, guests=?, amenities=?, status=? 
        WHERE id=?");

    // ✅ correct types: s = string, d = double/float, i = integer
    $stmt->bind_param("ssdissi", $name, $type, $price, $capacity, $amenities, $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $stmt->error]);
    }
    exit;
}
echo json_encode(["success" => false, "message" => "Invalid request"]);
