<?php
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // CHECK: Does this room have bookings?
    $check_sql = "SELECT COUNT(*) as count FROM bookings WHERE room_id = $id";
    $result = mysqli_query($conn, $check_sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        // OPTION A: Soft Delete (Change status so it doesn't show to users)
        // This avoids the #1451 Foreign Key Error
        $update_sql = "UPDATE rooms SET status = 'Maintenance' WHERE id = $id";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: admin.php?section=manage-rooms-section&msg=Room hidden because it has active bookings");
        }
    } else {
        // OPTION B: Hard Delete (Only if no bookings exist)
        $delete_sql = "DELETE FROM rooms WHERE id = $id";
        if (mysqli_query($conn, $delete_sql)) {
            header("Location: admin.php?section=manage-rooms-section&msg=Room deleted successfully");
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    }
} else {
    header("Location: admin_dashboard.php?section=manage-rooms-section");
}
?>