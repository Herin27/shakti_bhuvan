<?php
include 'db.php';

$rn_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$message = '';

// --- Handle Form Submission (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rn_id > 0) {
    
    $new_room_number = mysqli_real_escape_string($conn, trim($_POST['room_number']));
    $new_floor = mysqli_real_escape_string($conn, $_POST['floor']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $new_room_type_id = intval($_POST['room_type_id']);

    if (empty($new_room_number) || $new_room_type_id <= 0) {
        $message = '<div class="alert alert-danger">Error: Room Number and Room Type are required.</div>';
    } else {
        $sql_update = "
            UPDATE room_numbers 
            SET 
                room_number = '$new_room_number',
                floor = '$new_floor',
                status = '$new_status',
                room_type_id = $new_room_type_id
            WHERE id = $rn_id
        ";

        if (mysqli_query($conn, $sql_update)) {
            $message = '<div class="alert alert-success">Physical Room #'.htmlspecialchars($new_room_number).' updated successfully!</div>';
        } else {
            $error_detail = mysqli_error($conn);
            // Check for duplicate entry error
            if (strpos($error_detail, 'Duplicate entry') !== false) {
                 $message = '<div class="alert alert-danger">Error: Room Number "'.htmlspecialchars($new_room_number).'" already exists. Please choose a unique number.</div>';
            } else {
                 $message = '<div class="alert alert-danger">Error updating room: ' . $error_detail . '</div>';
            }
        }
    }
}

// --- Fetch Current Room Number Details (GET/After POST) ---
if ($rn_id > 0) {
    $sql_fetch = "
        SELECT rn.*, r.name AS room_type_name
        FROM room_numbers rn
        JOIN rooms r ON rn.room_type_id = r.id
        WHERE rn.id = $rn_id
    ";
    $result = mysqli_query($conn, $sql_fetch);
    $room_number_data = mysqli_fetch_assoc($result);

    if (!$room_number_data) {
        die("<div class='container mt-5'><div class='alert alert-danger'>Error: Physical Room ID not found.</div></div>");
    }
} else {
    die("<div class='container mt-5'><div class='alert alert-danger'>Error: Invalid Room Number ID provided.</div></div>");
}

// --- Fetch All Room Types for Dropdown ---
$all_room_types = [];
$sql_types = "SELECT id, name FROM rooms ORDER BY name ASC";
$result_types = mysqli_query($conn, $sql_types);
if ($result_types) {
    while ($row = mysqli_fetch_assoc($result_types)) {
        $all_room_types[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room #<?php echo htmlspecialchars($room_number_data['room_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container { max-width: 700px; margin-top: 50px; }
        .card { border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-custom { background-color: #a0522d; border-color: #a0522d; color: white; }
        .btn-custom:hover { background-color: #8b4513; border-color: #8b4513; color: white; }
        h2 { color: #5a4636; }
    </style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Physical Room: room <?php echo htmlspecialchars($room_number_data['room_number']); ?></h2>
        <a href="admin_dashboard.php?section=manage-room-numbers-section" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Room Numbers</a>
    </div>

    <?php echo $message; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $rn_id; ?>">

                <div class="mb-3">
                    <label for="room_number" class="form-label">Physical Room Number *</label>
                    <input type="text" class="form-control" id="room_number" name="room_number" 
                           value="<?php echo htmlspecialchars($room_number_data['room_number']); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="room_type_id" class="form-label">Associated Room Type *</label>
                        <select class="form-select" id="room_type_id" name="room_type_id" required>
                            <?php foreach ($all_room_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>" 
                                    <?php echo ($type['id'] == $room_number_data['room_type_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="floor" class="form-label">Floor</label>
                        <select class="form-select" id="floor" name="floor" required>
                            <?php
                            $floors = ['Ground Floor', 'First Floor', 'Second Floor', 'Third Floor', 'Fourth Floor'];
                            foreach ($floors as $floor_name): ?>
                                <option value="<?php echo $floor_name; ?>" 
                                    <?php echo ($floor_name == $room_number_data['floor']) ? 'selected' : ''; ?>>
                                    <?php echo $floor_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Current Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php
                        $statuses = ['Available', 'Occupied', 'Maintenance'];
                        foreach ($statuses as $status_name): ?>
                            <option value="<?php echo $status_name; ?>" 
                                <?php echo ($status_name == $room_number_data['status']) ? 'selected' : ''; ?>>
                                <?php echo $status_name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Setting status to 'Occupied' here is manual; automated changes happen on booking/cleanup.</small>
                </div>
                
                <button type="submit" class="btn btn-custom mt-3 w-100">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>