<?php
include 'db.php';
include 'header.php'; // For navigation and styles

$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$room = null;
$available_room_numbers = [];

// Fetch room details and available room numbers
if ($room_id > 0) {
    // 1. Fetch Room Type Details
    $sql_room = "SELECT * FROM rooms WHERE id = $room_id";
    $result_room = mysqli_query($conn, $sql_room);
    
    if ($result_room && mysqli_num_rows($result_room) > 0) {
        $room = mysqli_fetch_assoc($result_room);
        
        // 2. Fetch Available Physical Room Numbers
        $sql_room_numbers = "SELECT room_number FROM room_numbers 
                             WHERE room_type_id = $room_id AND status = 'Available' 
                             ORDER BY room_number ASC";
        $result_room_numbers = mysqli_query($conn, $sql_room_numbers);
        
        if ($result_room_numbers) {
            while ($row = mysqli_fetch_assoc($result_room_numbers)) {
                $available_room_numbers[] = $row['room_number'];
            }
        }
    }
}

if (!$room) {
    echo "<div class='container' style='padding: 50px; text-align: center; max-width: 800px; margin: auto;'>";
    echo "<h1>Room Not Found</h1>";
    echo "<p>Please select a valid room to proceed with booking.</p>";
    echo "<a href='rooms.php' class='btn book'>Back to Rooms</a>";
    echo "</div>";
    include 'footer.php';
    exit;
}

