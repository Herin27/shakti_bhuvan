<?php
session_start();
include 'db.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ✅ Delete single image
    if ($action === "delete_image" && !empty($_POST['room_id'])) {
        $room_id = intval($_POST['room_id']);

        // Get image path
        $stmt = $conn->prepare("SELECT image FROM rooms WHERE id=?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->bind_result($path);
        $stmt->fetch();
        $stmt->close();

        // Delete file if exists
        if ($path && file_exists($path)) unlink($path);

        // Remove from DB
        $stmt = $conn->prepare("UPDATE rooms SET image=NULL WHERE id=?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["success" => true]);
        exit;
    }

    // ✅ Update room info
    $room_id   = intval($_POST['room_id']);
    $name      = $_POST['name'] ?? '';
    $type      = $_POST['type'] ?? '';
    $price     = $_POST['price'] ?? 0;
    $capacity  = $_POST['capacity'] ?? 1;
    $amenities = $_POST['amenities'] ?? '';
    $status    = $_POST['status'] ?? 'Available';

    $stmt = $conn->prepare("UPDATE rooms SET name=?, bed_type=?, price=?, guests=?, amenities=?, status=? WHERE id=?");
    $stmt->bind_param("ssdissi", $name, $type, $price, $capacity, $amenities, $status, $room_id);
    $stmt->execute();
    $stmt->close();

    // ✅ Upload new single image (replace old)
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $unique = time() . "_" . basename($filename);
        $targetPath = $uploadDir . $unique;

        if (move_uploaded_file($tmp, $targetPath)) {
            // delete old image
            $stmt = $conn->prepare("SELECT image FROM rooms WHERE id=?");
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $stmt->bind_result($oldPath);
            $stmt->fetch();
            $stmt->close();
            if ($oldPath && file_exists($oldPath)) unlink($oldPath);

            // update DB
            $stmt = $conn->prepare("UPDATE rooms SET image=? WHERE id=?");
            $stmt->bind_param("si", $targetPath, $room_id);
            $stmt->execute();
            $stmt->close();
        }

        echo json_encode(["success" => true, "image" => $targetPath]);
        exit;
    }

    echo json_encode(["success" => true]);
    exit;
}

// ✅ Fetch single room
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_room_details') {
    $room_id = intval($_GET['room_id']);
    $stmt = $conn->prepare("SELECT id, name, bed_type, price, guests, amenities, status, image FROM rooms WHERE id=?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    echo json_encode(["success" => true, "room" => $room]);
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request"]);
