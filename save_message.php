<?php
include 'db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$fullname = $_POST['fullname'];
$email    = $_POST['email'];
$phone    = $_POST['phone'];
$subject  = $_POST['subject'];
$checkin  = $_POST['checkin'];
$checkout = $_POST['checkout'];
$message  = $_POST['message'];

// Insert into database
$sql = "INSERT INTO contact_messages (fullname, email, phone, subject, checkin, checkout, message) 
        VALUES ('$fullname', '$email', '$phone', '$subject', '$checkin', '$checkout', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "Message saved successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
