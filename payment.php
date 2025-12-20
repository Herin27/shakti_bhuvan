<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['booking']) || empty($_SESSION['booking'])) {
    echo "<div style='text-align:center; padding: 50px;'>No pending booking found. <a href='rooms.php'>Browse Rooms</a></div>";
    include 'footer.php';
    exit;
}
$booking = $_SESSION['booking'];

// --- STEP 1: Determine Tiered Tax Percentage ---
$room_rate = $booking['room_rate']; // This should be the per-night rate

// --- STEP 2: Calculate Subtotal and Tax Amount ---
// Subtotal = (Room Charge * Nights) + (Extra Bed Total)
$total_room_charge = ($booking['room_rate'] ?? 0) * ($booking['nights'] ?? 0);
$total_extra_bed_charge = ($booking['extra_bed_unit_price'] ?? 0) * ($booking['extra_bed_included'] ?? 0) * ($booking['nights'] ?? 0);
$subtotal = $total_room_charge + $total_extra_bed_charge;

$display_tax_pct = 0;

if ($subtotal <= 1000) {
    $display_tax_pct = 0;
} elseif ($subtotal > 1000 && $subtotal <= 7500) {
    $display_tax_pct = 5;
} else {
    $display_tax_pct = 18;
}

$tax_multiplier = $display_tax_pct / 100;
$tax_amount = $subtotal * $tax_multiplier;
$final_payable = $subtotal + $tax_amount;

// Update session with the precisely calculated final price for Razorpay
$_SESSION['booking']['total_price'] = $final_payable;

// --- Razorpay config ---
$keyId = "rzp_test_RqeUyvsrea1Qdx";   
$keySecret = "DypnwCtjMOpiwBcJmZKkeYbd"; 

require('razorpay-php-master/Razorpay.php'); 
use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

// Amount in paise
$amount = (int)(round($final_payable, 2) * 100);

