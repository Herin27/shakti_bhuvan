<?php
session_start();

// ✅ Ensure booking session exists
if (!isset($_SESSION['booking'])) {
    echo "No booking found. Please book a room first.";
    exit;
}

$booking = $_SESSION['booking'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            background: #fff;
            margin: 50px auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .details p {
            margin: 6px 0;
            font-size: 14px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-size: 14px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .pay-btn {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #28a745;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .pay-btn:hover {
            background: #218838;
        }
        .payment-methods {
            margin: 15px 0;
        }
        .payment-methods label {
            font-weight: normal;
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment Page</h2>

    <div class="details">
        <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['room_name']); ?></p>
        <p><strong>Check-in:</strong> <?php echo htmlspecialchars($booking['checkin']); ?></p>
        <p><strong>Check-out:</strong> <?php echo htmlspecialchars($booking['checkout']); ?></p>
        <p><strong>Nights:</strong> <?php echo $booking['nights']; ?></p>
        <p><strong>Total Price:</strong> ₹<?php echo number_format($booking['total_price'], 2); ?></p>
    </div>

    <form action="thank_you.php" method="post">
        <input type="hidden" name="room_id" value="<?php echo $booking['room_id']; ?>">
        <input type="hidden" name="total_price" value="<?php echo $booking['total_price']; ?>">

        <label for="name">Cardholder Name</label>
        <input type="text" name="name" id="name" required>

        <label for="card">Card Number</label>
        <input type="number" name="card" id="card" placeholder="1111 2222 3333 4444" required>

        <label for="expiry">Expiry Date (MM/YY)</label>
        <input type="text" name="expiry" id="expiry" placeholder="MM/YY" required>

        <label for="cvv">CVV</label>
        <input type="number" name="cvv" id="cvv" required>

        <div class="payment-methods">
            <label><input type="radio" name="method" value="card" checked> Credit/Debit Card</label>
            <label><input type="radio" name="method" value="upi"> UPI</label>
            <label><input type="radio" name="method" value="netbanking"> Net Banking</label>
        </div>

        <button type="submit" class="pay-btn">Pay Now</button>
    </form>
</div>

</body>
</html>
