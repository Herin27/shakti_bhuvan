<?php
include 'db.php';
include 'header.php';   

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$guests = $_POST['guests'] ?? '';

if (!empty($checkin) && !empty($checkout) && !empty($guests)) {
    $sql = "
        SELECT * FROM rooms r
        WHERE r.guests >= ?
        AND r.id NOT IN (
            SELECT b.room_id FROM bookings b
            WHERE (b.checkin <= ? AND b.checkout >= ?)
        )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $guests, $checkout, $checkin);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<div class="container mt-4">';
    echo '<div class="row">';
    echo '<h1 class="mb-4">Searched Rooms</h1>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // calculate nights
            $nights = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);
            if ($nights < 1) $nights = 1;
            $totalPrice = $row['price'] * $nights;

            // ✅ FIX: Clean image path
            $imageFile = trim($row['image']);
            
            // Case 1: DB already has path (uploads/room1.jpg)
            if (strpos($imageFile, "uploads/") !== false) {
                $imagePath = $imageFile;
            } 
            // Case 2: Only filename stored (room1.jpg)
            else {
                $imagePath = "uploads/" . $imageFile;
            }

            // Fallback if missing
            if (empty($imageFile) || !file_exists(__DIR__ . "/" . $imagePath)) {
                $imagePath = "assets/default-room.jpg";
            }

            echo '
            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <img src="'.$imagePath.'" 
                         alt="'.htmlspecialchars($row['name']).'" 
                         class="card-img-top rounded-top-4" 
                         style="height:200px;object-fit:cover;">
                         
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            '.htmlspecialchars($row['name']).'
                            <span class="text-warning">⭐ '.$row['rating'].'</span>
                        </h5>
                        <p class="card-text text-muted" style="height:50px;overflow:hidden;">'.htmlspecialchars($row['description']).'</p>
                        
                        <div class="mb-2">';
                        $amenities = explode(",", $row['amenities']);
                        foreach($amenities as $a) {
                            echo '<span class="badge bg-light text-dark me-1">'.trim($a).'</span>';
                        }
                        echo '</div>

                        <h6 class="fw-bold text-success">₹'.$row['price'].'/night</h6>
                        <p class="text-muted small">Total for '.$nights.' night(s): ₹'.$totalPrice.'</p>
                        
                        <a href="View_Details.php?id='.$row['id'].'" class="btn btn-outline-dark w-100">View Details</a>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '<p class="text-center">No rooms available for your selection.</p>';
    }

    echo '</div></div>';
    $stmt->close();
} else {
    echo "<p class='text-center'>Please select check-in, check-out, and guests.</p>";
}

$conn->close();
include 'footer.php';
?>
