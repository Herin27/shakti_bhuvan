<?php
include 'db.php';

if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // બધી ડિટેલ્સ એકસાથે કાઢવા માટેની મોટી ક્વેરી
    $sql = "SELECT b.*, r.name as room_type, r.price as base_price, r.amenities, u.member_since, u.total_spent
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            LEFT JOIN users u ON b.email = u.email
            WHERE b.id = '$id'";
            
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);

    if($data) {
        ?>
<div class="p-4">
    <div class="row g-4">
        <div class="col-md-6 border-end">
            <h6 class="text-primary text-uppercase small fw-bold mb-3">Booking Information</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td>Booking ID:</td>
                    <td class="fw-bold">#BK0<?= $data['id'] ?></td>
                </tr>
                <tr>
                    <td>Room No:</td>
                    <td><span class="badge bg-dark">Room <?= $data['room_number'] ?></span></td>
                </tr>
                <tr>
                    <td>Room Type:</td>
                    <td><?= $data['room_type'] ?></td>
                </tr>
                <tr>
                    <td>Check-in:</td>
                    <td><?= date('d M, Y', strtotime($data['checkin'])) ?></td>
                </tr>
                <tr>
                    <td>Check-out:</td>
                    <td><?= date('d M, Y', strtotime($data['checkout'])) ?></td>
                </tr>
                <tr>
                    <td>Guests:</td>
                    <td><?= $data['guests'] ?> Persons</td>
                </tr>
            </table>
        </div>

        <div class="col-md-6">
            <h6 class="text-primary text-uppercase small fw-bold mb-3">Customer Profile</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td>Name:</td>
                    <td class="fw-bold"><?= $data['customer_name'] ?></td>
                </tr>
                <tr>
                    <td>Phone:</td>
                    <td><?= $data['phone'] ?></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><?= $data['email'] ?></td>
                </tr>
                <tr>
                    <td>Member Since:</td>
                    <td><?= ($data['member_since']) ? date('M Y', strtotime($data['member_since'])) : 'Walk-in' ?></td>
                </tr>
            </table>
        </div>

        <div class="col-12">
            <hr>
        </div>

        <div class="col-md-12">
            <h6 class="text-primary text-uppercase small fw-bold mb-3">Billing & Payment</h6>
            <div class="d-flex justify-content-between bg-light p-3 rounded">
                <div>
                    <p class="mb-0 small text-muted">Total Amount</p>
                    <h4 class="mb-0 text-success">    <?= number_format($data['total_price'], 2) ?></h4>
                </div>
                <div class="text-end">
                    <p class="mb-0 small text-muted">Payment Status</p>
                    <span class="badge bg-<?= ($data['payment_status'] == 'Paid') ? 'success' : 'danger' ?> fs-6">
                        <?= $data['payment_status'] ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    } else {
        echo "<div class='p-4 text-center text-danger'>No details found for this booking.</div>";
    }
}
?>