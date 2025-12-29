<?php
session_start();
include 'db.php';

if (isset($_POST['change_password'])) {
    // હાલમાં લોગીન થયેલ એડમિનની ઈમેલ (તમારા સેશન મુજબ)
    // જો સેશનમાં ઈમેલ ન હોય તો ડાયરેક્ટ ID 1 માટે (તમારા ટેબલ મુજબ)
    $admin_id = 1; 
    
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // ૧. પહેલા ચેક કરો કે નવો પાસવર્ડ અને કન્ફર્મ પાસવર્ડ સરખા છે?
    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('New password and Confirm password do not match!'); window.location.href='admin_dashboard.php?section=settings-section';</script>";
        exit();
    }

    // ૨. ડેટાબેઝમાંથી અત્યારનો પાસવર્ડ મેળવો
    $sql = "SELECT password FROM admin WHERE id = $admin_id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['password'] == $current_pass) {
        // ૩. જો જૂનો પાસવર્ડ સાચો હોય તો નવો પાસવર્ડ અપડેટ કરો
        $update_sql = "UPDATE admin SET password = '$new_pass' WHERE id = $admin_id";
        
        if (mysqli_query($conn, $update_sql)) {
            echo "<script>alert('Password updated successfully!'); window.location.href='admin_dashboard.php?section=settings-section';</script>";
        } else {
            echo "<script>alert('Error updating password!'); window.location.href='admin_dashboard.php?section=settings-section';</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect!'); window.location.href='admin_dashboard.php?section=settings-section';</script>";
    }
}
?>