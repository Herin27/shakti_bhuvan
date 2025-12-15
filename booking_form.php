<?php
session_start();
include 'db.php'; // if needed for additional checks

function post($key, $default = null) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// GST helper (same slabs used in view_details)
function getGstRate($price) {
    if ($price < 1000) return 0;
    else if ($price <= 7500) return 5;
    else return 18;
}

// Validation function (for server-side checks)
function validateInput($data, $type) {
    if ($type === 'email') {
        // Basic email format validation
        return filter_var($data, FILTER_VALIDATE_EMAIL);
    } elseif ($type === 'phone') {
        // Basic Indian phone number validation: 10 digits, starting with 6, 7, 8, or 9
        // This is a common pattern for server-side validation.
        return preg_match('/^[6-9]\d{9}$/', $data);
    }
    return true; // For other fields, assume valid for this context
}

// Step 1: Initial booking from view_details.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['room_id'])) {
    $room_id 	= intval(post('room_id'));
    $room_name 	= post('room_name', '');
    $checkin 	= post('checkin', '');
    $checkout 	= post('checkout', '');
    $roomPrice 	= (float) post('room_price', 0); 	    // original price per night
    $discount 	= (float) post('room_discount', 0); 	// discounted price PER NIGHT (final price)
    // tax_fee in POST is optional (other fixed fees); if view_details passed a gst amount here, we ignore it and compute GST ourselves.
    $otherFee 	= isset($_POST['tax_fee']) ? (float) post('tax_fee', 0) : 0; 
    $extraBeds 	= max(0, intval(post('beds', 0))); 	    // capture if sent

    try {
        $d1 = new DateTime($checkin);
        $d2 = new DateTime($checkout);
        $calcNights = $d1->diff($d2)->days;
    } catch (Exception $e) {
        die("Invalid dates provided. Please go back and select valid check-in/check-out dates.");
    }

    if ($calcNights <= 0) {
        die("Check-out date must be after check-in date. Please go back and choose valid dates.");
    }

    // CORRECT per-night logic: discounted price is the final per-night amount
    $perNight = (float)$discount;

    // GST based on perNight (GST applied on room tariff only: perNight * nights)
    $gstRate = getGstRate($perNight);
    $gstAmount = ($perNight * $calcNights) * ($gstRate / 100.0);

    // final total
    $calculatedTotal = ($perNight * $calcNights) + $gstAmount + $otherFee + ($extraBeds * 100);

    // Save booking summary in session (include gst info)
    $_SESSION['booking'] = [
        'room_id' 	=> $room_id,
        'room_name' 	=> $room_name,
        'checkin' 	=> $checkin,
        'checkout' 	=> $checkout,
        'nights' 	=> $calcNights,
        'price' 	=> $roomPrice, 	    // original price (for reference)
        'discount' 	=> $discount, 	    // final per-night price
        'gst_rate' 	=> $gstRate,
        'gst_amount' 	=> $gstAmount,
        'other_fee' 	=> $otherFee,
        'extra_beds' 	=> $extraBeds,
        'total_price' 	=> $calculatedTotal
    ];
}

// Step 2: Handle user info submission (e.g., from an earlier form step, or if we were on this page and re-submitting)
// This is not the primary POST handler for this file, but acts as a guard against invalid data if the form were submitted back to this page.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    // Basic validation on POST data if needed before submission to booking_submit.php (for demo, we'll rely on the client side and booking_submit for final checks)
    $email = post('email');
    $phone = post('phone');
    
    if (!validateInput($email, 'email')) {
        die("Invalid email format provided. Please go back and correct it.");
    }
    if (!validateInput($phone, 'phone')) {
        die("Invalid phone number format provided. Please go back and correct it (Expected: 10 digits).");
    }
    // Note: The form action is booking_submit.php, so this check will primarily happen there, but it's good practice.
}


// If user landed on this page without session booking data, show message
if (!isset($_SESSION['booking'])) {
    die("No booking data found. Please select room & dates first.");
}

$booking = $_SESSION['booking'];

// Step 3: Handle updates (like user entering beds or applying coupon here)
// This recalculates based on session data and any potential POST update for 'beds'
$extraBeds = isset($_POST['beds']) ? intval($_POST['beds']) : (isset($booking['extra_beds']) ? $booking['extra_beds'] : 0);

try {
    $d1 = new DateTime($booking['checkin']);
    $d2 = new DateTime($booking['checkout']);
    $nights = $d1->diff($d2)->days;
} catch (Exception $e) {
    die("Booking dates invalid.");
}
if ($nights <= 0) {
    die("Check-out date must be after check-in date. Please go back and choose valid dates.");
}

// CORRECT perNight for session-based recalculation
$perNight = (float)$booking['discount']; 	// discounted price per night stored in session
$gstRate = isset($booking['gst_rate']) ? (float)$booking['gst_rate'] : getGstRate($perNight);
$gstAmount = ($perNight * $nights) * ($gstRate / 100.0);
$otherFee = isset($booking['other_fee']) ? (float)$booking['other_fee'] : 0.0;

