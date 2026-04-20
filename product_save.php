<?php
require 'db.php';

// Fetch categories for the dropdown
$categoryQuery = "SELECT CategoryID, CategoryName FROM categorytable";
$categoryResult = $conn->query($categoryQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST["productName"];
    $categoryID = $_POST["categoryID"];

    // Get the category name based on the selected category ID
    $categoryNameQuery = "SELECT CategoryName FROM categorytable WHERE CategoryID = ?";
    $stmt = $conn->prepare($categoryNameQuery);
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $stmt->bind_result($categoryName);
    $stmt->fetch();
    $stmt->close();

    if ($categoryName) {
        // Auto-generate Product ID (incremental)
        $getMaxIDQuery = "SELECT MAX(ProductID) + 1 AS NewID FROM producttable";
        $maxIDResult = $conn->query($getMaxIDQuery);
        $row = $maxIDResult->fetch_assoc();
        $productID = $row["NewID"] ?? 1; // If no records exist, start from 1

        // Insert into product table
        $insertQuery = "INSERT INTO producttable (ProductID, ProductName, CategoryID, CategoryName) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("isis", $productID, $productName, $categoryID, $categoryName);

        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully! Product ID: $productID'); window.location.href='product_save.php';</script>";
          
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>Invalid Category ID.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap"
      rel="stylesheet"
    />


<style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #EED6D3;  /* Soft light pink background */
            color: #67595E;  /* Dark muted brown for text */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h2 {
            color: #67595E;  /* Dark muted brown for the header */
            text-align: center;
            margin-bottom: 25px;
            font-size: 32px;
            font-weight: 600;
             font-family: "Playfair Display", serif;
        }

        /* Form Container */
        form {
            background-color:#F9F1F0;
            padding: 30px;
            width: 100%;
            max-width: 600px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Input and Select Styling */
        form label {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #67595E;  /* Dark muted brown */

        }

        form input, form select {
            padding: 14px;
            font-size: 16px;
            border: 1px solid #A49393;  /* Soft brown border */
            border-radius: 8px;
            background-color: transparent; /* No background color */
            color: #67595E;  /* Dark text color for input fields */
            transition: all 0.3s ease;
        }

        form input:focus, form select:focus {
            border-color: #67595E;  /* Dark brown border on focus */
            outline: none;
        }

        /* Button Styling */
        form button {
            padding: 14px;
            font-size: 16px;
            color: white;
            background-color: #67595E;  /* Dark brown button */
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        form button:hover {
            background-color: #A49393;  /* Soft brown button hover */
        }

        /* Success/Error Message Styling */
        p {
            text-align: center;
            font-size: 18px;
            font-weight: 500;
        }

        p.success {
            color: #27ae60;
        }

        p.error {
            color: #e74c3c;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            form {
                padding: 20px;
                width: 85%;
            }

            h2 {
                font-size: 28px;
            }
        }
    </style>


</head>
<body>

<h2>Add New Product</h2>

<form action="product_save.php"  method="POST">
    <label for="productName">Product Name:</label>
    <input type="text" name="productName" required>

    <label for="categoryID">Category:</label>
    <select name="categoryID" required>
        <option value="">Select Category</option>
        <?php
        while ($row = $categoryResult->fetch_assoc()) {
            echo "<option value='" . $row['CategoryID'] . "'>" . $row['CategoryID'] . " - " . $row['CategoryName'] . "</option>";
        }
        ?>
    </select>

    <button type="submit">Save Product</button>
</form>

</body>
</html>