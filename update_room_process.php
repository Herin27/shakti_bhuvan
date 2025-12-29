<?php
include 'db.php';

if (isset($_POST['update_room'])) {
    $room_id = intval($_POST['room_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $bed_type = mysqli_real_escape_string($conn, $_POST['bed_type']);
    $guests = intval($_POST['guests']);
    $ac_status = $_POST['ac_status'];
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);

    $sql_update = "UPDATE rooms SET 
                    name = '$name',
                    price = '$price',
                    discount_price = '$discount_price',
                    description = '$description',
                    size = '$size',
                    bed_type = '$bed_type',
                    guests = $guests,
                    ac_status = '$ac_status',
                    amenities = '$amenities'
                   WHERE id = $room_id";

    if (mysqli_query($conn, $sql_update)) {
        header("Location: admin_dashboard.php?section=manage-rooms-section&msg=room_updated");
        exit;
    } else {
        echo "Error updating room: " . mysqli_error($conn);
    }
}
?>