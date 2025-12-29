<?php
include 'db.php';
// Header include કરવાની જરૂર નથી જો તમે માત્ર વિગતો જ બતાવવા માંગતા હોવ, અથવા એડમિન લેઆઉટ વાપરી શકો.

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// બુકિંગની સંપૂર્ણ વિગતો મેળવવાની ક્વેરી
$sql = "SELECT b.*, r.name as room_type_name, r.price as base_price 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.id = $booking_id";

$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking not found!");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking Details - #<?php echo $booking_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
        padding: 40px 0;
    }

    .detail-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .detail-header {
        background: #a0522d;
        color: white;
        padding: 20px;
    }

    .info-label {
        color: #888;
        font-size: 0.85rem;
        text-transform: uppercase;
        font-weight: bold;
    }

    .info-value {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 15px;
    }

    .status-badge {
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: bold;
    }

    .price-row {
        background: #fffcf5;
        padding: 15px;
        border-radius: 10px;
        border: 1px dashed #a0522d;
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-3">
                    <a href="admin_dashboard.php?section=bookings-section" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <div class="detail-card">
                    <div class="detail-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Booking Details #BK0<?php echo str_pad($booking_id, 4, '0', STR_PAD_LEFT); ?>
                        </h4>
                        <span class="status-badge bg-white text-dark"><?php echo $booking['status']; ?></span>
                    </div>

                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Customer Info</h5>
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['customer_name']); ?></div>

                                <div class="info-label">Phone Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['phone']); ?></div>

                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['email']); ?></div>
                            </div>

                            <div class="col-md-6 ps-md-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-bed me-2"></i>Stay Info</h5>
                                <div class="info-label">Room Type</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['room_type_name']); ?>
                                </div>

                                <div class="info-label">Assigned Room Number</div>
                                <div class="info-value"><span class="badge bg-dark">Room
                                        <?php echo htmlspecialchars($booking['room_number']); ?></span></div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="info-label">Check-in</div>
                                        <div class="info-value">
                                            <?php echo date('d M, Y', strtotime($booking['checkin'])); ?></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-label">Check-out</div>
                                        <div class="info-value">
                                            <?php echo date('d M, Y', strtotime($booking['checkout'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Other Details</h5>
                                <div class="info-label">Number of Guests</div>
                                <div class="info-value"><?php echo $booking['guests']; ?> Adults</div>

                                <div class="info-label">Extra Bed Included?</div>
                                <div class="info-value">
                                    <?php echo ($booking['extra_bed_included']) ? '✅ Yes' : '❌ No'; ?></div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-receipt me-2"></i>Financial Summary</h5>
                                <div class="price-row">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Payment Status:</span>
                                        <strong class="text-success"><?php echo $booking['payment_status']; ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0">Total Amount:</span>
                                        <span
                                            class="h4 mb-0 text-primary">₹<?php echo number_format($booking['total_price'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <button onclick="window.print()" class="btn btn-outline-dark">
                                <i class="fas fa-print me-2"></i>Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>