// $tax_rate = 0.05; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book <?php echo htmlspecialchars($room['name']); ?> - Shakti Bhuvan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/navbar.css">
    <link rel="stylesheet" href="./assets/css/rooms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .booking-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background: #fdfdfd;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 40px;
        }
        .booking-form h2 { color: #5a4636; margin-bottom: 30px; font-size: 2rem; border-bottom: 2px solid #f5e6cc; padding-bottom: 10px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 600; margin-bottom: 6px; color: #444; font-size: 0.95rem; }
        .form-group input, .form-group select, .form-group textarea { padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; background-color: #f9f9f9; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #f1c45f; box-shadow: 0 0 0 3px rgba(241, 196, 95, 0.3); background-color: #fff; }
        .summary-card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); height: fit-content; }
        .summary-card h3 { color: #333; margin-top: 0; font-size: 1.5rem; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .summary-card h3 i { color: #f1c45f; }
        .room-info-static { font-size: 0.95rem; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #eee; }
        .calculation-table { width: 100%; font-size: 1rem; margin-top: 15px; border-collapse: collapse; }
        .calculation-table td { padding: 8px 0; vertical-align: top; }
        .calculation-table .label { color: #555; text-align: left; }
        .calculation-table .value { font-weight: 600; text-align: right; }
        .total-row td { border-top: 2px solid #333; font-size: 1.4rem; font-weight: 700; color: #333; padding-top: 15px; }
        .total-row .value { color: #b58900 !important; }
        .submit-btn-summary { background: #f1c45f; color: white; border: none; padding: 15px 30px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; width: 100%; margin-top: 20px; transition: background 0.3s, box-shadow 0.2s; }
        .submit-btn-summary:hover { background: #d4a93d; box-shadow: 0 4px 10px rgba(241, 196, 95, 0.4); }
        .cancellation-policy { font-size: 0.85rem; color: #888; margin-top: 15px; text-align: center; }
        
        /* New Styles for Extra Bed Section */
        #extra_bed_count_wrapper { display: none; }
    </style>
</head>
<body>

<div class="booking-container">
    <div class="summary-card">
        <h3><i class="fas fa-calendar-alt"></i> Book Your Stay</h3>
        <div class="room-info-static">
            <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['name']); ?></p>
            <p><strong>AC Status:</strong> <?php echo htmlspecialchars($room['ac_status']); ?></p>
        </div>
        
        <table class="calculation-table">
            <tr>
                <td class="label">Room Rate (per night)</td>
                <td class="value">₹<span id="display_room_rate"><?php echo htmlspecialchars(number_format($room['discount_price'])); ?></span></td>
            </tr>
            <tr>
                <td class="label">Nights</td>
                <td class="value" id="nights_value">-</td>
            </tr>
            <tr>
                <td class="label">Room Subtotal</td>
                <td class="value">₹<span id="room_charge_value">0</span></td>
            </tr>
            <tr>
                <td class="label">Extra Bed Charge</td>
                <td class="value">₹<span id="extra_bed_charge_value">0</span></td>
            </tr>
            <tr>
                <td class="label">Taxes & Fees</td>
                <td class="value">₹<span id="tax_value">0</span></td>
            </tr>
            <tr class="total-row">
                <td class="label">Total</td>
                <td class="value">₹<span id="total_payable_value">0</span></td>
            </tr>
        </table>
        
        <button type="submit" form="bookingForm" class="submit-btn-summary">Book Now</button>
        <!-- <p class="cancellation-policy">Free cancellation up to 24 hours before check-in</p> -->
    </div>

    <div class="booking-form">
        <h2>Enter Your Details</h2>
        <form id="bookingForm" action="process_booking.php" method="POST"> 
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
            <input type="hidden" id="room_rate" value="<?php echo htmlspecialchars($room['discount_price']); ?>">
            <input type="hidden" id="tax_rate" value="<?php echo $tax_rate; ?>">
            <input type="hidden" id="extra_bed_price_val" value="<?php echo htmlspecialchars($room['extra_bed_price']); ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="checkin">Check-in Date *</label>
                    <input type="date" id="checkin" name="checkin" required>
                </div>
                <div class="form-group">
                    <label for="checkout">Check-out Date *</label>
                    <input type="date" id="checkout" name="checkout" required>
                </div>
                <div class="form-group">
                    <label for="customer_name">Full Name *</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email">
                </div>
                <!-- <div class="form-group">
                    <label for="guests">Number of Guests *</label>
                    <select id="guests" name="guests" required>
                        <?php 
                        for ($i = 1; $i <= 10; $i++) {
                            echo "<option value='{$i}'>{$i} Guest" . ($i > 1 ? "s" : "") . "</option>";
                        }
                        ?>
                    </select>
                </div> -->
                <div class="form-group">
                    <label for="extra_bed">Include Extra Bed?</label>
                    <select id="extra_bed" name="extra_bed" onchange="toggleExtraBedCount()">
                        <option value="0">No Extra Bed</option>
                        <option value="1">Yes (₹<?php echo htmlspecialchars(number_format($room['extra_bed_price'])); ?> / night)</option>
                    </select>
                </div>
                <div class="form-group" id="extra_bed_count_wrapper">
                    <label for="extra_bed_count">How many Extra Beds?</label>
                    <select id="extra_bed_count" name="extra_bed_count" onchange="calculatePrice()">
                        <option value="1">1 Bed</option>
                        <option value="2">2 Beds</option>
                        <option value="3">3 Beds</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="notes">Special Requests / Notes</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="e.g., Early check-in..."></textarea>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    const ROOM_RATE = parseFloat(document.getElementById('room_rate').value);
    const EXTRA_BED_PRICE_UNIT = parseFloat(document.getElementById('extra_bed_price_val').value);

    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const extraBedSelect = document.getElementById('extra_bed');
    const extraBedCountSelect = document.getElementById('extra_bed_count');
    const extraBedWrapper = document.getElementById('extra_bed_count_wrapper');

    const nightsValue = document.getElementById('nights_value');
    const roomChargeValue = document.getElementById('room_charge_value');
    const extraBedChargeValue = document.getElementById('extra_bed_charge_value');
    const taxValue = document.getElementById('tax_value');
    const totalPayableValue = document.getElementById('total_payable_value');
    
    const formatCurrency = (amount) => amount.toFixed(0);

    function toggleExtraBedCount() {
        if (extraBedSelect.value === "1") {
            extraBedWrapper.style.display = "flex";
        } else {
            extraBedWrapper.style.display = "none";
            extraBedCountSelect.value = "1"; 
        }
        calculatePrice();
    }

    function calculatePrice() {
        const checkin = checkinInput.value;
        const checkout = checkoutInput.value;
        const isExtraBed = extraBedSelect.value === "1";
        const bedCount = isExtraBed ? parseInt(extraBedCountSelect.value) : 0;

        if (!checkin || !checkout || checkin >= checkout) {
            nightsValue.textContent = '-';
            roomChargeValue.textContent = '0';
            extraBedChargeValue.textContent = '0';
            taxValue.textContent = '0';
            totalPayableValue.textContent = '0';
            return;
        }

        const nights = Math.ceil((new Date(checkout) - new Date(checkin)) / (1000 * 3600 * 24));
        
        const totalRoomCharge = ROOM_RATE * nights;
        const totalExtraBedCharge = (EXTRA_BED_PRICE_UNIT * bedCount) * nights;
        const subtotal = totalRoomCharge + totalExtraBedCharge;

        // --- GST Logic Correction ---
        // Calculate the rate per night per room to determine GST slab
        // Usually, GST is based on the transaction value per unit per day
        let currentTaxRate = 0;
        
       if (subtotal <= 1000) {
        currentTaxRate = 0.00; // Tier 1: 0%
    } 
    else if (subtotal > 1000 && subtotal <= 7500) {
        currentTaxRate = 0.05; // Tier 2: 5%
    } 
    else {
        currentTaxRate = 0.18; // Tier 3: 18% (Above 7500)
    }

        const taxes = subtotal * currentTaxRate;
        const totalPayable = subtotal + taxes;

        // Update the UI
        nightsValue.textContent = nights;
        roomChargeValue.textContent = formatCurrency(totalRoomCharge);
        extraBedChargeValue.textContent = formatCurrency(totalExtraBedCharge);
        taxValue.textContent = formatCurrency(taxes) + " (" + (currentTaxRate * 100) + "%)";
        totalPayableValue.textContent = formatCurrency(totalPayable);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        checkinInput.setAttribute('min', today);
        
        checkinInput.addEventListener('change', function() {
            if (checkinInput.value) {
                const nextDay = new Date(checkinInput.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkoutInput.setAttribute('min', nextDay.toISOString().split('T')[0]);
                if (checkoutInput.value < checkoutInput.getAttribute('min')) {
                    checkoutInput.value = checkoutInput.getAttribute('min');
                }
            }
            calculatePrice();
        });
        
        checkoutInput.addEventListener('change', calculatePrice);
        calculatePrice();
    });
</script>
</body>
</html>