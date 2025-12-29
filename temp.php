<?php
include 'db.php';  // your DB connection file
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Test query error
$test = $conn->query("SELECT * FROM bookings");

if (!$test) {
    die("BOOKING QUERY ERROR → " . $conn->error);
} else {
    echo "Bookings found: " . $test->num_rows . "<br>";
}


// ---------- Total Bookings ----------
$totalBookings = 0;
$sql = "SELECT COUNT(*) as total FROM bookings";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalBookings = $row['total'];
}

// ---------- Rooms ----------
$totalRooms = 0;
$availableRooms = 0;

// Total rooms
$sql = "SELECT COUNT(*) as total FROM rooms";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalRooms = $row['total'];
}

// Available rooms (not booked today)
$today = date("Y-m-d");
$sql = "SELECT COUNT(*) as available 
        FROM rooms r
        WHERE r.id NOT IN (
            SELECT b.room_id FROM bookings b
            WHERE (b.checkin <= '$today' AND b.checkout >= '$today')
        )";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $availableRooms = $row['available'];
}

// ---------- Revenue This Month ----------
$currentMonth = date('m');
$currentYear = date('Y');

$sql = "SELECT SUM(total_price) as totalRevenue 
        FROM bookings 
        WHERE MONTH(created_at) = $currentMonth 
        AND YEAR(created_at) = $currentYear";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$revenueThisMonth = $row['totalRevenue'] ?? 0;

// ---------- Occupancy Rate ----------
$occupancyRate = 0;
if ($totalRooms > 0) {
    $occupiedRooms = $totalRooms - $availableRooms;
    $occupancyRate = round(($occupiedRooms / $totalRooms) * 100, 2);
}

// ---------- Monthly Revenue (last 6 months) ----------
$revenueData = [];
$monthLabels = [];
$sql = "SELECT DATE_FORMAT(checkin, '%Y-%m') as month, SUM(total_price) as revenue
        FROM bookings
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $monthLabels[] = $row['month'];
        $revenueData[] = $row['revenue'];
    }
}
// $monthLabels = array_reverse($monthLabels);
// $revenueData = array_reverse($revenueData);

// // ---------- Booking Trends (last 6 months) ----------
// $bookingData = [];
// $sql = "SELECT DATE_FORMAT(checkin, '%Y-%m') as month, COUNT(*) as total_bookings
//         FROM bookings
//         GROUP BY month
//         ORDER BY month DESC
//         LIMIT 6";
// $res = $conn->query($sql);
// if ($res) {
//     while ($row = $res->fetch_assoc()) {
//         $bookingData[] = $row['total_bookings'];
//     }
// }
// $bookingData = array_reverse($bookingData);


// ✅ Fetch room stats
$totalRooms = 0;
$available = 0;
$occupied = 0;
$maintenance = 0; // you can extend later if you add maintenance status

// Total rooms
$sql = "SELECT COUNT(*) as total FROM rooms";
$res = $conn->query($sql);
if ($res && $row = $res->fetch_assoc()) {
    $totalRooms = $row['total'];
}

// Available rooms
$sql = "SELECT COUNT(*) as total FROM rooms WHERE status = 'Available'";
$res = $conn->query($sql);
if ($res && $row = $res->fetch_assoc()) {
    $available = $row['total'];
}

// Occupied rooms
$sql = "SELECT COUNT(*) as total FROM rooms WHERE status = 'Occupied'";
$res = $conn->query($sql);
if ($res && $row = $res->fetch_assoc()) {
    $occupied = $row['total'];
}

// (Optional) Maintenance rooms
$sql = "SELECT COUNT(*) as total FROM rooms WHERE status = 'Maintenance'";
$res = $conn->query($sql);
if ($res && $row = $res->fetch_assoc()) {
    $maintenance = $row['total'];
}

// ✅ Fetch rooms for inventory table
$roomsData = [];
$sql = "SELECT * FROM rooms";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $roomsData[] = $row;
    }
}


include 'db.php';

// --------- BOOKING STATS ---------
$totalBookings = 0;
$confirmed = $checkedIn = $pending = $cancelled = 0;

$statsQuery = $conn->query("SELECT status, COUNT(*) AS count FROM bookings GROUP BY status");
while ($row = $statsQuery->fetch_assoc()) {
    $totalBookings += $row['count'];
    switch ($row['status']) {
        case 'Confirmed':   $confirmed = $row['count']; break;
        case 'Checked-in':  $checkedIn = $row['count']; break;
        case 'Pending':     $pending = $row['count']; break;
        case 'Cancelled':   $cancelled = $row['count']; break;
    }
}

