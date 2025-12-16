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
        
        // 2. Fetch Available Physical Room Numbers for this Room Type
        // NOTE: A robust system would also check dates here, but for this step, we show all "Available" rooms
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
    // Fallback if the room ID is invalid or not found
    echo "<div class='container' style='padding: 50px; text-align: center; max-width: 800px; margin: auto;'>";
    echo "<h1>Room Not Found</h1>";
    echo "<p>Please select a valid room to proceed with booking.</p>";
    echo "<a href='rooms.php' class='btn book'>Back to Rooms</a>";
    echo "</div>";
    include 'footer.php';
    exit;
}

// Define Tax Rate
$tax_rate = 0.05; // 5% as seen in your image
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
        /* BASE LAYOUT */
        .booking-container {
            max-width: 1000px; /* Increased max-width */
            margin: 50px auto;
            padding: 30px;
            background: #fdfdfd; /* Lighter background */
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 3fr 2fr; /* Adjusted grid ratio */
            gap: 40px;
        }

        /* FORM STYLES */
        .booking-form h2 {
            color: #5a4636;
            margin-bottom: 30px;
            font-size: 2rem;
            border-bottom: 2px solid #f5e6cc;
            padding-bottom: 10px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
            font-size: 0.95rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #f9f9f9;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #f1c45f;
            box-shadow: 0 0 0 3px rgba(241, 196, 95, 0.3);
            background-color: #fff;
        }
        .form-group textarea {
            resize: vertical;
        }

        /* SUMMARY CARD (Visual update to match image) */
        .summary-card {
            background: #fff; /* White background for clean look */
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); /* Lighter shadow */
            height: fit-content;
        }
        .summary-card h3 {
            color: #333;
            margin-top: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-card h3 i {
            color: #f1c45f;
        }
        
        /* Static Room Info */
        .room-info-static {
            font-size: 0.95rem;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eee;
        }

        /* Calculation Table */
        .calculation-table {
            width: 100%;
            font-size: 1rem;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .calculation-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .calculation-table .label {
            color: #555;
            text-align: left;
        }
        .calculation-table .value {
            font-weight: 600;
            text-align: right;
        }
        .calculation-table .nights-info {
            font-weight: 400; /* Normal weight for 'Nights' label */
        }
        
        /* Total Row Styling */
        .calculation-table tr.total-row td {
            border-top: 2px solid #333; /* Darker line for total separation */
            font-size: 1.4rem;
            font-weight: 700;
            color: #333; /* Dark color for Total Label */
            padding-top: 15px;
        }
        .total-row .value {
            color: #b58900 !important; /* Gold color for Total Price */
        }
        
        /* Book Now Button */
        .submit-btn-summary {
            background: #f1c45f;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: background 0.3s, box-shadow 0.2s;
        }
        .submit-btn-summary:hover {
            background: #d4a93d;
            box-shadow: 0 4px 10px rgba(241, 196, 95, 0.4);
        }
        
        /* Cancellation Policy */
        .cancellation-policy {
            font-size: 0.85rem;
            color: #888;
            margin-top: 15px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .booking-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .summary-card {
                order: -1; /* Place summary card first on smaller screens */
            }
        }
        @media (max-width: 576px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="booking-container">
    <div class="summary-card">
        <h3><i class="fas fa-calendar-alt"></i> Book Your Stay</h3>

        <div class="room-info-static">
            <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['name']); ?></p>
            <p><strong>Max Guests:</strong> <?php echo htmlspecialchars($room['guests']); ?></p>
            <p><strong>AC Status:</strong> <?php echo htmlspecialchars($room['ac_status']); ?></p>
        </div>
        
        <table class="calculation-table">
            <tr>
                <td class="label">Room Rate (per night)</td>
                <td class="value nights-info">₹<span id="display_room_rate"><?php echo htmlspecialchars(number_format($room['discount_price'], 2)); ?></span></td>
            </tr>
            <tr>
                <td class="label">Nights</td>
                <td class="value" id="nights_value">-</td>
            </tr>
            <tr>
                <td class="label">Room Charge Subtotal</td>
                <td class="value">₹<span id="room_charge_value">0.00</span></td>
            </tr>
            <tr>
                <td class="label">Extra Bed Charge</td>
                <td class="value">₹<span id="extra_bed_charge_value">0.00</span></td>
            </tr>
            <tr>
                <td class="label">Taxes & Fees (<?php echo $tax_rate * 100; ?>%)</td>
                <td class="value">₹<span id="tax_value">0.00</span></td>
            </tr>
            <tr class="total-row">
                <td class="label">Total</td>
                <td class="value">₹<span id="total_payable_value">0.00</span></td>
            </tr>
        </table>
        
        <button type="submit" form="bookingForm" class="submit-btn-summary">Book Now</button>
        
        <p class="cancellation-policy">Free cancellation up to 24 hours before check-in</p>
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
                    <label for="email">Email (Optional)</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="guests">Number of Guests * (Max: <?php echo htmlspecialchars($room['guests']); ?>)</label>
                    <select id="guests" name="guests" required>
                        <?php 
                        $max_guests = intval($room['guests']);
                        for ($i = 1; $i <= $max_guests; $i++) {
                            echo "<option value='{$i}'>{$i} Guest" . ($i > 1 ? "s" : "") . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="physical_room_number">Select Specific Room *</label>
                    <select id="physical_room_number" name="physical_room_number" required>
                        <option value="" disabled selected>Select a Room Number</option>
                        <?php if (!empty($available_room_numbers)): ?>
                            <?php foreach ($available_room_numbers as $room_num): ?>
                                <option value="<?php echo htmlspecialchars($room_num); ?>">Room <?php echo htmlspecialchars($room_num); ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No Available Rooms Found</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="extra_bed">Include Extra Bed?</label>
                    <select id="extra_bed" name="extra_bed">
                        <option value="0">No Extra Bed</option>
                        <option value="1">Yes (₹<?php echo htmlspecialchars(number_format($room['extra_bed_price'], 2)); ?> / night)</option>
                    </select>
                </div>
                
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="notes">Special Requests / Notes</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="e.g., Early check-in, dietary restrictions..."></textarea>
                </div>
                
                <button type="submit" class="submit-btn" style="display: none;">Confirm Booking</button>
            </div>
        </form>
    </div>

</div>

<?php include 'footer.php'; ?>

<script>
    // Constants from PHP
    const ROOM_RATE = parseFloat(document.getElementById('room_rate').value);
    const TAX_RATE = parseFloat(document.getElementById('tax_rate').value);
    const EXTRA_BED_PRICE = parseFloat(document.getElementById('extra_bed_price_val').value);

    // Form elements
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const extraBedSelect = document.getElementById('extra_bed');

    // Display elements
    const nightsValue = document.getElementById('nights_value');
    const roomChargeValue = document.getElementById('room_charge_value');
    const extraBedChargeValue = document.getElementById('extra_bed_charge_value');
    const taxValue = document.getElementById('tax_value');
    const totalPayableValue = document.getElementById('total_payable_value');
    
    // Helper function to format currency
    const formatCurrency = (amount) => amount.toFixed(2);

    function calculatePrice() {
        const checkin = checkinInput.value;
        const checkout = checkoutInput.value;
        const extraBedSelected = parseInt(extraBedSelect.value);

        if (!checkin || !checkout || checkin >= checkout) {
            // Reset summary if dates are invalid
            nightsValue.textContent = '-';
            roomChargeValue.textContent = '0.00';
            extraBedChargeValue.textContent = '0.00';
            taxValue.textContent = '0.00';
            totalPayableValue.textContent = '0.00';
            return;
        }

        // Calculate nights
        const date1 = new Date(checkin);
        const date2 = new Date(checkout);
        const timeDiff = date2.getTime() - date1.getTime();
        const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
        const nights = days > 0 ? days : 0; // Ensure non-negative nights

        // 1. Room Charge
        const totalRoomCharge = ROOM_RATE * nights;

        // 2. Extra Bed Charge
        const totalExtraBedCharge = extraBedSelected === 1 ? EXTRA_BED_PRICE * nights : 0;
        
        // 3. Subtotal
        const subtotal = totalRoomCharge + totalExtraBedCharge;

        // 4. Taxes
        const taxes = subtotal * TAX_RATE;

        // 5. Total
        const totalPayable = subtotal + taxes;

        // Update display
        nightsValue.textContent = nights;
        roomChargeValue.textContent = formatCurrency(totalRoomCharge);
        extraBedChargeValue.textContent = formatCurrency(totalExtraBedCharge);
        taxValue.textContent = formatCurrency(taxes);
        totalPayableValue.textContent = formatCurrency(totalPayable);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];

        // 1. Initialize Date Min values and attach event listeners
        checkinInput.setAttribute('min', today);
        
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        checkoutInput.setAttribute('min', tomorrow.toISOString().split('T')[0]);

        // Recalculate price whenever relevant fields change
        checkinInput.addEventListener('change', function() {
            if (checkinInput.value) {
                const checkinDate = new Date(checkinInput.value);
                checkinDate.setDate(checkinDate.getDate() + 1);
                const minCheckoutDate = checkinDate.toISOString().split('T')[0];
                
                checkoutInput.setAttribute('min', minCheckoutDate);
                
                // If current checkout is earlier than new minimum, reset it
                if (checkoutInput.value < minCheckoutDate) {
                    checkoutInput.value = minCheckoutDate;
                }
            }
            calculatePrice();
        });
        
        checkoutInput.addEventListener('change', calculatePrice);
        extraBedSelect.addEventListener('change', calculatePrice);
        
        // Initial price calculation on load (will likely show 0.00 or - until dates are selected)
        calculatePrice();
    });
</script>

</body>
</html>