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

// =========================================================
//         DATE FILTER LOGIC FOR DASHBOARD
// =========================================================

// બાયડિફોલ્ટ આજની તારીખ સેટ કરો
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date   = $_GET['end_date']   ?? date('Y-m-d');

// ૧. Total Bookings (Checked-out સિવાયના અને તારીખ મુજબ)
$sql_total_bookings = "SELECT COUNT(*) FROM bookings 
                       WHERE status != 'Checked-out' 
                       AND (checkin BETWEEN '$start_date' AND '$end_date')";
$total_bookings = fetchSingleValue($conn, $sql_total_bookings);

// ૨. Revenue (તારીખ મુજબ)
$sql_month_revenue = "SELECT SUM(total_price) FROM bookings 
                      WHERE (status = 'Confirmed' OR status = 'Checked-in') 
                      AND payment_status = 'Paid'
                      AND (checkin BETWEEN '$start_date' AND '$end_date')";
$revenue_filtered = fetchSingleValue($conn, $sql_month_revenue) ?: 0.00;

// ૩. Recent Bookings Table (માત્ર પસંદ કરેલી તારીખના જ)
$recent_bookings = [];
$sql_recent_bookings = "
    SELECT b.id, b.customer_name, b.checkin, b.checkout, b.status, r.name AS room_name
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE (b.checkin BETWEEN '$start_date' AND '$end_date')
    ORDER BY b.created_at DESC
";

$result_bookings = mysqli_query($conn, $sql_recent_bookings);
if ($result_bookings) {
    while ($row = mysqli_fetch_assoc($result_bookings)) {
        $recent_bookings[] = $row;
    }
}

// --- Dynamic Available Rooms Calculation ---
$today_date = date('Y-m-d'); // આજની તારીખ

$sql_available_now = "SELECT COUNT(*) FROM room_numbers 
                      WHERE status != 'Maintenance' 
                      AND room_number NOT IN (
                          /* ૧. અત્યારે ઓનલાઇન બુક હોય તેવા રૂમ */
                          SELECT DISTINCT room_number FROM bookings 
                          WHERE status IN ('Confirmed', 'Checked-in') 
                          AND room_number IS NOT NULL
                          AND NOT (checkout <= '$today_date' OR checkin >= '$today_date')
                      )
                      AND room_number NOT IN (
                          /* ૨. અત્યારે ઓફલાઇન બુક હોય તેવા રૂમ */
                          SELECT DISTINCT room_number FROM offline_booking 
                          WHERE NOT (checkout_date <= '$today_date' OR checkin_date >= '$today_date')
                      )";

$available_rooms = fetchSingleValue($conn, $sql_available_now);

// Total Physical Rooms (for Occupancy Rate calculation)
$sql_total_rooms_physical = "SELECT COUNT(*) FROM room_numbers";
$total_rooms_physical = fetchSingleValue($conn, $sql_total_rooms_physical);
$occupied_rooms_stats = $total_rooms_physical - $available_rooms; // Approximation
$occupancy_rate = ($total_rooms_physical > 0) ? round(($occupied_rooms_stats / $total_rooms_physical) * 100, 2) : 0;

// Revenue This Month
$current_month = date('m');
$current_year = date('Y');

$sql_month_revenue = "
    SELECT SUM(total_price)
    FROM bookings
    WHERE MONTH(checkin) = '$current_month' 
    AND YEAR(checkin) = '$current_year' 
    AND (status = 'Confirmed' OR status = 'Checked-in')
    AND payment_status = 'Paid'
";
$revenue_this_month = fetchSingleValue($conn, $sql_month_revenue) ?: 0.00;

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
//         ROOM DASHBOARD DATA FETCHING (UPDATED)
// =========================================================
$view_checkin = $_GET['dash_checkin'] ?? date('Y-m-d');
$view_checkout = $_GET['dash_checkout'] ?? date('Y-m-d', strtotime('+1 day'));

// ૧. બધા જ રૂમ નંબર્સ મેળવો
$room_dashboard_data = [];
$sql_all_rooms = "
    SELECT r.name AS type_name, rn.room_number, rn.status as current_status, rn.room_type_id
    FROM room_numbers rn
    JOIN rooms r ON rn.room_type_id = r.id
    ORDER BY r.name, rn.room_number ASC
";
$res_rooms = mysqli_query($conn, $sql_all_rooms);

// ૨. ઓનલાઇન અને ઓફલાઇન બંને બુકિંગમાંથી ઓક્યુપાઈડ રૂમ મેળવો
$occupied_rooms_in_range = [];

// UNION ક્વેરી: bookings અને offline_booking બંનેમાંથી રૂમ નંબર લેશે
// UNION ક્વેરીમાં સુધારો
$sql_booked = "
    (SELECT room_number 
     FROM bookings 
     WHERE status IN ('Confirmed', 'Checked-in') 
     AND NOT (checkout <= '$view_checkin' OR checkin >= '$view_checkout'))
    UNION
    (SELECT room_number 
     FROM offline_booking 
     WHERE NOT (checkout_date <= '$view_checkin' OR checkin_date >= '$view_checkout'))
";
// નોંધ: ઓફલાઇન બુકિંગમાં જો માત્ર ચેક-ઇન તારીખ જ હોય, તો તે મુજબ ફિલ્ટર થશે.

$res_booked = mysqli_query($conn, $sql_booked);
while($row = mysqli_fetch_assoc($res_booked)) {
    $occupied_rooms_in_range[] = $row['room_number'];
}

