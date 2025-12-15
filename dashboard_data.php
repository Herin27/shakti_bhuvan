<?php
include 'db.php'; // Include your database connection file

// --- Helper Functions (Using MySQLi, matching your db.php) ---
/**
 * Helper function to execute a simple SELECT query and fetch a single value (e.g., for counts).
 * @param mysqli $conn The MySQLi connection object.
 * @param string $sql The SQL query to execute.
 * @return mixed The fetched value, or 0/null on failure.
 */
function fetchSingleValue($conn, $sql) {
    try {
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            return $row[0];
        }
        return 0;
    } catch (Exception $e) {
        // Handle error
        return 0;
    }
}
// -----------------------------------------------------------


// --- 1. Dashboard Metrics (Cards) ---

// Total Bookings
$sql_total_bookings = "SELECT COUNT(*) FROM bookings";
// FIX: Changed $pdo to $conn
$total_bookings = fetchSingleValue($conn, $sql_total_bookings);

// Available Rooms
$sql_available_rooms = "SELECT COUNT(*) FROM rooms WHERE status = 'Available'";
// FIX: Changed $pdo to $conn
$available_rooms = fetchSingleValue($conn, $sql_available_rooms);

// Revenue This Month
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t'); // t is the number of days in the given month.

// Since your bookings table has 'total_price' and a 'checkin' date, let's use that for a quick metric
// that reflects the data better for December 2025.
$sql_month_revenue = "
    SELECT SUM(total_price)
    FROM bookings
    WHERE checkin >= '$current_month_start' AND checkin <= '$current_month_end' 
    AND (status = 'Confirmed' OR status = 'Checked-in')
";

// FIX: Using MySQLi syntax for execution
$result_revenue = mysqli_query($conn, $sql_month_revenue);
$revenue_this_month = 0.00;
if ($result_revenue) {
    $row = mysqli_fetch_array($result_revenue);
    $revenue_this_month = $row[0] ?: 0.00;
}


// Occupancy Rate (%) - (Total Rooms - Available Rooms) / Total Rooms * 100
$sql_total_rooms = "SELECT COUNT(*) FROM rooms";
// FIX: Changed $pdo to $conn
$total_rooms = fetchSingleValue($conn, $sql_total_rooms);
$occupancy_rate = ($total_rooms > 0) ? round((($total_rooms - $available_rooms) / $total_rooms) * 100, 2) : 0;


// --- 2. Recent Bookings Table ---
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

// FIX: Using MySQLi syntax for execution
$result_recent_bookings = mysqli_query($conn, $sql_recent_bookings);
if ($result_recent_bookings) {
    while ($row = mysqli_fetch_assoc($result_recent_bookings)) {
        $recent_bookings[] = $row;
    }
}


// --- 3. Data for Charts (Simplified structure for demonstration) ---
// Note: These arrays will be dynamically populated in the main admin_dashboard.php file
// using PHP logic that was previously defined for Chart.js integration.
$monthly_revenue_data = [
    'Jan' => 12000,
    'Feb' => 35000,
    'Mar' => 20000,
    'Apr' => 45000,
    'May' => 60000,
    'Jun' => 80000,
    'Jul' => 0, // Current month's data will come from $revenue_this_month
    'Aug' => 0,
    // ...
];
// For a real-world scenario, you would dynamically query the sums for the last 6-12 months.

?>