<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ✅ Fetch existing booking details
    $sql = "SELECT * FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Booking not found!");
    }

    $row = $result->fetch_assoc();

    // ✅ Calculate existing per-day price
    $days = (strtotime($row['checkout']) - strtotime($row['checkin'])) / (60 * 60 * 24);
    $days = $days > 0 ? $days : 1; 
    $per_day = ($row['total_price'] - 500) / $days;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name     = $_POST['name'];
        $phone    = $_POST['phone'];
        $checkin  = $_POST['checkin'];
        $checkout = $_POST['checkout'];

        // ✅ Recalculate price
        $days = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);
        $days = $days > 0 ? $days : 1;
        $price = ($per_day * $days) + 500;

        // ✅ Update booking
        $update = "UPDATE bookings 
                   SET customer_name = ?, phone = ?, checkin = ?, checkout = ?, total_price = ? 
                   WHERE id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ssssdi", $name, $phone, $checkin, $checkout, $price, $id);

        if ($stmt->execute()) {
            header("Location: admin_deshboard.php?success=Booking updated successfully");
            exit;
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Booking - Shakti Bhuvan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/view_details.css"> <!-- same CSS as view page -->
</head>
<body>

<header class="navbar">
    <div class="logo">
        <div class="logo-icon">
            <img src="assets/images/logo.jpg" alt="Shakti Bhuvan Logo">
        </div>
        <div class="logo-text">
            <h1>Shakti Bhuvan</h1>
            <span>Premium Stays</span>
        </div>
    </div>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="rooms.php">Rooms</a>
        <a href="contact.php">Contact</a>
        <a href="admin_deshboard.php" class="active">Dashboard</a>
    </nav>
</header>

<div class="container">
  <div class="booking-box" style="width:100%; max-width:600px; margin:auto;">
      <h3>Edit Booking</h3>
      <form method="POST" id="bookingForm">
          <div class="date-box">
              <label>Name</label>
              <input type="text" class="date-input" name="name" 
                     value="<?= htmlspecialchars($row['customer_name']) ?>" required>
          </div>

          <div class="date-box">
              <label>Phone</label>
              <input type="text" class="date-input" name="phone" 
                     value="<?= htmlspecialchars($row['phone']) ?>" required>
          </div>

          <div class="date-box">
              <label>Check-in</label>
              <input type="date" class="date-input" name="checkin" id="checkin" 
                     value="<?= $row['checkin'] ?>" required>
          </div>

          <div class="date-box">
              <label>Check-out</label>
              <input type="date" class="date-input" name="checkout" id="checkout" 
                     value="<?= $row['checkout'] ?>" required>
          </div>

          <div class="price-details">
              <div class="row total">
                  <span>Total Price</span>
                  <strong>₹<span id="totalPrice"><?= $row['total_price'] ?></span></strong>
              </div>
          </div>

          <input type="hidden" step="0.01" name="total_price" id="hidden_total_price" 
                 value="<?= $row['total_price'] ?>">

          <button type="submit" class="book-btn">Update Booking</button>
      </form>
  </div>
</div>

<script>
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const totalPriceDisplay = document.getElementById('totalPrice');
    const hiddenTotalPrice = document.getElementById('hidden_total_price');

    const perDay = <?= $per_day ?>;

    function updatePrice() {
        const checkin = new Date(checkinInput.value);
        const checkout = new Date(checkoutInput.value);

        if (checkin && checkout && checkout > checkin) {
            const days = (checkout - checkin) / (1000 * 60 * 60 * 24);
            const newPrice = (perDay * days) + 500;
            totalPriceDisplay.textContent = newPrice.toFixed(2);
            hiddenTotalPrice.value = newPrice.toFixed(2);
        }
    }

    checkinInput.addEventListener('change', updatePrice);
    checkoutInput.addEventListener('change', updatePrice);
</script>

</body>
</html>
<?php
} else {
    echo "Invalid Request!";
}
?>
