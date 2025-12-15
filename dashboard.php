<?php
// Include the database connection file using MySQLi
// NOTE: Make sure db.php is available in the same directory
include 'db.php'; 

// --- Helper Functions (using MySQLi) ---

/**
 * Helper function to execute a simple SELECT query and fetch a single value (e.g., for counts).
 */
function fetchSingleValue($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return $row[0];
    }
    return 0;
}

// =========================================================
//             DASHBOARD DATA FETCHING
// =========================================================

// Total Bookings
$sql_total_bookings = "SELECT COUNT(*) FROM bookings";
$total_bookings = fetchSingleValue($conn, $sql_total_bookings);

// Available Rooms
$sql_available_rooms = "SELECT COUNT(*) FROM rooms WHERE status = 'Available'";
$available_rooms = fetchSingleValue($conn, $sql_available_rooms);

// Total Rooms (for Occupancy Rate calculation)
$sql_total_rooms = "SELECT COUNT(*) FROM rooms";
$total_rooms = fetchSingleValue($conn, $sql_total_rooms);
$occupied_rooms_stats = $total_rooms - $available_rooms; // Approximation
$occupancy_rate = ($total_rooms > 0) ? round(($occupied_rooms_stats / $total_rooms) * 100, 2) : 0;

// Revenue This Month
$current_month = date('m');
$current_year = date('Y');

$sql_month_revenue = "
    SELECT SUM(total_price)
    FROM bookings
    WHERE MONTH(checkin) = '$current_month' 
    AND YEAR(checkin) = '$current_year' 
    AND (status = 'Confirmed' OR status = 'Checked-in')
";
$revenue_this_month = fetchSingleValue($conn, $sql_month_revenue) ?: 0.00;

// Recent Bookings Table
$recent_bookings = [];
$sql_recent_bookings = "
    SELECT 
        b.id, b.customer_name, b.checkin, b.checkout, b.status, r.name AS room_name
    FROM 
        bookings b
    JOIN 
        rooms r ON b.room_id = r.id
    ORDER BY 
        b.created_at DESC
    LIMIT 5
";

$result_bookings = mysqli_query($conn, $sql_recent_bookings);
if ($result_bookings) {
    while ($row = mysqli_fetch_assoc($result_bookings)) {
        $recent_bookings[] = $row;
    }
}

// Monthly Revenue Data for Chart 
$monthly_data = [];
$month_labels = [];
for ($i = 5; $i >= 0; $i--) {
    $date = new DateTime(date('Y-m-01'));
    $date->modify("-$i months");
    $month_name = $date->format('M');
    $month_num = $date->format('m');
    $year_num = $date->format('Y');

    $sql_month_sum = "
        SELECT SUM(total_price) 
        FROM bookings 
        WHERE MONTH(checkin) = '$month_num' AND YEAR(checkin) = '$year_num'
        AND (status = 'Confirmed' OR status = 'Checked-in')
    ";
    $sum = fetchSingleValue($conn, $sql_month_sum) ?: 0;
    
    $month_labels[] = $month_name;
    $monthly_data[] = $sum;
}
$monthly_data_json = json_encode($monthly_data);
$month_labels_json = json_encode($month_labels);

// Booking Trends Data for Chart
$daily_data = [];
$day_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = new DateTime(date('Y-m-d'));
    $date->modify("-$i days");
    $day_name = $date->format('D');
    $date_string = $date->format('Y-m-d');

    $sql_day_count = "
        SELECT COUNT(*) 
        FROM bookings 
        WHERE DATE(created_at) = '$date_string'
    ";
    $count = fetchSingleValue($conn, $sql_day_count);
    
    $day_labels[] = $day_name;
    $daily_data[] = $count;
}
$daily_data_json = json_encode($daily_data);
$day_labels_json = json_encode($day_labels);

// =========================================================
//             MANAGE ROOMS DATA FETCHING
// =========================================================
$sql_occupied_rooms = "SELECT COUNT(*) FROM rooms WHERE status = 'Occupied'";
$occupied_rooms = fetchSingleValue($conn, $sql_occupied_rooms);

$maintenance_rooms = 2; 

$room_inventory = [];
$sql_room_inventory = "
    SELECT 
        id, name, description, price, discount_price, size, bed_type, guests, image, amenities, features, status
    FROM 
        rooms
    ORDER BY 
        id ASC