// ૩. ડેટા સ્ટ્રક્ચર તૈયાર કરો
if ($res_rooms) {
    while ($row = mysqli_fetch_assoc($res_rooms)) {
        // જો રૂમ ઓનલાઇન અથવા ઓફલાઇન બુકિંગમાં હોય તો 'is_occupied' true થશે
        $isBooked = in_array($row['room_number'], $occupied_rooms_in_range);
        
        $room_dashboard_data[$row['type_name']]['rooms'][] = [
            'number' => $row['room_number'],
            'is_occupied' => $isBooked,
            'type_id' => $row['room_type_id']
        ];
    }
}

// Totals ગણતરી (પહેલા જેવું જ)
foreach ($room_dashboard_data as $type => $data) {
    $total = count($data['rooms']);
    $occ_count = 0;
    foreach($data['rooms'] as $rm) if($rm['is_occupied']) $occ_count++;
    
    $room_dashboard_data[$type]['total'] = $total;
    $room_dashboard_data[$type]['occupied'] = $occ_count;
    $room_dashboard_data[$type]['available'] = $total - $occ_count;
}

// =========================================================
//      AUTO CLEANUP FOR OVERDUE OFFLINE BOOKINGS
// =========================================================

$today_now = date('Y-m-d');

// ૧. એવા રૂમ નંબર્સ શોધો જેની ચેક-આઉટ ડેટ વીતી ગઈ છે
$sql_overdue_rooms = "SELECT room_number FROM offline_booking WHERE checkout_date < '$today_now'";
$res_overdue = mysqli_query($conn, $sql_overdue_rooms);

if (mysqli_num_rows($res_overdue) > 0) {
    $overdue_room_list = [];
    while($row = mysqli_fetch_assoc($res_overdue)) {
        $overdue_room_list[] = "'" . $row['room_number'] . "'";
    }
    
    $room_numbers_str = implode(',', $overdue_room_list);

    // ૨. રૂમનું સ્ટેટસ 'Available' કરો
    mysqli_query($conn, "UPDATE room_numbers SET status = 'Available' WHERE room_number IN ($room_numbers_str)");

    // ૩. ઓવરડ્યુ બુકિંગ રેકોર્ડ્સ ડિલીટ કરો
    mysqli_query($conn, "DELETE FROM offline_booking WHERE checkout_date < '$today_now'");
}

// =========================================================
//          MANAGE ROOMS DATA FETCHING
// =========================================================
$sql_occupied_rooms = "SELECT COUNT(*) FROM room_numbers WHERE status = 'Occupied'";
$occupied_rooms = fetchSingleValue($conn, $sql_occupied_rooms);

$maintenance_rooms = 0; // Fetched from room_numbers if possible
$maintenance_rooms = fetchSingleValue($conn, "SELECT COUNT(*) FROM room_numbers WHERE status = 'Maintenance'");


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
//          MANAGE ROOM NUMBERS DATA FETCHING
// =========================================================
$room_numbers_inventory = [];
$sql_room_numbers_inventory = "
    SELECT 
        rn.id, rn.room_number, rn.floor, rn.status,
        r.name AS room_type_name
    FROM 
        room_numbers rn
    JOIN 
        rooms r ON rn.room_type_id = r.id
    ORDER BY 
        rn.room_number ASC
";

$result_room_numbers_inventory = mysqli_query($conn, $sql_room_numbers_inventory);
if ($result_room_numbers_inventory) {
    while ($row = mysqli_fetch_assoc($result_room_numbers_inventory)) {
        $room_numbers_inventory[] = $row;
    }
}


// =========================================================
//           BOOKINGS DATA FETCHING (DATE-FILTER UPDATED)
// =========================================================
$filter_checkin = $_GET['book_checkin'] ?? '';
$filter_checkout = $_GET['book_checkout'] ?? '';

$all_bookings = [];
$sql_all_bookings = "
    SELECT 
        b.id, b.customer_name, b.phone, b.email, b.guests AS booking_guests, b.room_number,
        b.checkin, b.checkout, b.total_price, b.status, b.payment_status, b.extra_bed_included,
        r.name AS room_name, r.id AS room_id
    FROM 
        bookings b
    JOIN 
        rooms r ON b.room_id = r.id
";

// જો તારીખ પસંદ કરેલી હોય તો ફિલ્ટર ઉમેરો
if (!empty($filter_checkin) && !empty($filter_checkout)) {
    $sql_all_bookings .= " WHERE b.checkin >= '$filter_checkin' AND b.checkout <= '$filter_checkout'";
}

$sql_all_bookings .= " ORDER BY b.checkin DESC";

$result_all_bookings = mysqli_query($conn, $sql_all_bookings);
if ($result_all_bookings) {
    while ($row = mysqli_fetch_assoc($result_all_bookings)) {
        $all_bookings[] = $row;
    }
}



// =========================================================
//            OFFLINE BOOKINGS DATA FETCHING
// =========================================================
$offline_bookings = [];
$sql_offline = "SELECT * FROM offline_booking ORDER BY created_at DESC";
$res_offline = mysqli_query($conn, $sql_offline);
if ($res_offline) {
    while ($row = mysqli_fetch_assoc($res_offline)) {
        $offline_bookings[] = $row;
    }
}

// =========================================================
//            TODAY'S CHECKOUTS REMINDER DATA (UPDATED)
// =========================================================
$today_date = date('Y-m-d');
$today_checkouts = [];

