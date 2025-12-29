<?php
session_start();
include 'db.php';

if (isset($_POST['update_password'])) {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    // ૧. ડેટાબેઝમાંથી અત્યારનો પાસવર્ડ મેળવો
    $sql = "SELECT password FROM admin WHERE id = 1"; 
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // ૨. ચેક કરો કે જૂનો પાસવર્ડ મેચ થાય છે?
        if ($current_password === $row['password']) {
            // ૩. નવો પાસવર્ડ અપડેટ કરો
            $update_sql = "UPDATE admin SET password = '$new_password' WHERE id = 1";
            
            if (mysqli_query($conn, $update_sql)) {
                echo "<script>alert('Password updated successfully!'); window.location.href='admin_dashboard.php?section=change-password-section';</script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "<script>alert('Current password is incorrect!'); window.location.href='admin_dashboard.php?section=change-password-section';</script>";
        }
    }
    exit;
}
?>