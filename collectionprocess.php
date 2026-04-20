<?php
// Connect to database
$conn = new mysqli("localhost", "root", "pooja2005@sql", "sample");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$collection_name = $_POST['collection_name'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];
$product_ids = $_POST['product_ids']; // this is an array

// Insert into collectiontable
$insertCollection = $conn->prepare("INSERT INTO collectiontable (CollectionName, Price, Quantity) VALUES (?, ?, ?)");
$insertCollection->bind_param("sdi", $collection_name, $price, $quantity);
$insertCollection->execute();

// Get the ID of the inserted collection
$collection_id = $conn->insert_id;

// Insert into collectionproducttable for each selected product
$insertProduct = $conn->prepare("INSERT INTO collectionproducttable (CollectionID, ProductID) VALUES (?, ?)");
foreach ($product_ids as $product_id) {
    $insertProduct->bind_param("ii", $collection_id, $product_id);
    $insertProduct->execute();
}

// Close everything
$insertProduct->close();
$insertCollection->close();
$conn->close();

echo "<script>alert('Collection added successfully!'); window.location.href='collectionproductform.php';</script>";

?>