$totalPrice = ($perNight * $nights) + $gstAmount + $otherFee + ($extraBeds * 100);

// Update session with any user changes
$_SESSION['booking']['extra_beds'] = $extraBeds;
$_SESSION['booking']['nights'] = $nights;
$_SESSION['booking']['gst_rate'] = $gstRate;
$_SESSION['booking']['gst_amount'] = $gstAmount;
$_SESSION['booking']['total_price'] = $totalPrice;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Book Now - Shakti Bhuvan</title>
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="./assets/css/view_details.css">
	<link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
	.logo-icon img {
		width: 60px; 	/* adjust size */
		height: auto;
		border-radius: 50%; /* make circular if needed */
		margin-right: 10px;
	}
    /* Add basic style for error message */
    .error-message {
        color: red;
        font-size: 0.85em;
        margin-top: -10px;
        margin-bottom: 10px;
        display: none; /* Hidden by default */
    }
</style>
<body>
<header class="navbar">
	<div class="logo">
		<div class="logo-icon">
			<img src="assets/images/logo.png" alt="Shakti Bhuvan Logo">
		</div>
		<div class="logo-text">
			<h1>Shakti Bhuvan</h1>
			<span>Premium Stays</span>
		</div>
	</div>

	<nav class="nav-links">
		<a href="index.php" >Home</a>
		<a href="rooms.php">Rooms</a>
		<a href="gallery.php">Gallery</a>
		<a href="contact.php">Contact</a>
		<a href="admin.php">admin</a>
	</nav>

	<div class="contact-info">
		<span><i class="fas fa-phone"></i> +91 98765 43210</span>
		<span><i class="fas fa-envelope"></i> info@shaktibhuvan.com</span>
		<a href="rooms.php" class="book-btn">Book Now</a>
	</div>
</header>

<div class="container">
	<div>
		<h1 class="room-title">Booking Confirmation</h1>
		<p class="desc">Please review your booking details below and complete your information to confirm your stay.</p>

		<div class="card">
			<h3>Booking Summary</h3>
			<ul>
				<li><strong>Room:</strong> <?= htmlspecialchars($booking['room_name']) ?></li>
				<li><strong>Check-in:</strong> <?= htmlspecialchars($booking['checkin']) ?></li>
				<li><strong>Check-out:</strong> <?= htmlspecialchars($booking['checkout']) ?></li>
				<li><strong>Total Nights:</strong> <?= $nights ?></li>
				<li><strong>Room (per night):</strong> ₹<?= number_format($perNight, 2) ?></li>
				<li><strong>GST (<?= $gstRate ?>%):</strong> ₹<?= number_format($gstAmount, 2) ?></li>
				<li><strong>Other Fees:</strong> ₹<?= number_format($otherFee, 2) ?></li>
				<li><strong>Total Price:</strong> ₹<?= number_format($totalPrice, 2) ?></li>
			</ul>
		</div>
	</div>

	<form action="booking_submit.php" method="POST" id="bookingForm">
		<div class="booking-box">
			<h3>Guest Information</h3>

			<label>Name</label>
			<input type="text" name="name" class="date-input" required>

			<label>Email</label>
			<input type="email" name="email" id="emailInput" class="date-input" required>
            <p id="emailError" class="error-message">Please enter a valid email address (e.g., example@domain.com).</p>

			<label>Phone</label>
            <input type="tel" name="phone" id="phoneInput" class="date-input" required pattern="[6-9]{1}[0-9]{9}" title="Phone number must be 10 digits and start with 6, 7, 8, or 9.">
            <p id="phoneError" class="error-message">Please enter a valid 10-digit phone number (e.g., 9876543210).</p>

			<label>Location</label>
			<input type="text" name="location" class="date-input">

			<label>Guests</label>
			<input type="number" name="guests" class="date-input" min="1" value="1" required>

			<label>No. of Extra Bed</label>
			<input type="number" name="beds_display" id="bedsInput" class="date-input" min="0" value="<?= $extraBeds ?>">
			<button type="button" id="addBedBtn" class="add-extra-bed-btn">Add Extra Bed</button>
