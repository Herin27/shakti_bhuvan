<?php
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// રૂમની જૂની વિગતો મેળવો
$sql = "SELECT * FROM rooms WHERE id = $id";
$result = mysqli_query($conn, $sql);
$room = mysqli_fetch_assoc($result);

if (!$room) {
    die("Room type not found!");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Room Type - <?= htmlspecialchars($room['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background-color: #fffaf0;
        padding: 40px 0;
    }

    .edit-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border: none;
        padding: 30px;
    }

    .btn-save {
        background-color: #a0522d;
        color: white;
        border: none;
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="edit-card">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h4><i class="fas fa-edit me-2 text-warning"></i> Edit Room Type:
                            <?= htmlspecialchars($room['name']) ?></h4>
                        <a href="admin_dashboard.php?section=manage-rooms-section"
                            class="btn btn-sm btn-outline-secondary">Back</a>
                    </div>

                    <form action="update_room_process.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="room_id" value="<?= $id ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Room Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="<?= htmlspecialchars($room['name']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Price (Standard)</label>
                                <input type="number" name="price" class="form-control" value="<?= $room['price'] ?>"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Discount Price</label>
                                <input type="number" name="discount_price" class="form-control"
                                    value="<?= $room['discount_price'] ?>" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control"
                                    rows="3"><?= htmlspecialchars($room['description']) ?></textarea>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Size (sq ft)</label>
                                <input type="text" name="size" class="form-control"
                                    value="<?= htmlspecialchars($room['size']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Bed Type</label>
                                <input type="text" name="bed_type" class="form-control"
                                    value="<?= htmlspecialchars($room['bed_type']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Max Guests</label>
                                <input type="number" name="guests" class="form-control" value="<?= $room['guests'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">AC Status</label>
                                <select name="ac_status" class="form-select">
                                    <option value="AC" <?= ($room['ac_status'] == 'AC') ? 'selected' : '' ?>>AC</option>
                                    <option value="Non-AC" <?= ($room['ac_status'] == 'Non-AC') ? 'selected' : '' ?>>
                                        Non-AC</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Amenities (અલ્પવિરામ (,) થી અલગ કરો)</label>
                                <input type="text" name="amenities" class="form-control"
                                    value="<?= htmlspecialchars($room['amenities']) ?>"
                                    placeholder="WiFi, TV, AC, Water">
                            </div>

                            <div class="col-md-12 text-center mt-4">
                                <button type="submit" name="update_room" class="btn btn-save px-5">
                                    <i class="fas fa-save me-2"></i> Update Room Type
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>