<?php
include 'db.php';
include 'header.php';

// ૧. યુઝર ઇનપુટ મેળવો
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$guests_per_room = intval($_POST['guests'] ?? 0); // એક રૂમમાં કેટલા ગેસ્ટ સમાઈ શકે
$rooms_needed = intval($_POST['rooms_needed'] ?? 1); // કુલ કેટલા રૂમ જોઈએ છે

if (!empty($checkin) && !empty($checkout)) {

    // ૨. એડવાન્સ ડાયનેમિક ક્વેરી
    // આ ક્વેરી દરેક રૂમ ટાઈપ માટે ગણતરી કરશે કે તે તારીખે કેટલા ફિઝિકલ રૂમ ખાલી છે
    // search.php ની અંદર આ ક્વેરી બદલો

$sql = "SELECT r.*, 
        (SELECT COUNT(*) FROM room_numbers rn WHERE rn.room_type_id = r.id AND rn.status != 'Maintenance') as total_physical,
        (
            SELECT COUNT(*) FROM bookings b 
            WHERE b.room_id = r.id 
            AND b.status IN ('Confirmed', 'Checked-in') 
            AND NOT (b.checkout <= ? OR b.checkin >= ?)
        ) as online_booked,
        (
            SELECT COUNT(*) FROM offline_booking ob 
            JOIN room_numbers rn ON ob.room_number = rn.room_number
            WHERE rn.room_type_id = r.id
            AND NOT (ob.checkout_date <= ? OR ob.checkin_date >= ?)
        ) as offline_booked
        FROM rooms r
        /* અહીં આપણે ચેક કરીએ છીએ કે શું પૂરતા રૂમ ખાલી છે? */
        HAVING (total_physical - (online_booked + offline_booked)) >= ? 
        /* અને શું એ રૂમોમાં આટલા ગેસ્ટ સમાઈ જશે? (રૂમની સંખ્યા * રૂમ દીઠ કેપેસિટી) */
        AND (r.guests * ?) >= ?
";

$stmt = $conn->prepare($sql);
// Bind Parameters: checkin, checkout, checkin, checkout, rooms_needed, rooms_needed, total_guests
$total_guests = intval($_POST['guests']); // કુલ ૧૪ ગેસ્ટ
$stmt->bind_param("ssssiii", $checkin, $checkout, $checkin, $checkout, $rooms_needed, $rooms_needed, $total_guests);
$stmt->execute();
$result = $stmt->get_result();

    // રાતની સંખ્યા ગણતરી
    $nights = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);
    if ($nights < 1) $nights = 1;
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Available Accommodations</h2>
        <div class="badge bg-primary p-2">
            Searching for <?= $rooms_needed ?> Room(s) | <?= $checkin ?> to <?= $checkout ?>
        </div>
    </div>

    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $available_count = $row['total_physical'] - ($row['online_booked'] + $row['offline_booked']);
                $totalPrice = $row['discount_price'] * $nights * $rooms_needed;

                // ઈમેજ સેટઅપ
                $images = !empty($row['image']) ? explode(",", $row['image']) : [];
                $imagePath = !empty($images[0]) ? "uploads/" . trim($images[0]) : "assets/default-room.jpg";
        ?>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="position-relative">
                    <img src="<?= $imagePath ?>" class="card-img-top" style="height:220px; object-fit:cover;">
                    <div class="position-absolute top-0 end-0 m-3 badge bg-success">
                        <?= $available_count ?> Rooms Left
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0"><?= htmlspecialchars($row['name']) ?></h5>
                        <span class="text-warning fw-bold">⭐ <?= $row['rating'] ?></span>
                    </div>

                    <p class="small text-muted mb-3"><?= htmlspecialchars(substr($row['description'], 0, 90)) ?>...</p>

                    <div class="mb-3">
                        <span class="text-dark small"><i class="fas fa-users"></i> Up to <?= $row['guests'] ?> per
                            room</span>
                        <br>
                        <span class="text-dark small"><i class="fas fa-layer-group"></i> <?= $row['floor'] ?></span>
                    </div>

                    <div class="price-box bg-light p-3 rounded-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Price/Night:</span>
                            <span class="fw-bold text-dark">₹<?= number_format($row['discount_price']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="text-primary fw-bold">Grand Total:</span>
                            <span class="text-primary h5 mb-0">₹<?= number_format($totalPrice) ?></span>
                        </div>
                        <small class="text-muted d-block mt-1">(For <?= $nights ?> night(s) & <?= $rooms_needed ?>
                            room(s))</small>
                    </div>

                    <a href="View_Details.php?id=<?= $row['id'] ?>" class="btn btn-dark w-100 py-2">View Details &
                        Book</a>
                </div>
            </div>
        </div>

        <?php
            }
        } else {
            echo '
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-4x text-light mb-3"></i>
                <h3>No Rooms Found</h3>
                <p class="text-muted">We couldn\'t find a room type that has ' . $rooms_needed . ' rooms available for these dates.</p>
                <a href="index.php" class="btn btn-outline-primary mt-3">Try Different Dates</a>
            </div>';
        }
        ?>
    </div>
</div>

<?php
    $stmt->close();
} else {
    echo "<div class='container mt-5 text-center'><h3>Invalid Search Request</h3><a href='index.php'>Go Back</a></div>";
}

$conn->close();
include 'footer.php';
?>