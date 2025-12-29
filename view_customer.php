<?php
include 'db.php';

// કસ્ટમરનો ID મેળવો (તમારા JavaScript માંથી પસાર થયેલ ID)
$customer_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// ૧. કસ્ટમરની પર્સનલ વિગતો મેળવો
$sql_user = "SELECT * FROM users WHERE customer_id = '$customer_id'";
$res_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($res_user);

if (!$user) {
    echo "<div class='container mt-5 text-center'><h3>Customer Not Found!</h3><a href='admin_dashboard.php' class='btn btn-primary'>Back</a></div>";
    exit;
}

// ૨. આ કસ્ટમરના તમામ બુકિંગ્સ મેળવો
$sql_bookings = "SELECT b.*, r.name as room_type 
                 FROM bookings b 
                 JOIN rooms r ON b.room_id = r.id 
                 WHERE b.email = '{$user['email']}' OR b.phone = '{$user['phone']}'
                 ORDER BY b.checkin DESC";
$res_bookings = mysqli_query($conn, $sql_bookings);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Profile - <?= htmlspecialchars($user['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --admin-brown: #a0522d;
    }

    body {
        background-color: #f4f7f6;
        font-family: 'Inter', sans-serif;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--admin-brown), #8b4513);
        color: white;
        padding: 40px 0;
        border-radius: 0 0 30px 30px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .booking-table-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-top: 30px;
    }

    .label-text {
        color: #888;
        font-size: 0.8rem;
        text-transform: uppercase;
        font-weight: bold;
    }

    .value-text {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 15px;
        display: block;
    }
    </style>
</head>

<body>

    <div class="profile-header text-center shadow">
        <div class="container">
            <div class="mb-3">
                <i class="fas fa-user-circle fa-5x"></i>
            </div>
            <h2 class="mb-1"><?= htmlspecialchars($user['name']) ?></h2>
            <p class="opacity-75">Customer ID: <?= htmlspecialchars($user['customer_id']) ?> | Member Since:
                <?= date('d M, Y', strtotime($user['member_since'])) ?></p>
            <a href="admin_dashboard.php?section=customers-section" class="btn btn-sm btn-light mt-2"><i
                    class="fas fa-arrow-left me-2"></i>Back to List</a>
        </div>
    </div>

    <div class="container" style="margin-top: -30px;">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="label-text">Total Bookings</div>
                    <div class="h3 mb-0 text-primary"><?= $user['bookings'] ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="label-text">Total Spent</div>
                    <div class="h3 mb-0 text-success">₹<?= number_format($user['total_spent'], 2) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="label-text">Account Status</div>
                    <div class="h3 mb-0 <?= $user['status'] == 'Active' ? 'text-success' : 'text-danger' ?>">
                        <?= $user['status'] ?></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="info-card">
                    <h5 class="mb-4 border-bottom pb-2"><i class="fas fa-id-card me-2 text-primary"></i>Contact Details
                    </h5>
                    <span class="label-text">Email Address</span>
                    <span class="value-text"><?= htmlspecialchars($user['email']) ?></span>

                    <span class="label-text">Phone Number</span>
                    <span class="value-text"><?= htmlspecialchars($user['phone']) ?></span>

                    <span class="label-text">Location</span>
                    <span class="value-text"><?= htmlspecialchars($user['location'] ?: 'Not Provided') ?></span>
                </div>
            </div>

            <div class="col-md-8">
                <div class="booking-table-card">
                    <h5 class="mb-4 border-bottom pb-2"><i class="fas fa-history me-2 text-primary"></i>Booking History
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Room</th>
                                    <th>Dates</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($res_bookings) > 0): ?>
                                <?php while($book = mysqli_fetch_assoc($res_bookings)): ?>
                                <tr>
                                    <td>
                                        <small
                                            class="d-block fw-bold"><?= htmlspecialchars($book['room_type']) ?></small>
                                        <small class="text-muted">No: <?= $book['room_number'] ?></small>
                                    </td>
                                    <td>
                                        <small class="d-block"><?= date('d M, Y', strtotime($book['checkin'])) ?>
                                            -</small>
                                        <small
                                            class="d-block"><?= date('d M, Y', strtotime($book['checkout'])) ?></small>
                                    </td>
                                    <td class="fw-bold text-dark">₹<?= number_format($book['total_price'], 2) ?></td>
                                    <td>
                                        <span
                                            class="badge rounded-pill bg-<?= $book['status'] == 'Confirmed' ? 'success' : ($book['status'] == 'Checked-out' ? 'info' : 'secondary') ?>">
                                            <?= $book['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No past bookings found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 mb-5">
            <a href="edit_customer.php?id=<?= $user['customer_id'] ?>" class="btn btn-warning px-4 me-2"><i
                    class="fas fa-edit me-2"></i>Edit Profile</a>
            <button onclick="window.print()" class="btn btn-outline-dark px-4"><i class="fas fa-print me-2"></i>Print
                Profile</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>