// UNION ક્વેરી: bookings (Online) અને offline_booking બંનેમાંથી આજની ચેક-આઉટ લિસ્ટ લાવશે
// આજની ચેક-આઉટ લિસ્ટ
$sql_today_checkouts = "
    (SELECT b.id, b.customer_name, b.phone, b.room_number, r.name AS room_name, b.status, 'online' AS booking_type
     FROM bookings b
     JOIN rooms r ON b.room_id = r.id
     WHERE b.checkout = '$today_date' 
     AND b.status IN ('Confirmed', 'Checked-in'))
    UNION ALL
    (SELECT o.id, o.customer_name, o.phone, o.room_number, 'Offline Room' AS room_name, o.payment_status AS status, 'offline' AS booking_type
     FROM offline_booking o
     WHERE o.checkout_date = '$today_date')
    ORDER BY room_number ASC";

$res_today = mysqli_query($conn, $sql_today_checkouts);
if ($res_today) {
    while ($row = mysqli_fetch_assoc($res_today)) {
        $today_checkouts[] = $row;
    }
}
$total_today_checkouts = count($today_checkouts);

// =========================================================
//          CUSTOMERS DATA FETCHING (ACTIVE)
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
//          PAYMENTS DATA FETCHING
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
//          SETTINGS DATA FETCHING
// =========================================================
$hero_images = [];
$sql_hero_images = "SELECT id, background_image FROM hero_section";
$result_hero_images = mysqli_query($conn, $sql_hero_images);
if ($result_hero_images) {
    while ($row = mysqli_fetch_assoc($result_hero_images)) {
        $hero_images[] = $row;
    }
}

// Placeholder for site settings, assuming site_settings table exists
$site_settings = [
    'phone_number' => '+91 92659 00219',
    'email_address' => 'info@shaktibhuvan.com',
    'physical_address' => 'Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110'
];

$sql_fetch_settings = "SELECT setting_key, setting_value FROM site_settings";
$result_settings = @mysqli_query($conn, $sql_fetch_settings); 
if ($result_settings) {
    while ($row = mysqli_fetch_assoc($result_settings)) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }
}

