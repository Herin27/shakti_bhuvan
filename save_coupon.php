<?php
include 'db.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $code = $_POST['code'];
    $discount = $_POST['discount_percent'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $sql = "INSERT INTO coupons (code, discount_percent, start_date, end_date) 
            VALUES ('$code', '$discount', '$start', '$end')";
    if(mysqli_query($conn, $sql)){
        echo "Coupon added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
