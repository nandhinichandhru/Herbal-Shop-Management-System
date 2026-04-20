<?php
require 'db.php'; // Make sure this file connects to your database as $conn

// Get form data
$BatchNo = $_POST['BatchNo'];
$ProductID = $_POST['ProductID'];
$BuyingRate = $_POST['BuyingRate'];
$SellingRate = $_POST['SellingRate'];
$Quantity = $_POST['Quantity'];
$ExpiryDate = $_POST['ExpiryDate'];
$DiscountRate = $_POST['DiscountRate'];
$DiscountStartDate = $_POST['DiscountStartDate'];
$DiscountEndDate = $_POST['DiscountEndDate'];
$VendorID = $_POST['VendorID'];

$DateOfTransaction = $_POST['DateOfTransaction'];
$TimeOfTransaction = $_POST['TimeOfTransaction'];

// 1. Insert into Batch Table
$insertBatch = $conn->prepare("INSERT INTO batch (BatchNo, ProductID, BuyingRate, SellingRate, DateOfTransaction, TimeOfTransaction, ExpiryDate, DiscountRate, DiscountStartDate, DiscountEndDate, VendorID)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$insertBatch->bind_param("siddssssssi", $BatchNo, $ProductID, $BuyingRate, $SellingRate, $DateOfTransaction, $TimeOfTransaction, $ExpiryDate, $DiscountRate, $DiscountStartDate, $DiscountEndDate, $VendorID);
$insertBatch->execute();

// 2. Get the newly inserted BatchID
$BatchID = $conn->insert_id; // This gets the last inserted ID (BatchID)

// 3. Update or Insert into Stock Table
$stockCheck = $conn->prepare("SELECT Quantity FROM stocktable WHERE ProductID = ?");
$stockCheck->bind_param("i", $ProductID);
$stockCheck->execute();
$stockResult = $stockCheck->get_result();

if ($stockResult->num_rows > 0) {
    $updateStock = $conn->prepare("UPDATE stocktable SET Quantity = Quantity + ? WHERE ProductID = ?");
    $updateStock->bind_param("ii", $Quantity, $ProductID);
    $updateStock->execute();
} else {
    $minStock = 10;
    $insertStock = $conn->prepare("INSERT INTO stocktable (ProductID, Quantity, MinStockLevel) VALUES (?, ?, ?)");
    $insertStock->bind_param("iii", $ProductID, $Quantity, $minStock);
    $insertStock->execute();
}

// 4. Insert into Stock Movement Table (using BatchID)
$type = "In";
$insertMovement = $conn->prepare("INSERT INTO stockmovement (ProductID, BatchID, Quantity, Type, TransactionDate)
VALUES (?, ?, ?, ?, ?)");
$insertMovement->bind_param("iiiss", $ProductID, $BatchID, $Quantity, $type, $DateOfTransaction);
$insertMovement->execute();

$conn->close();
    echo "<script>alert('Batch saved successfully!'); window.location.href='batchform.php';</script>";
?> 