<br>

			<input type="hidden" name="discount" value="0">

			<hr>

			<input type="hidden" name="room_id" value="<?= htmlspecialchars($booking['room_id']) ?>">
			<input type="hidden" name="checkin" value="<?= htmlspecialchars($booking['checkin']) ?>">
			<input type="hidden" name="checkout" value="<?= htmlspecialchars($booking['checkout']) ?>">
			<input type="hidden" name="beds" id="hiddenBeds" value="<?= htmlspecialchars($extraBeds) ?>"> 
			<input type="hidden" name="total_price" id="hiddenTotal" value="<?= htmlspecialchars($totalPrice) ?>">
			<input type="hidden" name="gst_rate" id="hiddenGstRate" value="<?= htmlspecialchars($gstRate) ?>">
			<input type="hidden" name="gst_amount" id="hiddenGstAmount" value="<?= htmlspecialchars($gstAmount) ?>">

			<div class="price-details">
				<div class="row"><span>Room Rate (per night)</span><strong id="perNightDisplay">₹<?= number_format($perNight,2) ?></strong></div>
				<div class="row"><span>Nights</span><strong id="nightsDisplay"><?= $nights ?></strong></div>
				<div class="row"><span>Extra Beds</span><strong id="extraBedDisplay"><?= $extraBeds ?> × ₹100</strong></div>
				<div class="row"><span>GST (<?= $gstRate ?>%)</span><strong id="gstDisplay">₹<?= number_format($gstAmount,2) ?></strong></div>
				<div class="row"><span>Other Fees</span><strong id="otherFeeDisplay">₹<?= number_format($otherFee,2) ?></strong></div>
				<div class="row total"><span>Total:</span><strong id="totalDisplay">₹<?= number_format($totalPrice,2) ?></strong></div>
			</div>

			<button type="submit" class="book-btn2">Submit & Pay</button>
			<p class="note">Free cancellation up to 24 hours before check-in</p>
		</div>
	</form>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
	const bedsInput = document.getElementById("bedsInput");
	const addBedBtn = document.getElementById("addBedBtn");
    
    // Validation elements
    const emailInput = document.getElementById("emailInput");
    const phoneInput = document.getElementById("phoneInput");
    const emailError = document.getElementById("emailError");
    const phoneError = document.getElementById("phoneError");
    const bookingForm = document.getElementById("bookingForm");

	const perNight = parseFloat(<?= json_encode($perNight) ?>);
	const nights = parseInt(<?= json_encode($nights) ?>, 10);
	const otherFee = parseFloat(<?= json_encode($otherFee) ?>);
	let gstRate = parseFloat(<?= json_encode($gstRate) ?>);

	const perNightDisplay = document.getElementById("perNightDisplay");
	const nightsDisplay = document.getElementById("nightsDisplay");
	const extraBedDisplay = document.getElementById("extraBedDisplay");
	const gstDisplay = document.getElementById("gstDisplay");
	const otherFeeDisplay = document.getElementById("otherFeeDisplay");
	const totalDisplay = document.getElementById("totalDisplay");

	const hiddenTotal = document.getElementById("hiddenTotal");
	const hiddenBeds = document.getElementById("hiddenBeds");
	const hiddenGstRate = document.getElementById("hiddenGstRate");
	const hiddenGstAmount = document.getElementById("hiddenGstAmount");

	function calcAndRender() {
		const beds = Math.max(0, parseInt(bedsInput.value || 0, 10));

		const subtotal = perNight * nights;
		const gstAmount = (subtotal * gstRate) / 100.0;
		const extraBedCost = beds * 100;

		const finalTotal = subtotal + gstAmount + otherFee + extraBedCost;

		// Update DOM
		extraBedDisplay.textContent = beds + " × ₹100";
		gstDisplay.textContent = "₹" + gstAmount.toLocaleString("en-IN", {minimumFractionDigits: 2});
		otherFeeDisplay.textContent = "₹" + otherFee.toLocaleString("en-IN", {minimumFractionDigits: 2});
		totalDisplay.textContent = "₹" + finalTotal.toLocaleString("en-IN", {minimumFractionDigits: 2});

		// Update hidden fields (so booking_submit.php receives right values)
		hiddenTotal.value = finalTotal.toFixed(2);
		hiddenBeds.value = beds; // Update the actual hidden beds field for submission
		hiddenGstRate.value = gstRate;
		hiddenGstAmount.value = gstAmount.toFixed(2);
	}

	bedsInput.addEventListener("input", calcAndRender);
	if (addBedBtn) {
		addBedBtn.addEventListener("click", function(e) {
			e.preventDefault();
			bedsInput.value = (parseInt(bedsInput.value || 0, 10) + 1);
			calcAndRender();
		});
	}
    
    // --- Validation Logic ---

    function validateEmail() {
        if (!emailInput.checkValidity()) {
            emailError.style.display = 'block';
            return false;
        } else {
            emailError.style.display = 'none';
            return true;
        }
    }

    function validatePhone() {
        // We use the pattern defined in HTML5 input: pattern="[6-9]{1}[0-9]{9}"
        if (!phoneInput.checkValidity()) {
            phoneError.style.display = 'block';
            return false;
        } else {
            phoneError.style.display = 'none';
            return true;
        }
    }

    // Real-time validation
    emailInput.addEventListener('input', validateEmail);
    phoneInput.addEventListener('input', validatePhone);

    // Final check before submission
    bookingForm.addEventListener('submit', function(e) {
        let isEmailValid = validateEmail();
        let isPhoneValid = validatePhone();
        
        // Prevent submission if validation fails
        if (!isEmailValid || !isPhoneValid) {
            e.preventDefault();
            alert("Please correct the highlighted errors in the Guest Information section.");
        }
    });

	// Initial render
	calcAndRender();
});
</script>

</body>
</html>