// --------- BOOKING LIST ---------
$bookingsQuery = $conn->query("
    SELECT 
        b.id AS booking_id,
        b.customer_name,
        b.phone AS customer_phone,
        r.name AS room_name,
        b.checkin,
        b.checkout,
        b.guests,
        b.total_price,
        b.status,
        b.payment_status
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    ORDER BY b.id DESC
");

// --------- CUSTOMERS LIST ---------
$customerQuery = $conn->query("SELECT * FROM users ORDER BY customer_id DESC");









// First, update VIP status dynamically
$conn->query("UPDATE users SET status='VIP' WHERE total_spent >= 50000");
$conn->query("UPDATE users SET status='ACTIVE' WHERE total_spent < 19000 AND status!='INACTIVE'");

// Stats
$totalCustomers = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'];
$activeCustomers = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE status='ACTIVE'")->fetch_assoc()['cnt'];
$vipCustomers = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE status='VIP'")->fetch_assoc()['cnt'];
$avgLifetimeValue = $conn->query("SELECT AVG(total_spent) AS avgVal FROM users")->fetch_assoc()['avgVal'];

// Fetch all customers
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>























<!-- html code -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shakti Bhuvan - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">

    <style>
    /* Ensure hidden sections are not visible */
    .hidden {
        display: none !important;
    }

    .section {
        display: none;
    }

    /* Default hidden */
    .section.active {
        display: block;
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Shakti Bhuvan <span>Admin Panel</span></h2>
        <ul>
            <li class="menu-item active" data-section="dashboard">Dashboard</li>
            <li class="menu-item" data-section="rooms">Manage Rooms</li>
            <!-- <li class="menu-item" data-section="bookings">Bookings</li> -->
            <!-- <li class="menu-item" data-section="customers">Customers</li> -->
            <!-- <li class="menu-item" data-section="payments">Payments</li> -->
            <li class="menu-item" data-section="forms">Forms</li>
            <!-- <li class="menu-item" data-section="reviews">Reviews</li> -->
            <!-- <li class="menu-item" data-section="settings">Settings</li> -->
        </ul>
        <a href="admin.php" class="logout">⏻ Logout</a>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <!-- Dashboard Section -->
        <div class="topbar">
            <input type="text" placeholder="Search bookings, rooms, customers...">
            <!-- <div class="icons">
                <span>🔔</span>
                <span>⚙️</span>
                <div class="profile">AD</div>
            </div> -->
        </div>
        <section id="forms" class="section container my-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Manage Website Content</h2>
                <p class="text-muted">Choose an option below to add new content to your website.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Add Slider Button -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100 text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-images fs-1 text-primary"></i>
                        </div>
                        <h5 class="card-title">Add Slider</h5>
                        <p class="card-text text-muted">Upload new slider images with titles and descriptions.</p>
                        <a href="add_slider.php" class="btn btn-primary w-100">Go to Add Slider</a>
                    </div>
                </div>

                <!-- Add Room Button -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100 text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-house-door-fill fs-1 text-success"></i>
                        </div>
                        <h5 class="card-title">Add Room</h5>
                        <p class="card-text text-muted">Add room details like type, price, and availability.</p>
                        <a href="admin_add_room.php" class="btn btn-success w-100">Go to Add Room</a>
                    </div>
                </div>

                <!-- Add Gallery Image Button -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100 text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-collection fs-1 text-warning"></i>
                        </div>
                        <h5 class="card-title">Add Gallery Images</h5>
                        <p class="card-text text-muted">Upload and manage images for the gallery.</p>
                        <a href="admin_gallery.php" class="btn btn-warning w-100">Go to Add Gallery</a>
                    </div>
                </div>
            </div>
        </section>


        <section id="dashboard" class="section active">

            <h1>Dashboard</h1>
            <p class="subtitle">Welcome back to Shakti Bhuvan admin panel</p>

            <div class="container my-5">
                <h2 class="mb-4 text-center">📊 Admin Dashboard</h2>
                <div class="row g-4">

                    <div class="row cards g-4">
                        <!-- Total Bookings -->
                        <div class="col-md-3">
                            <div class="card">
                                <h3>Total Bookings</h3>
                                <p class="value"><?= $totalBookings ?></p>
                                <span class="note">All-time</span>
                            </div>
                        </div>

                        <!-- Available Rooms -->
                        <div class="col-md-3">
                            <div class="card">
                                <h3>Available Rooms</h3>
                                <p class="value"><?= $availableRooms ?></p>
                                <span class="note">Out of <?= $totalRooms ?> total rooms</span>
                            </div>
                        </div>

                        <!-- Revenue This Month -->
                        <div class="col-md-3">
                            <div class="card">
                                <h3>Revenue This Month</h3>
                                <p class="value">₹<?= number_format($revenueThisMonth, 2) ?></p>
                                <span class="note"><?= date("F Y") ?></span>
                            </div>
                        </div>

                        <!-- Occupancy Rate -->
                        <div class="col-md-3">
                            <div class="card">
                                <h3>Occupancy Rate</h3>
                                <p class="value"><?= $occupancyRate ?>%</p>
                                <span class="note">Based on <?= $totalRooms ?> rooms</span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="charts container my-5">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- <div class="chart-box p-3 bg-white shadow rounded">
                                <h3>Monthly Revenue</h3>
                                <canvas id="revenueChart"></canvas>
                            </div> -->
                        </div>
                        <!-- <div class="col-md-6">
      <div class="chart-box p-3 bg-white shadow rounded">
        <h3>Booking Trends</h3>
        <canvas id="bookingChart"></canvas>
      </div>
    </div> -->
                    </div>
                </div>
        </section>


        <!-- ✅ Manage Rooms Section -->
        <section id="rooms" class="section container my-5">
            <h1>Manage Rooms</h1>
            <p class="subtitle">Add, edit, and manage your room inventory</p>

            <div class="row cards g-4 mb-5">
                <!-- Total Rooms -->
                <div class="col-md-3">
                    <div class="card p-3 shadow text-center">
                        <p class="value fs-3 fw-bold"><?= $totalRooms ?></p>
                        <h3>Total Rooms</h3>
                    </div>
                </div>

                <!-- Available Rooms -->
                <div class="col-md-3">
                    <div class="card p-3 shadow text-center">
                        <p class="value fs-3 fw-bold text-success"><?= $available ?></p>
                        <h3>Available</h3>
                    </div>
                </div>

                <!-- Occupied Rooms -->
                <div class="col-md-3">
                    <div class="card p-3 shadow text-center">
                        <p class="value fs-3 fw-bold text-warning"><?= $occupied ?></p>
                        <h3>Occupied</h3>
                    </div>
                </div>


            </div>

            <!-- ✅ Room Inventory Table -->
            <div class="card shadow border-0 rounded p-4">
                <h2 class="mb-4">Room Inventory</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Room ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Price/Night</th>
                                <th>Capacity</th>
                                <th>Amenities</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($roomsData)): ?>
                            <?php foreach ($roomsData as $room): ?>
                            <tr id="roomRow<?= $room['id'] ?>">
                                <td>RM<?= str_pad($room['id'], 3, "0", STR_PAD_LEFT) ?></td>
                                <td class="room-name"><?= htmlspecialchars($room['name']) ?></td>
                                <td class="room-type"><?= $room['bed_type'] ?></td>
                                <td class="room-price">₹<?= number_format($room['price'], 2) ?></td>
                                <td class="room-capacity"><?= $room['guests'] ?> Guests</td>
                                <td class="room-amenities">
                                    <?php 
                        $amenities = explode(",", $room['amenities']);
                        foreach ($amenities as $a) {
                            echo '<span class="badge bg-light text-dark me-1">'.trim($a).'</span>';
                        }
                        ?>
                                </td>
                                <td class="room-status">
                                    <?php if ($room['status'] == 'Available'): ?>
                                    <span class="badge bg-success">Available</span>
                                    <?php elseif ($room['status'] == 'Occupied'): ?>
                                    <span class="badge bg-warning text-dark">Occupied</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Maintenance</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="dropdown text-end">
                                        <button class="btn btn-light btn-sm rounded-circle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item" href="view_details.php?id=<?= $room['id'] ?>">
                                                    <i class="bi bi-eye me-2"></i> View Details
                                                </a>
                                            </li>
                                            <li>
                                                <!-- ✅ Open Modal Instead of New Page -->
                                                <button class="dropdown-item editRoomBtn" data-id="<?= $room['id'] ?>"
                                                    data-name="<?= htmlspecialchars($room['name']) ?>"
                                                    data-type="<?= $room['bed_type'] ?>"
                                                    data-price="<?= $room['price'] ?>"
                                                    data-capacity="<?= $room['guests'] ?>"
                                                    data-amenities="<?= $room['amenities'] ?>"
                                                    data-status="<?= $room['status'] ?>"
                                                    data-image="<?= $room['image'] ? 'uploads/'.$room['image'] : '' ?>">
                                                    <i class="bi bi-pencil-square me-2"></i> Edit Room
                                                </button>

                                            </li>
                                            <li>
                                                <form action="delete_room.php" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this room?');">
                                                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Delete Room
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No rooms found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ✅ Edit Room Modal -->
            <div class="modal fade" id="editRoomModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="editRoomForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Room</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="room_id" id="editRoomId">

                                <div class="mb-3">
                                    <label class="form-label">Room Name</label>
                                    <input type="text" class="form-control" name="name" id="editRoomName" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bed Type</label>
                                    <input type="text" class="form-control" name="type" id="editRoomType" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Price/Night</label>
                                    <input type="number" class="form-control" name="price" id="editRoomPrice" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" class="form-control" name="capacity" id="editRoomCapacity"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Amenities (comma separated)</label>
                                    <input type="text" class="form-control" name="amenities" id="editRoomAmenities">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" id="editRoomStatus">
                                        <option value="Available">Available</option>
                                        <option value="Occupied">Occupied</option>
                                        <option value="Maintenance">Maintenance</option>
                                    </select>
                                </div>

                                <!-- ✅ Room Image Section -->
                                <div class="mb-3">
                                    <label class="form-label">Room Image</label>
                                    <div id="currentRoomImageContainer" style="text-align:center;">
                                        <img id="currentRoomImage" src="" alt="Room Image"
                                            style="width: 200px; border-radius:10px; display:none; margin-bottom:10px;">
                                        <div>
                                            <button type="button" id="deleteImageBtn" class="btn btn-danger btn-sm"
                                                style="display:none;">Delete Image</button>
                                        </div>
                                    </div>

                                    <!-- <input type="file" name="image" id="editRoomImage" class="form-control mt-3">
                        <small class="text-muted">Upload a new image to replace the current one</small> -->
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ✅ JavaScript -->
            <script>
            document.addEventListener("DOMContentLoaded", () => {
                const editBtns = document.querySelectorAll(".editRoomBtn");
                const modal = new bootstrap.Modal(document.getElementById("editRoomModal"));
                const deleteImageBtn = document.getElementById("deleteImageBtn");
                const image = document.getElementById("currentRoomImage");

                editBtns.forEach(btn => {
                    btn.addEventListener("click", () => {
                        document.getElementById("editRoomId").value = btn.dataset.id;
                        document.getElementById("editRoomName").value = btn.dataset.name;
                        document.getElementById("editRoomType").value = btn.dataset.type;
                        document.getElementById("editRoomPrice").value = btn.dataset.price;
                        document.getElementById("editRoomCapacity").value = btn.dataset
                            .capacity;
                        document.getElementById("editRoomAmenities").value = btn.dataset
                            .amenities;
                        document.getElementById("editRoomStatus").value = btn.dataset.status;

                        if (btn.dataset.image) {
                            let imgPath = btn.dataset.image.replace(/^\.?\//,
                                ""); // remove ./ or /
                            document.getElementById("currentRoomImage").src = imgPath;
                            document.getElementById("currentRoomImage").style.display = "block";
                            deleteImageBtn.style.display = "inline-block";
                        } else {
                            document.getElementById("currentRoomImage").style.display = "none";
                            deleteImageBtn.style.display = "none";
                        }

                        modal.show();
                    });
                });

                // ✅ Delete image
                deleteImageBtn.addEventListener("click", () => {
                    const roomId = document.getElementById("editRoomId").value;
                    if (confirm("Are you sure you want to delete this image?")) {
                        fetch("update_room.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: new URLSearchParams({
                                    action: "delete_image",
                                    room_id: roomId
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert("Image deleted successfully!");
                                    image.style.display = "none";
                                    deleteImageBtn.style.display = "none";
                                } else {
                                    alert("Error deleting image: " + data.message);
                                }
                            });
                    }
                });

                // ✅ Update room (and refresh image dynamically)
                document.getElementById("editRoomForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    fetch("update_room.php", {
                            method: "POST",
                            body: new FormData(this)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert("Room updated successfully!");
                                if (data.image) {
                                    image.src = data.image;
                                    image.style.display = "block";
                                    deleteImageBtn.style.display = "inline-block";
                                }
                                // You can reload only if you want to refresh room list:
                                // location.reload();
                            } else {
                                alert("Error: " + data.message);
                            }
                        })
                        .catch(err => console.error("Fetch error:", err));
                });
            });
            </script>



            <section id="bookings" class="section">
    <h1>Bookings Management</h1>
    <p class="subtitle">Manage all customer bookings and reservations</p>

    <!-- Booking Stats -->
    <div class="cards">
        <div class="card"><p class="value"><?= $totalBookings ?></p><h3>Total Bookings</h3></div>
        <div class="card"><p class="value text-success"><?= $confirmed ?></p><h3>Confirmed</h3></div>
        <div class="card"><p class="value text-primary"><?= $checkedIn ?></p><h3>Checked In</h3></div>
        <div class="card"><p class="value text-warning"><?= $pending ?></p><h3>Pending</h3></div>
        <div class="card"><p class="value text-danger"><?= $cancelled ?></p><h3>Cancelled</h3></div>
    </div>

    <!-- Bookings Table -->
    <div class="table-box">
        <h3>All Bookings</h3>

        <table id="bookingsTable" class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($bookingsQuery->num_rows > 0): ?>
                <?php while ($row = $bookingsQuery->fetch_assoc()): ?>

                <?php
                $statusClass = match ($row['status']) {
                    'Confirmed' => "bg-success text-white",
                    'Checked-in' => "bg-primary text-white",
                    'Pending' => "bg-warning text-dark",
                    'Cancelled' => "bg-danger text-white",
                    default => "bg-secondary text-white"
                };
                ?>

                <tr>
                    <td>BK<?= str_pad($row['booking_id'], 3, "0", STR_PAD_LEFT) ?></td>

                    <td>
                        <?= htmlspecialchars($row['customer_name']) ?><br>
                        <small><?= htmlspecialchars($row['customer_phone']) ?></small>
                    </td>

                    <td><?= htmlspecialchars($row['room_name']) ?></td>
                    <td><?= $row['checkin'] ?> → <?= $row['checkout'] ?></td>
                    <td><?= $row['guests'] ?></td>
                    <td>₹<?= number_format($row['total_price'], 2) ?></td>

                    <td><span class="badge <?= $statusClass ?>"><?= $row['status'] ?></span></td>

                    <td>
                        <span class="badge <?= $row['payment_status'] === 'paid' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($row['payment_status']) ?>
                        </span>
                    </td>

                    <td>
                        <a href="view_booking.php?id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-info">View</a>
                        <a href="edit_booking.php?id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="delete_booking.php" style="display:inline-block;">
                            <input type="hidden" name="id" value="<?= $row['booking_id'] ?>">
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete booking?')">Delete</button>
                        </form>
                    </td>
                </tr>

                <?php endwhile; ?>
                <?php else: ?>
                <tr><td colspan="9" class="text-center">No bookings found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>






            <!-- Customers Section -->
            <section id="customers" class="section">
    <h1>Customer Management</h1>
    <p class="subtitle">Manage all customer information</p>

    <div class="table-box">
        <h3>All Customers</h3>

        <table id="customerTable" class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th>Bookings</th>
                    <th>Total Spent</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($customerQuery->num_rows > 0): ?>
                <?php while ($row = $customerQuery->fetch_assoc()): ?>

                <tr>
                    <td><?= $row['customer_id'] ?></td>

                    <td>
                        <?= htmlspecialchars($row['name']) ?><br>
                        <small>Since <?= $row['member_since'] ?></small>
                    </td>

                    <td>
                        <?= $row['email'] ?><br>
                        <small><?= $row['phone'] ?></small>
                    </td>

                    <td><?= $row['location'] ?></td>
                    <td><?= $row['bookings'] ?></td>
                    <td>₹<?= number_format($row['total_spent'], 2) ?></td>

                    <td>
                        <span class="badge 
                            <?= $row['status'] === 'VIP' ? 'bg-warning' : 
                               ($row['status'] === 'ACTIVE' ? 'bg-success' : 'bg-secondary') ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>

                    <td>
                        <a href="view_customer.php?id=<?= $row['customer_id'] ?>" class="btn btn-sm btn-info">View</a>
                        <a href="edit_customer.php?id=<?= $row['customer_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="delete_customer.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['customer_id'] ?>">
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete customer?')">Delete</button>
                        </form>
                    </td>
                </tr>

                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>


            <script>
            // Search filter
            document.getElementById("searchInput").addEventListener("keyup", function() {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll("#customerTable tr");
                rows.forEach((row, index) => {
                    if (index === 0) return; // skip header
                    row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
                });
            });
            </script>


        </main>

    <script>
    // Sidebar navigation handling
    document.querySelectorAll(".menu-item").forEach(item => {
        item.addEventListener("click", function() {
            // Remove active class from menu items
            document.querySelectorAll(".menu-item").forEach(i => i.classList.remove("active"));
            this.classList.add("active");

            // Hide all sections
            document.querySelectorAll(".section").forEach(sec => {
                sec.classList.remove("active");
                sec.classList.add("hidden");
            });

            // Show the selected section
            let section = this.dataset.section;
            let activeSection = document.getElementById(section);
            activeSection.classList.remove("hidden");
            activeSection.classList.add("active");
        });
    });
    </script>
</body>

</html>