";

$result_rooms = mysqli_query($conn, $sql_room_inventory);
if ($result_rooms) {
    while ($row = mysqli_fetch_assoc($result_rooms)) {
        $room_inventory[] = $row;
    }
}

// =========================================================
//             BOOKINGS DATA FETCHING
// =========================================================
$all_bookings = [];
$sql_all_bookings = "
    SELECT 
        b.id, b.customer_name, b.phone, b.guests AS booking_guests, b.checkin, b.checkout, b.total_price, b.status, b.payment_status,
        r.name AS room_name, r.id AS room_id
    FROM 
        bookings b
    JOIN 
        rooms r ON b.room_id = r.id
    ORDER BY 
        b.checkin DESC
";

$result_all_bookings = mysqli_query($conn, $sql_all_bookings);
if ($result_all_bookings) {
    while ($row = mysqli_fetch_assoc($result_all_bookings)) {
        $all_bookings[] = $row;
    }
}

// =========================================================
//             CUSTOMERS DATA FETCHING 
// =========================================================
$all_customers = [];
$sql_all_customers = "
    SELECT 
        customer_id, name, email, phone, location, member_since, bookings, total_spent, status
    FROM 
        users
    ORDER BY 
        member_since DESC
";

$result_all_customers = mysqli_query($conn, $sql_all_customers);
if ($result_all_customers) {
    while ($row = mysqli_fetch_assoc($result_all_customers)) {
        $all_customers[] = $row;
    }
}

// =========================================================
//             PAYMENTS DATA FETCHING
// =========================================================
$all_payments = [];
$sql_all_payments = "
    SELECT 
        p.id, p.booking_id, p.amount, p.payment_date,
        b.customer_name 
    FROM 
        payments p
    LEFT JOIN 
        bookings b ON p.booking_id = b.id 
    ORDER BY 
        p.payment_date DESC
";

$result_all_payments = mysqli_query($conn, $sql_all_payments);
if ($result_all_payments) {
    while ($row = mysqli_fetch_assoc($result_all_payments)) {
        $all_payments[] = $row;
    }
}

// =========================================================
//             SETTINGS DATA FETCHING
// =========================================================
$hero_images = [];
$sql_hero_images = "SELECT id, background_image FROM hero_section";
$result_hero_images = mysqli_query($conn, $sql_hero_images);
if ($result_hero_images) {
    while ($row = mysqli_fetch_assoc($result_hero_images)) {
        $hero_images[] = $row;
    }
}

$site_settings = [
    'phone_number' => '+91 98765 43210',
    'email_address' => 'info@shaktibhuvan.com',
    'physical_address' => 'Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110'
];

$sql_fetch_settings = "SELECT setting_key, setting_value FROM site_settings";
$result_settings = mysqli_query($conn, $sql_fetch_settings);
if ($result_settings) {
    while ($row = mysqli_fetch_assoc($result_settings)) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }
}

// =========================================================
//             GALLERY DATA FETCHING (NEW)
// =========================================================
$gallery_images = [];
$sql_gallery_images = "SELECT * FROM gallery ORDER BY id DESC";
$result_gallery = mysqli_query($conn, $sql_gallery_images);
if ($result_gallery) {
    while ($row = mysqli_fetch_assoc($result_gallery)) {
        $gallery_images[] = $row;
    }
}


// Close the MySQLi connection
mysqli_close($conn); 

