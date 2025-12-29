<?php
include 'db.php';
include 'header.php'; // Include header for navigation/styling

$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- 1. Fetch Main Room Details (including new fields) ---
$sql_room = "SELECT * FROM rooms WHERE id = $room_id";
$result_room = mysqli_query($conn, $sql_room);

if (!$result_room || mysqli_num_rows($result_room) == 0) {
    echo "<div class='container' style='padding: 50px; text-align: center;'>";
    echo "<h1>Room Not Found</h1>";
    echo "<p>The requested room details could not be loaded.</p>";
    echo "<a href='rooms.php' class='btn'>Back to All Rooms</a>";
    echo "</div>";
    include 'footer.php';
    exit;
}

$room = mysqli_fetch_assoc($result_room);

// --- 2. Dynamic Availability Logic (Advanced Date Check) ---
date_default_timezone_set('Asia/Kolkata');

// URL માંથી તારીખો મેળવો, જો ન હોય તો આજની તારીખ સેટ કરો
$checkin = $_GET['checkin'] ?? date('Y-m-d');
$checkout = $_GET['checkout'] ?? date('Y-m-d', strtotime('+1 day'));

// ૧. આ રૂમ ટાઈપના કુલ કેટલા રૂમ છે (Maintenance સિવાયના)
$sql_total = "SELECT COUNT(*) as total FROM room_numbers WHERE room_type_id = $room_id AND status != 'Maintenance'";
$res_total = mysqli_query($conn, $sql_total);
$total_physical = mysqli_fetch_assoc($res_total)['total'];

// ૨. પસંદ કરેલી તારીખે કેટલા Online રૂમ બુક છે
$sql_online = "SELECT COUNT(DISTINCT room_number) as booked FROM bookings 
               WHERE room_id = $room_id 
               AND status IN ('Confirmed', 'Checked-in') 
               AND NOT (checkout <= '$checkin' OR checkin >= '$checkout')";
$online_booked = mysqli_fetch_assoc(mysqli_query($conn, $sql_online))['booked'];

// ૩. પસંદ કરેલી તારીખે કેટલા Offline રૂમ બુક છે
$sql_offline = "SELECT COUNT(DISTINCT ob.room_number) as booked FROM offline_booking ob 
                JOIN room_numbers rn ON ob.room_number = rn.room_number
                WHERE rn.room_type_id = $room_id
                AND NOT (ob.checkout_date <= '$checkin' OR ob.checkin_date >= '$checkout')";
$offline_booked = mysqli_fetch_assoc(mysqli_query($conn, $sql_offline))['booked'];

// ૪. ફાઈનલ ઉપલબ્ધ રૂમની ગણતરી
$available_count = $total_physical - ($online_booked + $offline_booked);
if($available_count < 0) $available_count = 0;


// Image Handling
$images = !empty($room['image']) ? explode(',', $room['image']) : [];
$first_image = !empty($images[0]) ? trim($images[0]) : 'default.jpg';
$remaining_images = array_slice($images, 1);

