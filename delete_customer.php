<?php
include 'db.php';

if (isset($_GET['id'])) {
    // URL માંથી આવેલી ID ને સુરક્ષિત કરો
    $customer_id = mysqli_real_escape_string($conn, $_GET['id']);

    // ૧. પહેલા તપાસો કે ગ્રાહક અસ્તિત્વમાં છે (અહીં 'id' ની જગ્યાએ 'customer_id' વાપરો)
    $check_sql = "SELECT * FROM users WHERE customer_id = '$customer_id'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        // ૨. ગ્રાહકને ડિલીટ કરવાની ક્વેરી
        $delete_sql = "DELETE FROM users WHERE customer_id = '$customer_id'";

        if (mysqli_query($conn, $delete_sql)) {
            // સફળતાપૂર્વક ડિલીટ થયા પછી રીડાયરેક્ટ કરો
            header("Location: admin_dashboard.php?section=customers-section&msg=cust_deleted");
            exit();
        } else {
            echo "ભૂલ: ગ્રાહક ડિલીટ થઈ શક્યો નથી. " . mysqli_error($conn);
        }
    } else {
        echo "ગ્રાહક મળ્યો નથી. (ID: $customer_id)";
    }
} else {
    echo "કોઈ ID મળી નથી.";
}

mysqli_close($conn);
?>