// --- Helper Functions for Room Inventory ---
function getRoomTypeAndCount($name) {
    $name_lower = strtolower($name);
    if (strpos($name_lower, 'suite') !== false || strpos($name_lower, 'luxury') !== false) {
        return 'Suite';
    } elseif (strpos($name_lower, 'premium') !== false) {
        return 'Premium';
    } else {
        return 'Standard';
    }
}
function countAmenities($amenities_string) {
    if (empty($amenities_string)) return 0;
    return count(explode(',', $amenities_string));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shakti Bhuvan Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-color: #f7f3ed;
            --primary-color: #a0522d;
            --text-color: #333;
        }
        /* ... (CSS styles from previous sections remain) ... */
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #fffaf0;
            color: var(--text-color);
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-color);
            padding-top: 20px;
            border-right: 1px solid #ddd;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        .sidebar a.nav-link {
            color: var(--text-color);
            padding: 10px 20px;
            margin-bottom: 5px;
            transition: background-color 0.2s;
            cursor: pointer;
        }
        .sidebar a.nav-link.active {
            background-color: #e0d9cf;
            font-weight: bold;
        }
        .dashboard-card, .room-inventory-card, .stats-card {
            background-color: white;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .card-value {
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--text-color);
        }
        .card-title-text {
            color: #888;
            font-size: 0.9rem;
        }
        .stats-card {
            height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stats-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-color);
            line-height: 1;
        }
        .stats-label {
            color: #888;
            font-size: 0.9rem;
        }
        .room-inventory-card .table thead th {
            font-weight: normal;
            color: #999;
            text-transform: uppercase;
            font-size: 0.9rem;
            border-bottom: 1px solid #eee;
        }
        .room-inventory-card .table tbody td {
            vertical-align: middle;
            padding: 15px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .status-confirmed, .status-available { background-color: #e6ffe6; color: #008000; }
        .status-pending, .status-maintenance { background-color: #fffbe6; color: #ccaa00; }
        .status-checkedin { background-color: #e6f7ff; color: #007bff; }
        .status-checkedout { background-color: #ffcccc; color: #cc0000; }
        .status-cancelled { background-color: #f8d7da; color: #842029; }
        .status-paid { background-color: #e6ffe6; color: #008000; }
        .status-partial { background-color: #cff4fc; color: #084298; }
        .badge {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        .modal-body .btn {
            width: 100%;
            margin-bottom: 10px;
            text-align: left;
        }
        .modal-content {
            border-radius: 10px;
        }
        /* Settings Specific Styles */
        .settings-image-preview {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        .settings-image-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
        }
        /* Gallery Styles */
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-top: 20px; }
        .gallery-item { background: #fcfcfc; padding: 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center; }
        .gallery-item img { max-width: 100%; border-radius: 6px; height: 120px; object-fit: cover; }
        .delete-btn { background: #e74c3c; color: white; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="sidebar d-flex flex-column">
        <h3 class="ms-3 mb-5" style="font-family: 'Playfair Display', serif;">Shakti Bhuvan</h3>
        <nav class="nav flex-column">
            <a class="nav-link active" data-target="dashboard-section"><i class="fas fa-home me-2"></i>Dashboard</a>
            <a class="nav-link" data-target="manage-rooms-section"><i class="fas fa-key me-2"></i>Manage Rooms</a>
            <a class="nav-link" data-target="bookings-section"><i class="fas fa-calendar-alt me-2"></i>Bookings</a>
            <a class="nav-link" data-target="customers-section"><i class="fas fa-users me-2"></i>Customers</a>
            <!-- <a class="nav-link" data-target="payments-section"><i class="fas fa-credit-card me-2"></i>Payments</a> -->
            <a class="nav-link" data-target="gallery-section"><i class="fas fa-images me-2"></i>Gallery</a> <a class="nav-link" data-target="settings-section"><i class="fas fa-cog me-2"></i>Settings</a>
        </nav>
        <div class="mt-auto p-3">
            <!-- <a class="nav-link text-danger" href="admin.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a> -->
        </div>
    </div>

    <div class="main-content">
        
        <div id="dashboard-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Dashboard</h2>
                <div>
                    <i class="fas fa-bell me-3 text-muted"></i>
                    <i class="fas fa-user-circle text-muted fs-4"></i>
                </div>
            </div>
            <p class="text-muted">Welcome back to Shakti Bhuvan admin panel</p>
            
            <hr class="mt-0">

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-title-text mb-1">Total Bookings</p>
                                <h3 class="card-value"><?php echo number_format($total_bookings); ?></h3>
                            </div>
                            <i class="fas fa-calendar-check fs-3 text-muted"></i>
                        </div>
                        <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 4.2% more than last month</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-title-text mb-1">Available Rooms</p>
                                <h3 class="card-value"><?php echo number_format($available_rooms); ?></h3>
                            </div>
                            <i class="fas fa-bed fs-3 text-muted"></i>
                        </div>
                        <small class="text-muted">Out of <?php echo $total_rooms; ?> total rooms</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-title-text mb-1">Revenue This Month</p>
                                <h3 class="card-value">₹<?php echo number_format($revenue_this_month, 2); ?></h3>
                            </div>
                            <i class="fas fa-money-bill-wave fs-3 text-muted"></i>
                        </div>
                        <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 7.8% up from last month</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-title-text mb-1">Occupancy Rate</p>
                                <h3 class="card-value"><?php echo $occupancy_rate; ?>%</h3>
                            </div>
                            <i class="fas fa-chart-line fs-3 text-muted"></i>
                        </div>
                        <small class="text-danger"><i class="fas fa-arrow-down me-1"></i> 2% less than last month</small>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="dashboard-card h-100">
                        <h5 class="card-title">Monthly Revenue</h5>
                        <canvas id="monthlyRevenueChart" height="180"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dashboard-card h-100">
                        <h5 class="card-title">Booking Trends</h5>
                        <canvas id="bookingTrendsChart" height="180"></canvas>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="card-title mb-3">Recent Bookings</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer Name</th>
                                <th>Room</th>
                                <th>Check-in Date</th>
                                <th>Check-out Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_bookings) > 0): ?>
                                <?php foreach ($recent_bookings as $booking): 
                                    $booking_id_display = 'BK' . str_pad($booking['id'], 4, '0', STR_PAD_LEFT);
                                    $status_class = strtolower(str_replace([' ', '-'], '', $booking['status']));
                                    $status_badge_class = "status-{$status_class}";
                                    $numerical_id = $booking['id'];
                                ?>
                                <tr>
                                    <td><span class="fw-bold"><?php echo $booking_id_display; ?></span></td>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($booking['checkin'])); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($booking['checkout'])); ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo $status_badge_class; ?>">
                                            <?php echo htmlspecialchars($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm text-muted action-button" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#actionModal"
                                           data-record-id="<?php echo $booking_id_display; ?>"
                                           data-numerical-id="<?php echo $numerical_id; ?>"
                                           data-record-type="Booking">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted">No recent bookings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div id="manage-rooms-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Manage Rooms</h2>
                    <p class="text-muted">Add, edit, and manage your room inventory</p>
                </div>
                <a href="admin_add_room.php" class="btn btn-primary" style="background-color: var(--primary-color); border-color: var(--primary-color);"><i class="fas fa-plus me-2"></i>Add New Room</a>
            </div>
            
            <hr class="mt-0">
            
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value"><?php echo number_format($total_rooms); ?></p>
                        <p class="stats-label">Total Rooms</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value"><?php echo number_format($available_rooms); ?></p>
                        <p class="stats-label">Available</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value"><?php echo number_format($occupied_rooms); ?></p>
                        <p class="stats-label">Occupied</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value text-danger"><?php echo number_format($maintenance_rooms); ?></p>
                        <p class="stats-label">Maintenance</p>
                    </div>
                </div>
            </div>

            <div class="room-inventory-card">
                <h5 class="card-title mb-4">Room Inventory</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Price/Night</th>
                                <th>Capacity</th>
                                <th>Occupancy</th>
                                <th>Amenities</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($room_inventory) > 0): ?>
                                <?php foreach ($room_inventory as $room): 
                                    $room_id_display = 'RM' . str_pad($room['id'], 3, '0', STR_PAD_LEFT);
                                    $room_type = getRoomTypeAndCount($room['name']);
                                    $amenity_count = countAmenities($room['amenities']);
                                    $occupancy_display = "0/{$room['guests']}"; // Placeholder
                                    $status_class = strtolower(str_replace([' '], '', $room['status']));
                                    $status_badge_class = ($status_class == 'available') ? 'status-available' : 'status-maintenance';
                                    $status_display = ($status_class == 'available') ? 'Available' : 'Maintenance';
                                    $numerical_id = $room['id'];
                                ?>
                                <tr>
                                    <td><span class="fw-bold"><?php echo $room_id_display; ?></span></td>
                                    <td><?php echo htmlspecialchars($room['name']); ?></td>
                                    <td><?php echo htmlspecialchars($room_type); ?></td>
                                    <td>₹<?php echo number_format($room['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($room['guests']); ?> guests</td>
                                    <td><?php echo $occupancy_display; ?></td>
                                    <td>
                                        <i class="fas fa-wifi text-muted me-1"></i>
                                        <i class="fas fa-shower text-muted me-1"></i>
                                        <?php if ($amenity_count > 2): ?>
                                            <span class="text-muted">+<?php echo $amenity_count - 2; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo $status_badge_class; ?>">
                                            <?php echo $status_display; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm text-muted action-button"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#actionModal"
                                           data-record-id="<?php echo $room_id_display; ?>"
                                           data-numerical-id="<?php echo $numerical_id; ?>"
                                           data-record-type="Room">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="9" class="text-center text-muted">No rooms found in the inventory.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="bookings-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>All Bookings</h2>
                <a href="rooms.php" class="btn btn-success"><i class="fas fa-calendar-plus me-2"></i>New Booking</a>
            </div>
            
            <hr class="mt-0">

            <div class="dashboard-card">
                <h5 class="card-title mb-4">Booking List (Total: <?php echo count($all_bookings); ?>)</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Room</th>
                                <th>Guests</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($all_bookings) > 0): ?>
                                <?php foreach ($all_bookings as $booking): 
                                    $booking_id_display = 'BK' . str_pad($booking['id'], 4, '0', STR_PAD_LEFT);
                                    
                                    $status_class = strtolower(str_replace([' ', '-'], '', $booking['status']));
                                    $payment_class = strtolower($booking['payment_status']);
                                    
                                    $numerical_id = $booking['id'];
                                ?>
                                <tr>
                                    <td><span class="fw-bold"><?php echo $booking_id_display; ?></span></td>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['booking_guests']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($booking['checkin'])); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($booking['checkout'])); ?></td>
                                    <td>₹<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <span class="badge rounded-pill status-<?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill status-<?php echo $payment_class; ?>">
                                            <?php echo htmlspecialchars($booking['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm text-muted action-button"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#actionModal"
                                           data-record-id="<?php echo $booking_id_display; ?>"
                                           data-numerical-id="<?php echo $numerical_id; ?>"
                                           data-record-type="Booking">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="10" class="text-center text-muted">No bookings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div id="customers-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Customer Management</h2>
                <a href="#" class="btn btn-primary" style="background-color: #5cb85c; border-color: #5cb85c;"><i class="fas fa-user-plus me-2"></i>Add Customer</a>
            </div>
            
            <hr class="mt-0">

            <div class="dashboard-card">
                <h5 class="card-title mb-4">Customer List (Total: <?php echo count($all_customers); ?>)</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Member Since</th>
                                <th>Bookings</th>
                                <th>Total Spent</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($all_customers) > 0): ?>
                                <?php foreach ($all_customers as $customer): 
                                    $numerical_id = $customer['customer_id']; // Using customer_id as the unique ID
                                    $status_class = strtolower($customer['status']);
                                ?>
                                <tr>
                                    <td><span class="fw-bold"><?php echo htmlspecialchars($customer['customer_id']); ?></span></td>
                                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($customer['member_since'])); ?></td>
                                    <td><?php echo htmlspecialchars($customer['bookings']); ?></td>
                                    <td>₹<?php echo number_format($customer['total_spent'], 2); ?></td>
                                    <td>
                                        <span class="badge rounded-pill status-<?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($customer['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm text-muted action-button"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#actionModal"
                                           data-record-id="<?php echo htmlspecialchars($customer['customer_id']); ?>"
                                           data-numerical-id="<?php echo htmlspecialchars($numerical_id); ?>"
                                           data-record-type="Customer">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="9" class="text-center text-muted">No customers found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div id="payments-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Payment History</h2>
                <a href="#" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Payment</a>
            </div>
            
            <hr class="mt-0">

            <div class="dashboard-card">
                <h5 class="card-title mb-4">All Payment Records (Total: <?php echo count($all_payments); ?>)</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Booking Ref</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($all_payments) > 0): ?>
                                <?php foreach ($all_payments as $payment): 
                                    $payment_id_display = 'PM' . str_pad($payment['id'], 3, '0', STR_PAD_LEFT);
                                    $booking_ref_display = !empty($payment['booking_id']) ? 'BK' . str_pad($payment['booking_id'], 4, '0', STR_PAD_LEFT) : 'N/A';
                                    $customer_display = htmlspecialchars($payment['customer_name'] ?? 'N/A');
                                    
                                    $numerical_id = $payment['id']; 
                                ?>
                                <tr>
                                    <td><span class="fw-bold"><?php echo $payment_id_display; ?></span></td>
                                    <td><?php echo $booking_ref_display; ?></td>
                                    <td><?php echo $customer_display; ?></td>
                                    <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($payment['payment_date'])); ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm text-muted action-button"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#actionModal"
                                           data-record-id="<?php echo $payment_id_display; ?>"
                                           data-numerical-id="<?php echo $numerical_id; ?>"
                                           data-record-type="Payment">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted">No payment records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="gallery-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gallery Management</h2>
            </div>
            <hr class="mt-0">

            <div class="row g-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <h5 class="card-title mb-4"><i class="fas fa-camera me-2"></i> Gallery Image Management</h5>
                        <p class="text-muted small">Upload new images to the gallery and categorize them, or delete existing entries.</p>
                        
                        <form method="post" enctype="multipart/form-data" action="gallery_admin.php">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="image_type">Image Category:</label>
                                    <select name="image_type" id="image_type" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Hotel View">Hotel View</option>
                                        <option value="Luxury Suite">Luxury Suite</option>
                                        <option value="Deluxe Room">Deluxe Room</option>
                                        <option value="Standard Room">Standard Room</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="images">Select Images (Multiple):</label>
                                    <input type="file" name="images[]" id="images" class="form-control" multiple required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" name="upload" class="btn btn-warning w-100"><i class="fas fa-upload me-2"></i> Upload</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <h6>Uploaded Images (<?php echo count($gallery_images); ?> Total)</h6>
                        <div class="gallery">
                            <?php 
                            foreach ($gallery_images as $img): 
                                // Ensure $img['image_url'] is correctly pointing to the file path (e.g., uploads/filename.jpg)
                            ?>
                                <div class="gallery-item">
                                    <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="Gallery Image" class="img-fluid">
                                    <p class="small text-muted"><?php echo htmlspecialchars($img['image_type']); ?></p>
                                    
                                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this image?');" action="gallery_admin.php">
                                        <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                        <input type="hidden" name="delete" value="1">
                                        <button type="submit" class="btn btn-danger btn-sm w-100 delete-btn"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <style>
                            /* Inline styles for the gallery section (re-defined here for simplicity) */
                            .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-top: 20px; }
                            .gallery-item { background: #fcfcfc; padding: 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center; }
                            .gallery-item img { max-width: 100%; border-radius: 6px; height: 120px; object-fit: cover; }
                            .delete-btn { background: #e74c3c; color: white; margin-top: 5px; }
                        </style>
                    </div>
                </div>
            </div>
        </div>

        <div id="reviews-section" class="content-section" style="display: none;">
            <h2>Customer Reviews</h2>
            <p>Content for Reviews will go here...</p>
        </div>
        
        <div id="settings-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Site Settings & Configuration</h2>
            </div>
            
            <hr class="mt-0">

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="dashboard-card">
                        <h5 class="card-title mb-4"><i class="fas fa-images me-2"></i> Hero Slider Images</h5>
                        <p class="text-muted small">Manage the main background images displayed on the homepage slider.</p>
                        
                        <form method="POST" action="update_hero.php" enctype="multipart/form-data">
                            <?php foreach ($hero_images as $image): ?>
                            <div class="settings-image-container">
                                <img src="<?php echo htmlspecialchars($image['background_image']); ?>" alt="Hero Image" class="settings-image-preview">
                                <span class="text-muted small"><?php echo basename($image['background_image']); ?></span>
                                <input type="hidden" name="image_id[]" value="<?php echo $image['id']; ?>">
                                <button type="submit" name="delete_image" value="<?php echo $image['id']; ?>" class="btn btn-danger btn-sm ms-auto"
                                        onclick="return confirm('Are you sure you want to delete this image?');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                            
                            <hr>
                            <h6>Upload New Image:</h6>
                            <input type="file" name="new_image" class="form-control mb-3" accept="image/*" required>
                            <button type="submit" name="add_image" class="btn btn-primary w-100"><i class="fas fa-upload me-2"></i> Upload & Save</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="dashboard-card">
                        <h5 class="card-title mb-4"><i class="fas fa-phone-alt me-2"></i> Contact Information</h5>
                        <p class="text-muted small">Update global contact details (e.g., footer, header).</p>
                        <form method="POST" action="update_contact.php">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($site_settings['phone_number']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($site_settings['email_address']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Physical Address</label>
                                <textarea class="form-control" name="address"><?php echo htmlspecialchars($site_settings['physical_address']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-2"></i> Save Contact</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0">
                    <h5 class="modal-title" id="actionModalLabel">Actions for <span id="modal-record-id" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted small" id="modal-record-type-text"></p>
                    
                    <a href="#" class="btn btn-outline-secondary" id="action-view-link">
                        <i class="fas fa-eye me-2"></i> View Details
                    </a>
                    
                    <a href="#" class="btn btn-outline-primary" id="action-edit-link">
                        <i class="fas fa-edit me-2"></i> Edit Record
                    </a>
                    
                    <a href="#" class="btn btn-outline-danger" id="action-delete-link">
                        <i class="fas fa-trash-alt me-2"></i> Delete Record
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
            const contentSections = document.querySelectorAll('.content-section');
            const actionModal = document.getElementById('actionModal');

            // --- 1. Section Switching Logic ---
            function switchSection(targetId) {
                contentSections.forEach(section => {
                    section.style.display = 'none';
                });
                
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.style.display = 'block';
                }
                
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                });
                document.querySelector(`.sidebar .nav-link[data-target="${targetId}"]`).classList.add('active');
            }

            // Check URL parameters on load for deep linking
            const urlParams = new URLSearchParams(window.location.search);
            const initialSection = urlParams.get('section') || 'dashboard-section';
            
            // Set initial active state and display section
            if (initialSection) {
                switchSection(initialSection);
            }
            
            // Event listener for sidebar clicks
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    switchSection(targetId);
                });
            });

            // --- 2. Dynamic Modal Content Logic ---
            if (actionModal) {
                actionModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget; 
                    
                    const recordId = button.getAttribute('data-record-id');
                    const numericalId = button.getAttribute('data-numerical-id'); 
                    const recordType = button.getAttribute('data-record-type');
                    
                    // --- Truncated ID logic for View/Edit ---
                    const numericalIdLastTwo = recordId.slice(-2);

                    document.getElementById('modal-record-id').textContent = recordId;
                    document.getElementById('modal-record-type-text').textContent = `Record Type: ${recordType}`;

                    // --- Determine the ID and Script based on recordType ---
                    let editScript = 'edit.php';
                    let deleteScript = 'delete_room.php';
                    let editViewId = numericalIdLastTwo; // Default to truncated ID

                    if (recordType === 'Customer') {
                        editScript = 'edit_customer.php';
                        deleteScript = 'delete_customer.php';
                        editViewId = numericalId; // Use full string customer_id
                    } else if (recordType === 'Booking') {
                        deleteScript = 'delete_booking.php';
                    } else if (recordType === 'Payment') {
                        editScript = 'edit_payment.php';
                        deleteScript = 'delete_payment.php';
                        editViewId = numericalId; // Use full numerical ID
                    }
                    
                    // View and Edit Links
                    document.getElementById('action-view-link').href = `View_Details.php?type=${recordType}&id=${editViewId}`; 
                    document.getElementById('action-edit-link').href = `${editScript}?type=${recordType}&id=${editViewId}`;
                    
                    // Delete uses the FULL numericalId
                    const deleteId = numericalId;

                    document.getElementById('action-delete-link').onclick = function() {
                        if (confirm(`Are you sure you want to permanently delete ${recordType} ${recordId}? This action cannot be undone.`)) {
                            window.location.href = `${deleteScript}?id=${deleteId}&type=${recordType}`;
                        }
                        return false; 
                    };
                });
            }

            // --- 3. Chart Initialization (Must be inside DOMContentLoaded) ---
            
            // 1. Monthly Revenue Chart Setup
            const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart');
            new Chart(monthlyRevenueCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo $month_labels_json; ?>,
                    datasets: [{
                        label: 'Revenue (₹)',
                        data: <?php echo $monthly_data_json; ?>,
                        backgroundColor: 'rgba(160, 82, 45, 0.6)', 
                        borderColor: 'rgba(160, 82, 45, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false }, title: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
                }
            });

            // 2. Booking Trends Chart Setup
            const bookingTrendsCtx = document.getElementById('bookingTrendsChart');
            new Chart(bookingTrendsCtx, {
                type: 'line',
                data: {
                    labels: <?php echo $day_labels_json; ?>,
                    datasets: [{
                        label: 'Bookings',
                        data: <?php echo $daily_data_json; ?>,
                        borderColor: 'rgba(0, 123, 255, 1)',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false }, title: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { display: false }, ticks: { precision: 0 } }, x: { grid: { display: false } } }
                }
            });
            
        });
    </script>
</body>
</html>