try {
    $orderData = [
        'receipt'         => 'BK_' . $booking['booking_id'],
        'amount'          => $amount,
        'currency'        => 'INR',
        'payment_capture' => 1
    ];
    $razorpayOrder = $api->order->create($orderData);
    $orderId = $razorpayOrder['id'];
} catch (\Exception $e) {
    die("Razorpay Order Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - Shakti Bhuvan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/view_details.css"> 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

  <style>
    /* Global/Base Styles for this page */
    body {
        background-color: #f8f5f0; /* Light, warm background */
    }
    li{
        list-style-type: none;
        margin-left: 10px;
    }
    .room-title {
        color: #5a4636;
        font-size: 2rem;
        border-bottom: 2px solid #f5e6cc;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .desc {
        color: #666;
        margin-bottom: 30px;
    }
    
    /* Main Layout */
    .container {
        max-width: 950px;
        margin: 50px auto;
        padding: 30px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    
    /* Summary Cards */
    .card {
        background: #fdfdfd;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .card h3 {
        color: #b58900; /* Gold color for section titles */
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1.2rem;
        font-weight: 700;
        border-bottom: 1px solid #f5e6cc;
        padding-bottom: 8px;
    }
    .card ul {
        list-style: none;
        padding: 0;
    }
    .card li {
        padding: 8px 0;
        border-bottom: 1px dotted #f0f0f0;
        display: flex;
        /* Removed justify-content: space-between */
        font-size: 0.95rem;
        color: #444;
    }
    .card li:last-child {
        border-bottom: none;
    }
    /* Updated style to control list item width and prevent overlap */
    .card li strong {
        color: #333;
        font-weight: 600;
        min-width: 150px; /* Increased min-width for the label */
        margin-right: 10px; /* Space between label and value */
    }
    .card li span {
        flex: 1; /* Allow the value span to take up remaining space */
        text-align: right;
        word-wrap: break-word; /* Ensure long values wrap */
    }

    /* Payment Box (Right Column) */
    .booking-box {
        background: #fff8ee; /* Light warm background for focus */
        border: 2px solid #f1c45f;
        border-radius: 15px;
        padding: 25px;
        height: fit-content;
        text-align: center;
        margin-top: 70px;
    }
    .booking-box h3 {
        color: #5a4636;
        margin-top: 0;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }
    .price-details {
        margin-bottom: 20px;
    }
    .price-details .row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 1rem;
        color: #555;
    }
    .price-details .total {
        font-size: 1.5rem;
        font-weight: 700;
        border-top: 2px solid #b58900; /* Gold line for total */
        padding-top: 15px;
        margin-top: 10px;
        color: #333;
    }
    .price-details .total strong {
        color: #b58900; /* Gold color for total amount */
    }
    .book-btn2 {
        display: block;
        width: 100%;
        background: #0a7d5f; 
        color: white;
        border: none;
        padding: 15px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.3s;
        box-shadow: 0 4px 10px rgba(0, 100, 0, 0.2);
    }
    .book-btn2:hover {
        background: #05684c;
    }
    .note {
        text-align: center;
        margin-top: 15px;
        font-size: 0.85rem;
        color: #888;
    }

    .edit-btn {
        display: block;
        width: 100%;
        background: #6c757d;
        color: white;
        text-decoration: none;
        padding: 12px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        margin-top: 10px;
        transition: background 0.3s;
        text-align: center;
    }
    .edit-btn:hover {
        background: #5a6268;
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container {
            grid-template-columns: 1fr;
            padding: 20px;
        }
        .booking-box {
            order: -1; /* Payment box first on mobile */
        }
    }
  </style>
</head>
<body>
<?php 
if (file_exists('header.php')) {
    include 'header.php';
} else {
    // Fallback if header.php is missing
    echo '<header class="navbar"><div class="logo-text"><h1>Shakti Bhuvan</h1><span>Premium Stays</span></div></header>';
}
?>


<div class="container">
  <div>
    <h1 class="room-title">Payment Confirmation</h1>
    <p class="desc">Please review your booking details and proceed to payment to finalize your reservation.</p>

    <div class="card">
      <h3>Booking Details</h3>
      <ul>
        <strong>Room Type:</strong> <span><?= htmlspecialchars($booking['room_name'] ?? 'N/A'); ?></span><br>
        <!-- <strong>Specific Room:</strong> <span>Room - <?= htmlspecialchars($booking['room_number'] ?? 'N/A'); ?></span><br> -->
        <strong>Check-in Date:</strong> <span><?= htmlspecialchars($booking['checkin'] ?? 'N/A'); ?></span><br>
        <strong>Check-out Date:</strong> <span><?= htmlspecialchars($booking['checkout'] ?? 'N/A'); ?></span><br>
        <strong>Nights:</strong> <span><?= $booking['nights'] ?? 0; ?></span><br>
        <!-- <strong>Guests:</strong> <span><?= $booking['guests'] ?? 0; ?></span><br> -->
        <strong>Extra Bed Included:</strong> <span><?= ($booking['extra_bed_included'] ?? 0) ? 'Yes' : 'No'; ?></span><br>
        <strong>No Of Extra Beds:</strong> <span><?= $booking['extra_bed_included'] ?? 0; ?></span><br>
      </ul>
    </div>
    <div class="card">
      <h3>Customer Details</h3>
      <ul>
        <strong>Name:</strong> <span><?= htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></span><br>
        <strong>Phone:</strong> <span><?= htmlspecialchars($booking['phone'] ?? 'N/A'); ?></span><br>
        <strong>Email:</strong> <span><?= htmlspecialchars($booking['email'] ?? 'N/A'); ?></span><br>
      </ul>
    </div>


  </div>

  
  <div class="booking-box">
    <h3>Proceed to Payment</h3>

    <div class="price-details">
        <div class="row">
            <span>Room Charge (<?= $booking['nights']; ?> Nights):</span>
            <span>₹<?= number_format($total_room_charge, 0); ?></span>
        </div>

        <?php if (($booking['extra_bed_included'] ?? 0) > 0): ?>
        <div class="row">
            <span>Extra Bed (<?= $booking['extra_bed_included']; ?> beds):</span>
            <span>₹<?= number_format($total_extra_bed_charge, 0); ?></span>
        </div>
        <?php endif; ?>

        <div class="row" style="border-top: 1px solid #eee; margin-top: 5px; padding-top: 10px;">
            <span>Subtotal:</span>
            <span>₹<?= number_format($subtotal, 0); ?></span>
        </div>
        
        <div class="row">
            <span>GST (<?= $display_tax_pct; ?>%):</span>
            <span>₹<?= number_format($tax_amount, 0); ?></span>
        </div>

        <div class="row total">
            <span>Total Payable:</span>
            <strong>₹<?= number_format($final_payable, 0); ?></strong>
        </div>
    </div>

    <button id="payBtn" class="book-btn2">Pay ₹<?= number_format($final_payable, 0); ?></button>
    <a href="booking.php?room_id=<?= $booking['room_id']; ?>" class="edit-btn">
        <i class="fas fa-edit"></i> Back to Edit Details
    </a>
</div>
</div>

<?php 
// Include footer file
if (file_exists('footer.php')) {
    include 'footer.php';
}
?>

<script>
document.getElementById('payBtn').onclick = function(e){
    var options = {
        "key": "<?php echo $keyId; ?>",
        "amount": "<?php echo $amount; ?>", // Amount in paise
        "currency": "INR",
        "name": "Shakti Bhuvan",
        "description": "Room Booking Payment",
        "order_id": "<?php echo $orderId; ?>", 
        // Inside the Razorpay handler function in payment.php
"handler": function (response){
    var paymentData = {
        razorpay_payment_id: response.razorpay_payment_id,
        razorpay_order_id: response.razorpay_order_id,
        razorpay_signature: response.razorpay_signature,
        booking_id: "<?php echo $booking['booking_id']; ?>" 
    };

    // Corrected the filename from verify_payment.php to process_payment.php
    fetch('process_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // Success redirect
            window.location.href = "thankyou.php?status=success&booking_id=" + data.booking_id;
        } else {
            // Failure redirect with error message
            console.error("Verification failed:", data.error);
            window.location.href = "thankyou.php?status=failed&booking_id=" + paymentData.booking_id;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.location.href = "thankyou.php?status=failed&booking_id=" + paymentData.booking_id;
    });
},
        "prefill": {
            "name": "<?php echo $booking['customer_name'] ?? 'Guest'; ?>",
            "email": "<?php echo $booking['email'] ?? ''; ?>",
            "contact": "<?php echo $booking['phone'] ?? ''; ?>"
        },
        "theme": {
            "color": "#b58900" // Gold/Brown theme
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
    e.preventDefault();
}
</script>

</body>
</html>