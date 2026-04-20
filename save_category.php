<?php
require 'db.php'; // Ensure this connects properly to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $CategoryName = trim($_POST['CategoryName']);
    $Description = trim($_POST['Description']);

    // Ensure category name is not empty
    if (empty($CategoryName)) {
        die("<script>alert('Category Name is required!'); window.history.back();</script>");
    }

    // Correct table name: categorytable
    $query = "INSERT INTO categorytable (CategoryName, Description) VALUES (?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("SQL Error: " . $conn->error); // Debugging error
    }

    // Bind parameters
    $stmt->bind_param("ss", $CategoryName, $Description);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Category added successfully!'); window.location.href='categoryfinal.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
