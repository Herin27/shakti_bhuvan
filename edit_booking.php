<?php
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// જૂની વિગતો મેળવો
$sql = "SELECT * FROM bookings WHERE id = $id";
$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking not found!");
}

// રૂમ લિસ્ટ મેળવો (જો રૂમ બદલવો હોય તો)
$rooms_sql = "SELECT id, name FROM rooms";
$rooms_result = mysqli_query($conn, $rooms_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Booking #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background-color: #fffaf0;
        padding-top: 50px;
    }

    .edit-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .btn-update {
        background-color: #a0522d;
        color: white;
        border: none;
    }

    .btn-update:hover {
        background-color: #8b4513;
        color: white;
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="edit-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-edit me-2"></i>Edit Booking #<?= $id ?></h4>
                        <a href="admin_dashboard.php?id=<?= $id ?>" class="btn btn-sm btn-outline-secondary">Cancel</a>
                    </div>

                    <form action="update_booking_process.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $id ?>">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="<?= htmlspecialchars($booking['customer_name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="<?= htmlspecialchars($booking['phone']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($booking['email']) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Room Type</label>
                                <select name="room_id" class="form-select">
                                    <?php while($row = mysqli_fetch_assoc($rooms_result)): ?>
                                    <option value="<?= $row['id'] ?>"
                                        <?= ($row['id'] == $booking['room_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Room Number</label>
                                <input type="text" name="room_number" class="form-control"
                                    value="<?= htmlspecialchars($booking['room_number']) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Check-in Date</label>
                                <input type="date" name="checkin" class="form-control"
                                    value="<?= $booking['checkin'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Check-out Date</label>
                                <input type="date" name="checkout" class="form-control"
                                    value="<?= $booking['checkout'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total Price (₹)</label>
                                <input type="number" name="total_price" class="form-control"
                                    value="<?= $booking['total_price'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Booking Status</label>
                                <select name="status" class="form-select">
                                    <option value="Confirmed"
                                        <?= ($booking['status'] == 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Checked-in"
                                        <?= ($booking['status'] == 'Checked-in') ? 'selected' : '' ?>>Checked-in
                                    </option>
                                    <option value="Checked-out"
                                        <?= ($booking['status'] == 'Checked-out') ? 'selected' : '' ?>>Checked-out
                                    </option>
                                    <option value="Cancelled"
                                        <?= ($booking['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    <option value="Paid"
                                        <?= ($booking['payment_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                    <option value="Pending"
                                        <?= ($booking['payment_status'] == 'Pending') ? 'selected' : '' ?>>Pending
                                    </option>
                                    <option value="Partial"
                                        <?= ($booking['payment_status'] == 'Partial') ? 'selected' : '' ?>>Partial
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 mt-4 text-center">
                                <button type="submit" name="update_booking" class="btn btn-update px-5 py-2">
                                    <i class="fas fa-save me-2"></i>Update Booking Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>