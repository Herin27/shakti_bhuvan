<?php
session_start();
include 'db.php';

if (isset($_POST['update_admin'])) {
    $email = mysqli_real_escape_string($conn, $_POST['new_email']);
    $pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($pass === $confirm_pass) {
        // અહીં તમે પાસવર્ડ અપડેટ કરશો (ટેબલમાં id=1 એડમિન માટે)
        $sql = "UPDATE admin SET email='$email', password='$pass' WHERE id=1";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Credentials Updated Successfully'); window.location.href='admin_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match!'); window.location.href='admin_dashboard.php';</script>";
    }
}
?>