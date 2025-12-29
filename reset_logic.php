<?php
include 'db.php';

if (isset($_POST['reset_btn'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    // ફોન નંબર ચેક કરો કે તે એડમિનનો જ છે?
    $check_sql = "SELECT * FROM users WHERE phone = '$phone' AND status = 'Active'";
    $res = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($res) > 0) {
        $update = "UPDATE users SET password = '$new_password' WHERE phone = '$phone'";
        mysqli_query($conn, $update);
        header("Location: admin.php?msg=password_reset_success");
    } else {
        echo "<script>alert('Phone number not found!'); window.location.href='forgot_password.php';</script>";
    }
}
?>