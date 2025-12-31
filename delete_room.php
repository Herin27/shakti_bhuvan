<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // ૧. પહેલા તપાસો કે આ રૂમ ટાઈપ અસ્તિત્વમાં છે
    $check_sql = "SELECT * FROM rooms WHERE id = '$id'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        // ૨. રૂમ ટાઈપ ડિલીટ કરવાની ક્વેરી
        $delete_sql = "DELETE FROM rooms WHERE id = '$id'";

        if (mysqli_query($conn, $delete_sql)) {
            // સફળતાપૂર્વક ડિલીટ થયા પછી મેનેજ રૂમ સેક્શન પર મોકલો
            header("Location: admin_dashboard.php?section=manage-rooms-section&msg=room_deleted");
            exit();
        } else {
            echo "ભૂલ: રૂમ ટાઈપ ડિલીટ થઈ શક્યો નથી. " . mysqli_error($conn);
        }
    } else {
        echo "રૂમ ટાઈપ મળ્યો નથી.";
    }
}

mysqli_close($conn);
?>