// Amenities/Features/Policies Handling
$amenities = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
$features = !empty($room['features']) ? explode(',', $room['features']) : [];
$policies = !empty($room['policies']) ? explode(',', $room['policies']) : [];



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($room['name']); ?> Details</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/navbar.css">
    <link rel="stylesheet" href="./assets/css/rooms.css">

    <style>
    /* Specific Styles for View_Details Page */
    .details-container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 15px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
    }

    .main-content h1 {
        color: #5a4636;
        margin-bottom: 10px;
    }

    .main-content .rating {
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .image-gallery {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 30px;
    }

    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 12px;
    }

    .thumbnail-images {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 10px;
    }

    .thumbnail-images img {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.2s;
    }

    .thumbnail-images img:hover,
    .thumbnail-images img.active {
        border-color: #f1c45f;
    }

    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        justify-content: center;
        align-items: center;
    }

    .image-modal img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 12px;
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 35px;
        font-size: 40px;
        color: white;
        cursor: pointer;
    }

    .nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 60px;
        color: white;
        cursor: pointer;
        user-select: none;
    }

    .prev-btn {
        left: 30px;
    }

    .next-btn {
        right: 30px;
    }


    .detail-box {
        background: #fff;
        border: 1px solid #f5e6cc;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .detail-box h3 {
        color: #b58900;
        margin-top: 0;
        margin-bottom: 15px;
        border-bottom: 1px solid #f5e6cc;
        padding-bottom: 8px;
    }

    .detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .detail-list li {
        margin-bottom: 10px;
        font-size: 1rem;
        color: #444;
        display: flex;
        align-items: center;
    }

    .detail-list li strong {
        color: #5a4636;
        min-width: 150px;
        font-weight: 600;
    }

    .price-section {
        background: #fdfaf6;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        border: 2px solid #f1c45f;
    }

    .price-section .current-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #b58900;
        margin: 0;
    }

    .price-section .original-price {
        font-size: 1.2rem;
        color: #888;
        text-decoration: line-through;
        margin-bottom: 15px;
    }

    .book-button {
        display: block;
        width: 100%;
        background: #f1c45f;
        color: white;
        padding: 15px;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.3s;
    }

    .book-button:hover {
        background: #d4a93d;
    }

    .tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 15px;
    }

    .tag-list .tag {
        background: #f5e6cc;
        color: #5a4636;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Physical Rooms Display */
    .physical-rooms h4 {
        margin-top: 0;
        color: #444;
    }

    .physical-rooms-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .room-number-tag {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
    }

    .room-number-tag.Available {
        background-color: #e6ffe6;
        /* Light Green */
        color: #0a662e;
        /* Dark Green */
        border-color: #a3e6a3;
    }

    .room-number-tag.Occupied {
        background-color: #ffe6e6;
        /* Light Red */
        color: #cc0000;
        /* Dark Red */
        border-color: #ff9999;
    }

    @media (max-width: 992px) {
        .details-container {
            grid-template-columns: 1fr;
        }

        .main-image {
            height: 300px;
        }

        .sidebar {
            order: -1;
            /* Move price/booking to top on mobile */
        }
    }
    </style>
</head>

<body>

    <div class="details-container">
        <div class="main-content">
            <h1 class="room-title"><?php echo htmlspecialchars($room['name']); ?></h1>
            <div class="rating">
                ⭐ <?php echo htmlspecialchars($room['rating'] ?? 'N/A'); ?> </div>

            <div class="image-gallery">
                <img id="main-room-image" src="uploads/<?php echo htmlspecialchars($first_image); ?>"
                    alt="<?php echo htmlspecialchars($room['name']); ?>" class="main-image">

                <?php if (!empty($remaining_images)): ?>
                <div class="thumbnail-images">
                    <img src="uploads/<?php echo htmlspecialchars($first_image); ?>"
                        data-src="uploads/<?php echo htmlspecialchars($first_image); ?>" class="active" alt="Thumbnail">
                    <?php foreach ($remaining_images as $img): 
                    $safe_img = htmlspecialchars(trim($img));
                    if (!empty($safe_img)):
                ?>
                    <img src="uploads/<?php echo $safe_img; ?>" data-src="uploads/<?php echo $safe_img; ?>"
                        alt="Thumbnail">
                    <?php endif; endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="detail-box">
                <h3>Description</h3>
                <p><?php echo htmlspecialchars($room['description']); ?></p>
            </div>

            <div class="detail-box">
                <h3>Key Details</h3>
                <ul class="detail-list">
                    <li><strong>Size:</strong> <?php echo htmlspecialchars($room['size'] ?? 'N/A'); ?></li>
                    <li><strong>Bed Type:</strong> <?php echo htmlspecialchars($room['bed_type'] ?? 'N/A'); ?></li>
                    <li><strong>Max Guests:</strong> <?php echo htmlspecialchars($room['guests'] ?? 'N/A'); ?></li>
                    <li><strong>Floor:</strong> <?php echo htmlspecialchars($room['floor'] ?? 'N/A'); ?></li>
                    <li><strong>AC Status:</strong> <span
                            style="font-weight: 600; color: <?php echo $room['ac_status'] == 'AC' ? '#0a7d5f' : '#8a6642'; ?>;"><?php echo htmlspecialchars($room['ac_status'] ?? 'N/A'); ?></span>
                    </li>
                </ul>
            </div>

            <?php
// --- ADD THIS HELPER FUNCTION AT THE TOP OF YOUR PHP ---
function getIcon($text) {
    $text = strtolower(trim($text));
    
    // Icon Mapping for Amenities & Features
    if (str_contains($text, 'wifi')) return '📶';
    if (str_contains($text, 'ac') || str_contains($text, 'air')) return '❄️';
    if (str_contains($text, 'tv') || str_contains($text, 'television')) return '📺';
    if (str_contains($text, 'water')) return '🚰';
    if (str_contains($text, 'parking')) return '🅿️';
    if (str_contains($text, 'breakfast') || str_contains($text, 'food')) return '☕';
    if (str_contains($text, 'bed')) return '🛏️';
    if (str_contains($text, 'bath') || str_contains($text, 'shower')) return '🚿';
    if (str_contains($text, 'service')) return '🛎️';
    
    // Icon Mapping for Policies
    if (str_contains($text, 'check-in')) return '🔑';
    if (str_contains($text, 'check-out')) return '🚪';
    if (str_contains($text, 'smoke') || str_contains($text, 'smoking')) return '🚭';
    if (str_contains($text, 'pet')) return '🐾';
    if (str_contains($text, 'id') || str_contains($text, 'proof')) return '🪪';
    if (str_contains($text, 'cancel')) return '📅';

    // Default icon if no match found
    return '🔹'; 
}
?>

            <div class="detail-box">
                <h3>Amenities</h3>
                <?php if (!empty($amenities[0])): ?>
                <div class="tag-list">
                    <?php foreach($amenities as $amenity): 
                    $clean_amenity = trim($amenity);
                ?>
                    <span class="tag"><?php echo getIcon($clean_amenity); ?>
                        <?php echo htmlspecialchars($clean_amenity); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p>No amenities listed.</p>
                <?php endif; ?>
            </div>

            <div class="detail-box">
                <h3>Room Features</h3>
                <?php if (!empty($features[0])): ?>
                <div class="tag-list">
                    <?php foreach($features as $feature): 
                    $clean_feature = trim($feature);
                ?>
                    <span class="tag"><?php echo getIcon($clean_feature); ?>
                        <?php echo htmlspecialchars($clean_feature); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p>No special features listed.</p>
                <?php endif; ?>
            </div>

            <div class="detail-box">
                <h3>Hotel & Room Policy</h3>
                <?php if (!empty($policies[0])): ?>
                <ul class="detail-list">
                    <?php foreach($policies as $policy): 
                    $clean_policy = trim($policy);
                ?>
                    <li><?php echo getIcon($clean_policy); ?> &nbsp; <?php echo htmlspecialchars($clean_policy); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>No policies listed.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sidebar">
            <div class="detail-box price-section">
                <p class="original-price">Total Price: ₹<?php echo htmlspecialchars(number_format($room['price'] )); ?>
                </p>

                <p class="current-price">₹<?php echo htmlspecialchars(number_format($room['discount_price'])); ?></p>
                <small><b style="color:gray;">Per Night</b></small>

                <hr style="margin: 15px 0;">
                <p style="font-size: 1rem; color: #5a4636;">
                    Extra Bed Charge: ₹<?php echo htmlspecialchars(number_format($room['extra_bed_price'])); ?>
                </p>
                <hr style="margin: 15px 0 25px;">

                <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="book-button">Book Now</a>
            </div>

            <!-- <div class="detail-box physical-rooms">
                <h3>Availability Status</h3>

                <?php if ($available_count > 0): ?>
                <div class="room-count-display"
                    style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                    
            <div>
                <span style="font-size: 1.5rem; font-weight: 700; color: #5a4636;">
                    <?php echo $available_count; ?>
                </span>
                <span style="font-size: 1rem; color: #666; margin-left: 5px;">
                    Room<?php echo ($available_count > 1) ? 's' : ''; ?> currently available
                </span>
            </div>
        </div>

        <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-calendar-times" style="font-size: 2rem; color: #dc3545;"></i>
                <div>
                    <span style="font-size: 1.2rem; font-weight: 700; color: #dc3545;">Fully Occupied</span>
                    <p style="font-size: 0.9rem; color: #666; margin: 0;">No rooms available today.</p>
                </div>
            </div>

            <?php if ($next_available_date): ?>
            <div
                style="background: #fff5f5; border-left: 4px solid #dc3545; padding: 10px 15px; border-radius: 4px; margin-top: 5px;">
                <p style="margin: 0; font-size: 0.95rem; color: #444;">
                    <i class="fas fa-calendar-alt" style="color: #dc3545; margin-right: 8px;"></i>
                    Next Expected Availability:
                    <strong><?php echo date('d M, Y', strtotime($next_available_date)); ?></strong>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div> -->

            <div class="detail-box availability-card"
                style="border-top: 5px solid <?= ($available_count > 0) ? '#28a745' : '#dc3545' ?>;">
                <h3 style="color: #5a4636;"><i class="fas fa-calendar-alt"></i> Check Availability</h3>

                <form action="View_Details.php" method="GET" class="mb-4">
                    <input type="hidden" name="id" value="<?= $room_id ?>">
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <label class="small text-muted">Check-in</label>
                            <input type="date" id="checkin" name="checkin" value="<?= $checkin ?>"
                                class="form-control form-control-sm" required>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="small text-muted">Check-out</label>
                            <input type="date" id="checkout" name="checkout" value="<?= $checkout ?>"
                                class="form-control form-control-sm" required>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-outline-dark w-100">Update Availability</button>
                        </div>
                    </div>
                </form>

                <hr>

                <?php if ($available_count > 0): ?>
                <div class="text-center">
                    <div class="badge bg-success mb-2 p-2"><?= $available_count ?> Room(s) Available</div>
                    <p class="small text-muted">For: <?= date('d M', strtotime($checkin)) ?> to
                        <?= date('d M', strtotime($checkout)) ?></p>
                </div>
                <?php else: ?>
                <div class="text-center text-danger">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h5>Sold Out!</h5>
                    <p class="small text-muted">Selected dates are fully booked. Please try different dates above.</p>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <div id="imageModal" class="image-modal">
        <span class="close-btn">&times;</span>
        <span class="nav-btn prev-btn">&#10094;</span>
        <img id="modalImage">
        <span class="nav-btn next-btn">&#10095;</span>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    document.getElementById("checkin").addEventListener("change", function() {
        const checkinDate = new Date(this.value);

        // add 1 day
        checkinDate.setDate(checkinDate.getDate() + 1);

        const minCheckout = checkinDate.toISOString().split('T')[0];

        const checkoutInput = document.getElementById("checkout");
        checkoutInput.min = minCheckout; // disable previous dates
        checkoutInput.value = minCheckout; // auto set next day
    });
    </script>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainImage = document.getElementById('main-room-image');
        const thumbnails = document.querySelectorAll('.thumbnail-images img');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Remove active class from all thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));

                // Set clicked thumbnail as active
                this.classList.add('active');

                // Change the main image source
                mainImage.src = this.getAttribute('data-src');
            });
        });
    });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {

        const galleryImages = document.querySelectorAll("#main-room-image, .thumbnail-images img");
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        const closeBtn = document.querySelector(".close-btn");
        const nextBtn = document.querySelector(".next-btn");
        const prevBtn = document.querySelector(".prev-btn");

        let images = [];
        galleryImages.forEach(img => images.push(img.src));

        let currentIndex = 0;

        galleryImages.forEach((img, index) => {
            img.addEventListener("click", () => {
                currentIndex = index;
                modal.style.display = "flex";
                modalImg.src = images[currentIndex];
            });
        });

        closeBtn.onclick = () => modal.style.display = "none";
        nextBtn.onclick = () => changeImage(1);
        prevBtn.onclick = () => changeImage(-1);

        function changeImage(step) {
            currentIndex = (currentIndex + step + images.length) % images.length;
            modalImg.src = images[currentIndex];
        }

        document.addEventListener("keydown", function(e) {
            if (modal.style.display === "flex") {
                if (e.key === "Escape") modal.style.display = "none";
                if (e.key === "ArrowRight") changeImage(1);
                if (e.key === "ArrowLeft") changeImage(-1);
            }
        });
    });
    </script>


</body>

</html>