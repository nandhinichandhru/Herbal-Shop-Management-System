<?php
require 'db.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendorName = $_POST["vendorName"];
    $contactNo = $_POST["contactNo"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $gstNo = $_POST["gstNo"];
    $shippingMethod = $_POST["shippingMethod"];
    $paymentMethod = $_POST["paymentMethod"];

    // Insert into database
    $insertQuery = "INSERT INTO vendortable (VendorName, ContactNo, Email, Address, GSTNo, ShippingMethod, PaymentMethod)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssssss", $vendorName, $contactNo, $email, $address, $gstNo, $shippingMethod, $paymentMethod);

    if ($stmt->execute()) {
    
        echo "<script>alert('Vendor added successfully!'); window.location.href='vendorfinal.html';</script>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