// =========================================================
//          GALLERY DATA FETCHING
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
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
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
        width: 300px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: var(--sidebar-color);
        padding-top: 20px;
        border-right: 1px solid #ddd;
    }

    .main-content {
        margin-left: 300px;
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

    .dashboard-card,
    .room-inventory-card,
    .stats-card {
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

    .status-confirmed,
    .status-paid,
    .status-available {
        background-color: #e6ffe6;
        color: #008000;
    }

    .status-pending,
    .status-maintenance {
        background-color: #fffbe6;
        color: #ccaa00;
    }

    .status-checkedin {
        background-color: #e6f7ff;
        color: #007bff;
    }

    .status-checkedout {
        background-color: #ffcccc;
        color: #cc0000;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #842029;
    }

    .status-partial {
        background-color: #cff4fc;
        color: #084298;
    }

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
    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .gallery-item {
        background: #fcfcfc;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .gallery-item img {
        max-width: 100%;
        border-radius: 6px;
        height: 120px;
        object-fit: cover;
    }

    .delete-btn {
        background: #e74c3c;
        color: white;
        margin-top: 5px;
    }

    .logout {
        position: absolute;
        bottom: 20px;
        left: 35px;
        text-decoration: none;
    }

    /* Room Dashboard Box Styles */
    .room-box {
        width: 60px;
        height: 60px;
        border: 2px solid #333;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border-radius: 4px;
    }

    .status-square {
        width: 50px;
        height: 50px;
        border: 2px solid #333;
    }

    /* "Shaded" effect for Occupied as seen in sketch */
    .occupied {
        background: repeating-linear-gradient(45deg,
                #ccc,
                #ccc 5px,
                #fff 5px,
                #fff 10px);
        background-color: #eee;
    }

    .available {
        background-color: transparent;
    }

    /* Hero Slider Specific modern styles */
    .hero-manage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .hero-image-card {
        background: #fff;
        border: 1px solid #eef0f2;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        position: relative;
    }

    .hero-image-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .hero-img-wrapper {
        height: 140px;
        width: 100%;
        overflow: hidden;
        background: #f8f9fa;
    }

    .hero-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .hero-image-card:hover .hero-img-wrapper img {
        transform: scale(1.1);
    }

    .hero-card-body {
        padding: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .hero-filename {
        font-size: 0.75rem;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }

    /* Custom Upload Area */
    .upload-box {
        border: 2px dashed #d1d9e0;
        border-radius: 12px;
        padding: 20px;
        background: #fafbfc;
        text-align: center;
    }

    /* ===========================
   MOBILE RESPONSIVE UPDATES
=========================== */

    @media (max-width: 991px) {

        /* Sidebar ne hide karo */
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            display: none;
            /* JS thi toggle thase */
            border-right: none;
            border-bottom: 1px solid #ddd;
            z-index: 1001;
        }

        .sidebar.show {
            display: block;
        }

        .main-content {
            margin-left: 0;
            /* Full width content */
            padding: 15px;
        }

        /* Mobile Header/Toggle Button */
        .mobile-nav-toggle {
            display: block;
            background: var(--sidebar-color);
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Dashboard Cards smaller on mobile */
        .dashboard-card {
            margin-bottom: 15px;
        }

        .card-value {
            font-size: 1.5rem;
        }

        /* Table responsive fixes */
        .table-responsive {
            border: 0;
        }

        /* Room Dashboard Boxes smaller */
        .room-box {
            width: 50px;
            height: 50px;
            font-size: 0.8rem;
        }

        /* Charts height adjustment */
        canvas {
            height: 250px !important;
        }

        /* Sidebar Logout adjustment */
        .logout {
            position: relative;
            bottom: 0;
            left: 20px;
            margin-top: 20px;
            display: block;
            padding-bottom: 20px;
        }
    }

    @media (min-width: 992px) {
        .mobile-nav-toggle {
            display: none;
        }
    }
    </style>
</head>

<body>

    <div class="sidebar d-flex flex-column">
        <h3 class="ms-3 mb-5" style="font-family: 'Playfair Display', serif;">Shakti Bhuvan</h3>
        <nav class="nav flex-column">
            <a class="nav-link active" data-target="dashboard-section"><i class="fas fa-home me-2"></i>Dashboard</a>
            <a class="nav-link active" data-target="room-dashboard-section"><i class="fas fa-th-large me-2"></i> Room
                Dashboard</a>
            <a class="nav-link" data-target="today-checkouts-section">
                <i class="fas fa-bell me-2 text-danger"></i> Today's Checkouts
                <?php if($total_today_checkouts > 0): ?>
                <span class="badge bg-danger rounded-pill ms-1"><?= $total_today_checkouts ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-link" data-target="bookings-section"><i class="fas fa-calendar-alt me-2"></i>Bookings</a>
            <a class="nav-link" data-target="offline-bookings-section"><i class="fas fa-bed-pulse me-2"></i>Offline
                Bookings</a>
            <a class="nav-link" data-target="manage-rooms-section"><i class="fas fa-key me-2"></i>Manage Room Types</a>
            <a class="nav-link" data-target="manage-room-numbers-section"><i class="fas fa-list-ol me-2"></i>Manage Room
                Numbers</a>

            <a class="nav-link" data-target="customers-section"><i class="fas fa-users me-2"></i>Customers</a>

            <a class="nav-link" data-target="gallery-section"><i class="fas fa-images me-2"></i>Gallery</a>
            <a class="nav-link" data-target="settings-section"><i class="fas fa-cog me-2"></i>Settings</a>
        </nav>
        <div class="mt-auto p-3">
        </div>
        <a href="admin.php" class="text-danger logout"><i class="fas fa-sign-out-alt me-2"></i>logout</a>
    </div>

    <div class="main-content">

        <div id="dashboard-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Dashboard</h2>
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="section" value="dashboard-section">
                    <div>
                        <label class="small fw-bold">From:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm"
                            value="<?= $start_date ?>">
                    </div>
                    <div>
                        <label class="small fw-bold">To:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm"
                            value="<?= $end_date ?>">
                    </div>
                    <div class="align-self-end">
                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                        <a href="admin_dashboard.php" class="btn btn-sm btn-outline-secondary">Today</a>
                    </div>
                </form>
            </div>
            <p class="text-muted">Showing data from <strong><?= date('d M, Y', strtotime($start_date)) ?></strong> to
                <strong><?= date('d M, Y', strtotime($end_date)) ?></strong>
            </p>

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
                        <small>--</small>
                        <!-- <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 4.2% more than last
                            month</small> -->
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
                        <small class="text-muted">Out of <?php echo $total_rooms_physical; ?> total rooms</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-title-text mb-1">Revenue </p>
                                <h3 class="card-value">₹<?php echo number_format($revenue_filtered, 2); ?></h3>
                            </div>
                            <i class="fas fa-money-bill-wave fs-3 text-muted"></i>
                        </div>
                        <small>--</small>
                        <!-- <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 7.8% up from last month</small> -->
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
                        <small>--</small>
                        <!-- <small class="text-danger"><i class="fas fa-arrow-down me-1"></i> 2% less than last
                            month</small> -->
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
                <h5 class="card-title mb-3">Bookings from <?= $start_date ?> to <?= $end_date ?></h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer Name</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_bookings) > 0): ?>
                            <?php foreach ($recent_bookings as $booking): 
                            $status_class = strtolower(str_replace([' ', '-'], '', $booking['status']));
                        ?>
                            <tr>
                                <td>BK<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                <td><?php echo $booking['checkin']; ?></td>
                                <td><?php echo $booking['checkout']; ?></td>
                                <td><span
                                        class="badge rounded-pill status-<?php echo $status_class; ?>"><?php echo $booking['status']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No bookings found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


        <div id="room-dashboard-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Room Availability Dashboard</h2>
                <form class="d-flex gap-2 align-items-end" method="GET">
                    <input type="hidden" name="section" value="room-dashboard-section">
                    <div>
                        <label class="small">Check-in</label>
                        <input type="date" name="dash_checkin" class="form-control form-control-sm"
                            value="<?= $view_checkin ?>">
                    </div>
                    <div>
                        <label class="small">Check-out</label>
                        <input type="date" name="dash_checkout" class="form-control form-control-sm"
                            value="<?= $view_checkout ?>">
                    </div>
                    <button type="submit" class="btn btn-sm btn-dark">Filter</button>
                </form>
            </div>
            <hr class="mt-0">

            <div class="row g-4">
                <?php foreach ($room_dashboard_data as $type_name => $data): ?>
                <div class="col-md-6 mb-4">
                    <div class="dashboard-card h-100">
                        <h5 class="mb-4 text-primary"><?= htmlspecialchars($type_name) ?></h5>
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <?php foreach ($data['rooms'] as $room): 
                            $statusClass = $room['is_occupied'] ? 'occupied' : 'available';
                            $clickAction = $room['is_occupied'] ? "alert('Room already booked for these dates')" : "openOfflineBooking('".$room['number']."', '".$view_checkin."')";
                        ?>
                            <div class="room-box <?= $statusClass ?>" onclick="<?= $clickAction ?>"
                                style="cursor: pointer;" title="Room <?= $room['number'] ?>">
                                R<?= htmlspecialchars($room['number']) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-auto p-3 bg-light rounded border d-flex justify-content-around text-center">
                            <div><small class="d-block">Total</small><strong><?= $data['total'] ?></strong></div>
                            <div><small
                                    class="d-block text-danger">Booked</small><strong><?= $data['occupied'] ?></strong>
                            </div>
                            <div><small
                                    class="d-block text-success">Available</small><strong><?= $data['available'] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>



        <div class="modal fade" id="offlineBookingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="process_offline_booking.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">New Offline Booking - Room <span id="display_room_no"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="room_number" id="form_room_number">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Check-in Date</label>
                                    <input type="date" name="checkin_date" id="form_checkin_date" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Check-out Date</label>
                                    <input type="date" name="checkout_date" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" name="customer_name" class="form-control"
                                        placeholder="Enter name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="10 digit number"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Status</label>
                                    <select name="payment_status" class="form-select">
                                        <option value="Paid">Paid</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Partial">Partial</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="submit_offline" class="btn btn-primary">Confirm Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        <div id="manage-rooms-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Manage Room Types</h2>
                    <p class="text-muted">Add, edit, and manage your room type inventory</p>
                </div>
                <a href="admin_add_room.php" class="btn btn-primary"
                    style="background-color: var(--primary-color); border-color: var(--primary-color);"><i
                        class="fas fa-plus me-2"></i>Add New Room Type</a>
            </div>

            <hr class="mt-0">

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value"><?php echo number_format($total_rooms_physical); ?></p>
                        <p class="stats-label">Total Physical Rooms</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value"><?php echo number_format($available_rooms); ?></p>
                        <p class="stats-label">Rooms Available Now</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value"><?php echo number_format($occupied_rooms); ?></p>
                        <p class="stats-label">Rooms Occupied</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <p class="stats-value text-danger"><?php echo number_format($maintenance_rooms); ?></p>
                        <p class="stats-label">Under Maintenance</p>
                    </div>
                </div>
            </div>

            <div class="room-inventory-card">
                <h5 class="card-title mb-4">Room Type Inventory</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
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
                            <?php if (count($room_inventory) > 0): ?>
                            <?php foreach ($room_inventory as $room): 
                                    $room_id_display = 'RM' . str_pad($room['id'], 3, '0', STR_PAD_LEFT);
                                    $room_type = getRoomTypeAndCount($room['name']);
                                    $amenity_count = countAmenities($room['amenities']);
                                    $status_class = strtolower(str_replace([' '], '', $room['status']));
                                    $status_badge_class = ($status_class == 'available') ? 'status-available' : 'status-maintenance';
                                    $status_display = ($status_class == 'available') ? 'Available' : 'Maintenance';
                                    $numerical_id = $room['id'];
                                ?>
                            <tr>
                                <td><span class="fw-bold"><?php echo $room_id_display; ?></span></td>
                                <td><?php echo htmlspecialchars($room['name']); ?></td>
                                <td><?php echo htmlspecialchars($room_type); ?></td>
                                <td>₹<?php echo number_format($room['discount_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($room['guests']); ?> guests</td>
                                <td>
                                    <i class="fas fa-wifi text-muted me-1"></i>
                                    <i class="fas fa-tv text-muted me-1"></i>
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
                                    <a href="#" class="btn btn-sm text-muted action-button" data-bs-toggle="modal"
                                        data-bs-target="#actionModal" data-record-id="<?php echo $room_id_display; ?>"
                                        data-numerical-id="<?php echo $numerical_id; ?>" data-record-type="Room">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No room types found in the inventory.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>





        <div id="manage-room-numbers-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Manage Room Numbers</h2>
                    <p class="text-muted">Physical room inventory management (Total:
                        <?php echo count($room_numbers_inventory); ?> rooms)</p>
                </div>
                <a href="admin_add_room.php" class="btn btn-warning"
                    style="background-color: #f1c45f; border-color: #f1c45f;"><i class="fas fa-plus me-2"></i>Add New
                    Room Numbers (via Room Type)</a>
            </div>

            <hr class="mt-0">

            <div class="dashboard-card">
                <h5 class="card-title mb-4">Physical Room List</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Room Number</th>
                                <th>Room Type</th>
                                <th>Floor</th>
                                <th>Current Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($room_numbers_inventory) > 0): ?>
                            <?php foreach ($room_numbers_inventory as $rn_room): 
                                    $rn_id_display = 'RN' . str_pad($rn_room['id'], 4, '0', STR_PAD_LEFT);
                                    $status_class = strtolower(str_replace([' ', '-'], '', $rn_room['status']));
                                    $status_badge_class = "status-{$status_class}";
                                    $numerical_id = $rn_room['id'];
                                ?>
                            <tr>
                                <td><span class="fw-bold"><?php echo $rn_id_display; ?></span></td>
                                <td><span
                                        class="badge bg-dark"><?php echo htmlspecialchars($rn_room['room_number']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($rn_room['room_type_name']); ?></td>
                                <td><?php echo htmlspecialchars($rn_room['floor']); ?></td>
                                <td>
                                    <span class="badge rounded-pill <?php echo $status_badge_class; ?>">
                                        <?php echo htmlspecialchars($rn_room['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm text-muted action-button" data-bs-toggle="modal"
                                        data-bs-target="#actionModal" data-record-id="<?php echo $rn_id_display; ?>"
                                        data-numerical-id="<?php echo $numerical_id; ?>" data-record-type="RoomNumber">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No physical room numbers found. Please
                                    add room numbers via the "Add New Room Type" page.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div id="bookings-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>All Bookings</h2>
                <form class="d-flex gap-2 align-items-end" method="GET">
                    <input type="hidden" name="section" value="bookings-section">
                    <div>
                        <label class="small fw-bold">From Check-in:</label>
                        <input type="date" name="book_checkin" class="form-control form-control-sm"
                            value="<?= $filter_checkin ?>">
                    </div>
                    <div>
                        <label class="small fw-bold">To Check-out:</label>
                        <input type="date" name="book_checkout" class="form-control form-control-sm"
                            value="<?= $filter_checkout ?>">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <?php if(!empty($filter_checkin)): ?>
                    <a href="admin_dashboard.php?section=bookings-section"
                        class="btn btn-sm btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-success ms-2" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </form>
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
                                <th>Email</th>
                                <th>Room Type</th>
                                <th>Room No.</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Guests</th>
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
                                    
                                    $extra_bed_icon = ($booking['extra_bed_included'] == 1) ? '<i class="fas fa-plus text-success ms-1" title="Extra Bed"></i>' : '';
                                ?>
                            <tr>
                                <td><span class="fw-bold"><?php echo $booking_id_display; ?></span></td>
                                <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                <td><span
                                        class="badge bg-secondary"><?php echo htmlspecialchars($booking['room_number'] ?? 'N/A'); ?></span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($booking['checkin'])); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($booking['checkout'])); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_guests']) . $extra_bed_icon; ?></td>
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
                                    <div class="d-flex gap-2">
                                        <?php if ($booking['status'] !== 'Checked-out'): ?>
                                        <a href="process_booking_status.php?booking_id=<?php echo $numerical_id; ?>&action=checkout"
                                            class="btn btn-sm btn-outline-warning"
                                            onclick="return confirm('Mark room as Checking out / Cleaning?')">
                                            <i class="fas fa-broom me-1"></i> Checkout
                                        </a>
                                        <?php endif; ?>

                                        <a href="process_booking_status.php?booking_id=<?php echo $numerical_id; ?>&action=available"
                                            class="btn btn-sm btn-outline-success"
                                            onclick="return confirm('Mark room as Available for new guests?')">
                                            <i class="fas fa-check me-1"></i> Available
                                        </a>

                                        <a href="#" class="btn btn-sm text-muted action-button" data-bs-toggle="modal"
                                            data-bs-target="#actionModal"
                                            data-record-id="<?php echo $booking_id_display; ?>"
                                            data-numerical-id="<?php echo $numerical_id; ?>" data-record-type="Booking">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center text-muted">No bookings found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div id="offline-bookings-section" class="content-section" style="display: none;">
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'booked'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Offline booking has been created successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Offline Booking Records (Walk-in)</h2>
            </div>
            <hr class="mt-0">

            <div class="dashboard-card">
                <h5 class="card-title mb-4">All Offline Booking Details</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Room Number</th>
                                <th>Customer Details</th>
                                <th>Check-in / Check-out</th>
                                <th>Payment</th>
                                <th>Booking Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($offline_bookings) > 0): ?>
                            <?php foreach ($offline_bookings as $off): ?>
                            <tr>
                                <td>#<?php echo $off['id']; ?></td>
                                <td>
                                    <span
                                        class="badge bg-primary fs-6">R-<?php echo htmlspecialchars($off['room_number']); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($off['customer_name']); ?></strong><br>
                                    <small class="text-muted"><i
                                            class="fas fa-phone-alt me-1"></i><?php echo htmlspecialchars($off['phone']); ?></small>
                                </td>
                                <td>
                                    <small class="d-block"><strong>In:</strong>
                                        <?php echo date('d M, Y', strtotime($off['checkin_date'])); ?></small>
                                    <small class="d-block"><strong>Out:</strong>
                                        <?php echo date('d M, Y', strtotime($off['checkout_date'])); ?></small>
                                </td>
                                <td>
                                    <?php 
                                $p_status = $off['payment_status'];
                                $badge_color = ($p_status == 'Paid') ? 'bg-success' : (($p_status == 'Partial') ? 'bg-info text-dark' : 'bg-danger');
                            ?>
                                    <span class="badge <?php echo $badge_color; ?>"><?php echo $p_status; ?></span>
                                </td>
                                <td><?php echo date('d M, h:i A', strtotime($off['created_at'])); ?></td>
                                <td>
                                    <a href="process_offline_checkout.php?id=<?php echo $off['id']; ?>&room=<?php echo $off['room_number']; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to check out this room? The room will be freed up.');">
                                        <i class="fas fa-sign-out-alt me-1"></i> Check-out
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No offline booking records found.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div id="today-checkouts-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📅 Today's Checkouts Reminder</h2>
                <span class="text-muted">Date: <?= date('d M, Y') ?></span>
            </div>
            <hr class="mt-0">

            <?php if ($total_today_checkouts > 0): ?>
            <div class="alert alert-warning border-start border-4 border-warning shadow-sm mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Reminder:</strong> Today you have <strong><?= $total_today_checkouts ?></strong> room(s)
                scheduled for checkout.
            </div>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Room No.</th>
                                <th>Customer Details</th>
                                <th>Room Type</th>
                                <th>Status</th>
                                <th>Quick Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($today_checkouts as $checkout): ?>
                            <tr>
                                <td>
                                    <span
                                        class="badge bg-dark fs-6">R-<?= htmlspecialchars($checkout['room_number']) ?></span>
                                    <?php if($checkout['booking_type'] == 'offline'): ?>
                                    <span class="badge bg-secondary" style="font-size: 10px;">OFFLINE</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($checkout['customer_name']) ?></strong><br>
                                    <small class="text-muted"><i class="fas fa-phone-alt"></i>
                                        <?= htmlspecialchars($checkout['phone']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($checkout['room_name']) ?></td>
                                <td>
                                    <?php if($checkout['booking_type'] == 'online'): ?>
                                    <span class="badge bg-info text-dark"><?= $checkout['status'] ?></span>
                                    <?php else: ?>
                                    <span class="badge bg-warning text-dark">Offline - <?= $checkout['status'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($checkout['booking_type'] == 'online'): ?>
                                    <a href="process_booking_status.php?booking_id=<?= $checkout['id'] ?>&action=checkout"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('ઓનલાઇન બુકિંગ ચેક-આઉટ કન્ફર્મ કરો (રૂમ <?= $checkout['room_number'] ?>)?')">
                                        Check-out Now
                                    </a>
                                    <?php else: ?>
                                    <a href="process_offline_checkout.php?id=<?= $checkout['id'] ?>&room=<?= $checkout['room_number'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('ઓફલાઇન બુકિંગ ચેક-આઉટ કન્ફર્મ કરો (રૂમ <?= $checkout['room_number'] ?>)?')">
                                        Offline Check-out
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-check fa-4x text-light mb-3"></i>
                <h4 class="text-muted">No checkouts scheduled for today.</h4>
            </div>
            <?php endif; ?>
        </div>

        <!-- Customers Section -->

        <div id="customers-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Customer Management</h2>
                <a href="#" class="btn btn-primary" style="background-color: #5cb85c; border-color: #5cb85c;"><i
                        class="fas fa-user-plus me-2"></i>Add Customer</a>
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
                                <td><span
                                        class="fw-bold"><?php echo htmlspecialchars($customer['customer_id']); ?></span>
                                </td>
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
                                    <a href="#" class="btn btn-sm text-muted action-button" data-bs-toggle="modal"
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
                            <tr>
                                <td colspan="9" class="text-center text-muted">No customers found.</td>
                            </tr>
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
                                    <a href="#" class="btn btn-sm text-muted action-button" data-bs-toggle="modal"
                                        data-bs-target="#actionModal"
                                        data-record-id="<?php echo $payment_id_display; ?>"
                                        data-numerical-id="<?php echo $numerical_id; ?>" data-record-type="Payment">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No payment records found.</td>
                            </tr>
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
                        <p class="text-muted small">Upload new images to the gallery and categorize them, or delete
                            existing entries.</p>

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
                                    <input type="file" name="images[]" id="images" class="form-control" multiple
                                        required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" name="upload" class="btn btn-warning w-100"><i
                                            class="fas fa-upload me-2"></i> Upload</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <h6>Uploaded Images (<?php echo count($gallery_images); ?> Total)</h6>
                        <div class="gallery">
                            <?php 
                            foreach ($gallery_images as $img): 
                                $image_path = htmlspecialchars($img['image_url']);
                            ?>
                            <div class="gallery-item">
                                <img src="<?php echo $image_path; ?>" alt="Gallery Image" class="img-fluid">
                                <p class="small text-muted"><?php echo htmlspecialchars($img['image_type']); ?></p>

                                <form method="post"
                                    onsubmit="return confirm('Are you sure you want to delete this image?');"
                                    action="gallery_admin.php">
                                    <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                    <input type="hidden" name="delete" value="1">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 delete-btn"><i
                                            class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <style>
                        /* Inline styles for the gallery section (re-defined here for simplicity) */
                        .gallery {
                            display: grid;
                            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                            gap: 15px;
                            margin-top: 20px;
                        }

                        .gallery-item {
                            background: #fcfcfc;
                            padding: 10px;
                            border-radius: 8px;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                            text-align: center;
                        }

                        .gallery-item img {
                            max-width: 100%;
                            border-radius: 6px;
                            height: 120px;
                            object-fit: cover;
                        }

                        .delete-btn {
                            background: #e74c3c;
                            color: white;
                            margin-top: 5px;
                        }
                        </style>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div id="reviews-section" class="content-section" style="display: none;">
            <h2>Customer Reviews</h2>
            <p>Content for Reviews will go here...</p>
        </div> -->

        <div id="settings-section" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Site Settings & Configuration</h2>
            </div>

            <hr class="mt-0">

            <div class="row g-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <h5 class="card-title mb-4"><i class="fas fa-images me-2 text-warning"></i> Homepage Hero Slider
                        </h5>
                        <p class="text-muted small">Manage high-resolution background images for your homepage slider.
                        </p>

                        <form method="POST" action="update_hero.php" enctype="multipart/form-data">
                            <div class="hero-manage-grid">
                                <?php foreach ($hero_images as $image): ?>
                                <div class="hero-image-card">
                                    <div class="hero-img-wrapper">
                                        <img src="<?php echo htmlspecialchars($image['background_image']); ?>"
                                            alt="Slider Image">
                                    </div>
                                    <div class="hero-card-body">
                                        <span
                                            class="hero-filename"><?php echo basename($image['background_image']); ?></span>
                                        <input type="hidden" name="image_id[]" value="<?php echo $image['id']; ?>">
                                        <button type="submit" name="delete_image" value="<?php echo $image['id']; ?>"
                                            class="btn btn-sm btn-outline-danger border-0"
                                            onclick="return confirm('Delete this hero slider image?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="upload-box mt-4">
                                <h6 class="mb-3">Add New Slide Image</h6>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="file" name="new_image" class="form-control" accept="image/*"
                                                required>
                                            <button type="submit" name="add_image" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i> Upload
                                            </button>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Recommended size: 1920x1080px
                                            (JPG/PNG)</small>
                                    </div>
                                </div>
                            </div>
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
                    <h5 class="modal-title" id="actionModalLabel">Actions for <span id="modal-record-id"
                            class="fw-bold"></span></h5>
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
            const activeLink = document.querySelector(`.sidebar .nav-link[data-target="${targetId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
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
            actionModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                const recordId = button.getAttribute('data-record-id');
                const numericalId = button.getAttribute('data-numerical-id');
                const recordType = button.getAttribute('data-record-type');

                document.getElementById('modal-record-id').textContent = recordId;
                document.getElementById('modal-record-type-text').textContent =
                    `Record Type: ${recordType}`;

                // --- Determine the ID and Script based on recordType ---
                let viewScript = 'view_details.php';
                let editScript = 'edit_record.php';
                let deleteScript = 'delete_record.php';
                let viewEditId = numericalId; // Default to numerical ID

                if (recordType === 'Customer') {
                    // viewScript = 'view_customer.php';
                    editScript = 'edit_customer.php';
                    deleteScript = 'delete_customer.php';
                    viewEditId = recordId; // Use full string customer_id
                } else if (recordType === 'Booking') {
                    // viewScript = 'view_booking.php';
                    editScript = 'edit_booking.php';
                    deleteScript = 'delete_booking.php';
                } else if (recordType === 'Room') {
                    editScript = 'edit_room.php';
                    deleteScript = 'delete_room.php'; // આ ફાઈલ આપણે ઉપર બનાવી
                } else if (recordType === 'RoomNumber') {
                    viewScript = 'view_room_number.php';
                    editScript = 'edit_room_number.php';
                    deleteScript = 'delete_room_number.php';
                }

                // View and Edit Links
                document.getElementById('action-view-link').href = `${viewScript}?id=${viewEditId}`;
                document.getElementById('action-edit-link').href = `${editScript}?id=${viewEditId}`;

                // Delete Link uses the numerical ID
                document.getElementById('action-delete-link').onclick = function() {
                    if (confirm(
                            `Are you sure you want to permanently delete ${recordType} ${recordId}?`
                        )) {
                        window.location.href = `${deleteScript}?id=${numericalId}`;
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
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
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
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

    });

    // admin_dashboard.php ના અંતમાં રહેલા સ્ક્રિપ્ટ ટેગમાં સુધારો
    function openOfflineBooking(roomNum, checkin) {
        const checkoutInput = document.getElementsByName('checkout_date')[0];
        const checkinInput = document.getElementById('form_checkin_date');
        const submitBtn = document.querySelector('button[name="submit_offline"]');

        document.getElementById('display_room_no').innerText = roomNum;
        document.getElementById('form_room_number').value = roomNum;
        checkinInput.value = checkin;

        // Default checkout next day
        let nextDay = new Date(checkin);
        nextDay.setDate(nextDay.getDate() + 1);
        checkoutInput.value = nextDay.toISOString().split('T')[0];

        // Ajax ફંક્શન જે ચેક કરશે કે રૂમ ખાલી છે કે નહિ
        const checkAvailability = () => {
            let room = roomNum;
            let start = checkinInput.value;
            let end = checkoutInput.value;

            if (!start || !end) return;

            // એક નાની PHP ફાઈલ બનાવીશું 'check_room_conflict.php'
            fetch(`check_room_status_api.php?room=${room}&start=${start}&end=${end}`)
                .then(response => response.json())
                .then(data => {
                    if (data.is_booked) {
                        alert(`ચેતવણી: રૂમ નંબર ${room} આ તારીખો વચ્ચે પહેલેથી ઓનલાઇન બુક છે!`);
                        submitBtn.disabled = true; // બટન બંધ કરી દેશે
                        submitBtn.innerText = "રૂમ ઉપલબ્ધ નથી";
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.innerText = "Confirm Booking";
                    }
                });
        };

        // જ્યારે પણ તારીખ બદલાય ત્યારે ચેક કરો
        checkinInput.onchange = checkAvailability;
        checkoutInput.onchange = checkAvailability;

        var myModal = new bootstrap.Modal(document.getElementById('offlineBookingModal'));
        myModal.show();
    }

    function exportToExcel() {
        // ટેબલ પસંદ કરો (Bookings ટેબલ)
        const table = document.querySelector("#bookings-section table");
        let html = table.outerHTML;

        // Excel ફાઇલ ડાઉનલોડ કરવા માટે બ્લોબ (Blob) બનાવો
        const url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        const link = document.createElement("a");
        link.download = "Shakti_Bhuvan_Bookings.xls";
        link.href = url;
        link.click();
    }
    </script>
</body>

</html>