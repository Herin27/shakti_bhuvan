<?php
include 'db.php';

// રૂમ નંબરનો ID મેળવો (તમારા JavaScript માંથી પસાર થયેલ numerical-id)
$rn_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// રૂમ નંબર અને તેની સાથે સંકળાયેલ રૂમ ટાઈપ (rooms table) ની વિગતો મેળવો
$sql = "SELECT rn.*, r.name as room_type_name, r.price, r.discount_price, r.guests, r.ac_status 
        FROM room_numbers rn 
        JOIN rooms r ON rn.room_type_id = r.id 
        WHERE rn.id = $rn_id";

$result = mysqli_query($conn, $sql);
$room_num_data = mysqli_fetch_assoc($result);

if (!$room_num_data) {
    echo "<div class='container mt-5 text-center'><h3>Room Record Not Found!</h3><a href='admin_dashboard.php' class='btn btn-primary'>Back</a></div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details - #<?= $room_num_data['room_number'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --admin-primary: #a0522d;
        --admin-bg: #fffaf0;
    }

    body {
        background-color: var(--admin-bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .detail-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }

    .header-section {
        background: var(--admin-primary);
        color: white;
        padding: 25px;
    }

    .info-box {
        background: #fdfdfd;
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid var(--admin-primary);
        margin-bottom: 20px;
    }

    .label-text {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #888;
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    .value-text {
        font-size: 1.2rem;
        color: #333;
        font-weight: 500;
    }

    .status-badge {
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    /* Status Colors */
    .status-available {
        background-color: #e6ffe6;
        color: #008000;
    }

    .status-occupied {
        background-color: #ffcccc;
        color: #cc0000;
    }

    .status-maintenance {
        background-color: #fffbe6;
        color: #ccaa00;
    }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="mb-4">
            <a href="admin_dashboard.php?section=manage-room-numbers-section"
                class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Room Numbers
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="detail-card">
                    <div class="header-section d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">Physical Room: <?= htmlspecialchars($room_num_data['room_number']) ?></h3>
                            <p class="mb-0 opacity-75">ID Reference: RN-<?= str_pad($rn_id, 4, '0', STR_PAD_LEFT) ?></p>
                        </div>
                        <div>
                            <?php 
                            $status_class = "status-" . strtolower(str_replace(' ', '', $room_num_data['status']));
                        ?>
                            <span class="status-badge <?= $status_class ?> shadow-sm">
                                <i class="fas fa-info-circle me-1"></i> <?= $room_num_data['status'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="label-text">Room Number</div>
                                    <div class="value-text"><?= htmlspecialchars($room_num_data['room_number']) ?></div>
                                </div>
                                <div class="info-box">
                                    <div class="label-text">Floor Location</div>
                                    <div class="value-text"><?= htmlspecialchars($room_num_data['floor']) ?></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="label-text">Room Type</div>
                                    <div class="value-text"><?= htmlspecialchars($room_num_data['room_type_name']) ?>
                                    </div>
                                </div>
                                <div class="info-box">
                                    <div class="label-text">Max Capacity</div>
                                    <div class="value-text"><?= $room_num_data['guests'] ?> Guests
                                        (<?= $room_num_data['ac_status'] ?>)</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded border border-dashed text-center">
                            <div class="row">
                                <div class="col-6 border-end">
                                    <span class="label-text d-block">Price / Night</span>
                                    <span
                                        class="h4 text-primary">₹<?= number_format($room_num_data['discount_price'], 2) ?></span>
                                </div>
                                <div class="col-6">
                                    <span class="label-text d-block">Original Price</span>
                                    <span
                                        class="text-muted"><del>₹<?= number_format($room_num_data['price'], 2) ?></del></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-center mt-5">
                            <a href="edit_room_number.php?id=<?= $rn_id ?>" class="btn btn-primary px-4">
                                <i class="fas fa-edit me-2"></i>Edit Room
                            </a>
                            <a href="delete_room_number.php?id=<?= $rn_id ?>" class="btn btn-danger px-4"
                                onclick="return confirm('ખાતરી છે? આ રૂમ નંબર કાયમી માટે ડિલીટ થઈ જશે.')">
                                <i class="fas fa-trash-alt me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>

                <p class="text-center mt-4 text-muted small">
                    <i class="fas fa-shield-alt me-1"></i> Powered by Shakti Bhuvan Property Management System
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>