<?php
require 'db.php';

$productResult = $conn->query("SELECT ProductID, ProductName FROM producttable");
$vendorResult = $conn->query("SELECT VendorID, VendorName FROM vendortable");
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<style>
/* General Body Styling */
body {
    font-family: 'Arial', sans-serif;
 background: linear-gradient(to right, #D3BBDD, #F5F1F6); /* Soft gradient from #D3BBDD to light gray */
    color:black;
    padding: 40px;
    margin: 0;
}

/* Heading Styling */
h2 {
    text-align: center;
    font-size: 28px; /* Medium size */
    color: #533153; /* Dark purple text color */
    font-weight: 600;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    margin-bottom: 30px;
}

/* Form Container Styling */
.form-container {
    max-width: 650px; /* Slightly smaller form width */
    margin: auto;
    padding: 35px;
    border-radius: 12px;
    background-color: #FFFFFF;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border: 1px solid #C8A2C8; /* Soft purple border */
    font-family: 'Playfair Display', serif;
    
}

/* Input and Select Styling */
select, input[type="number"], input[type="text"], input[type="email"], input[type="date"], input[type="time"], textarea {
    padding: 14px 18px;
    border-radius: 8px;
    border: 1px solid #C8A2C8; /* Light purple border */
    background-color: #F5F1F6; /* Soft neutral purple background */
    color: #333;
    font-size: 16px;
    width: 100%;
    box-sizing: border-box;
}

/* Remove focus color change */
select:focus, input:focus {
    border-color: #704270; /* Deeper purple for focus state */
    background-color: #F5F1F6;
    outline: none;
}

/* General button styling for both <input> and <button> */
button, input[type="submit"] {
    background-color:rgb(191, 92, 233); /* Muted dark purple button */
    color: #fff;
    border: none;
    padding: 14px 22px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
    margin-top: 20px;
    font-weight: 600;
    text-align: center;
}

/* Hover effect for both buttons */
button:hover, input[type="submit"]:hover {
    background-color: #533153; /* Slightly darker purple on hover */
}

/* Styling for <button> tags */
button {
    width: auto;
}

/* Styling for <input type="submit"> */
input[type="submit"] {
    width: auto;
    display: inline-block;
}

/* Label Styling */
label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
  color : black; /* color: #704270; /* Muted purple for labels */
}

/* Textarea Styling */
textarea {
    resize: vertical;
    height: 120px;
    background-color: #F5F1F6; /* Matching the input field's background */
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-container {
        width: 100%;
        padding: 15px;
    }

    button {
        width: 100%;
    }
}
</style>









</head>
<body>
    <div class="form-container">
        <h2>Batch Entry Form</h2>
        <form action="batchphp.php" method="POST">
            <div class="form-grid">
                <div>
                    <label>Batch No</label>
                    <input type="text" name="BatchNo" required>
                </div>
                <div>
                    <label>Product</label>
                    <select name="ProductID" required>
                        <option value="">-- Select Product --</option>
                        <?php 
                        if ($productResult->num_rows > 0) {
                            while ($row = $productResult->fetch_assoc()) {
                                echo "<option value='{$row['ProductID']}'>{$row['ProductName']}</option>";
                            }
                        } else {
                            echo "<option disabled>No products found</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label>Buying Rate</label>
                    <input type="number" name="BuyingRate" step="0.01" required>
                </div>
                <div>
                    <label>Selling Rate</label>
                    <input type="number" name="SellingRate" step="0.01" required>
                </div>
                <div>
                    <label>Date</label>
                    <input type="date" name="DateOfTransaction">
                </div>
                <div>
                    <label>Time</label>
                    <input type="time" name="TimeOfTransaction">
                </div>
                <div>
                    <label>Quantity</label>
                    <input type="number" name="Quantity" required>
                </div>
                <div>
                    <label>Expiry Date</label>
                    <input type="date" name="ExpiryDate">
                </div>
                <div>
                    <label>Discount Rate (%)</label>
                    <input type="number" name="DiscountRate" step="0.01">
                </div>
                <div>
                    <label>Discount Start Date</label>
                    <input type="date" name="DiscountStartDate">
                </div>
                <div>
                    <label>Discount End Date</label>
                    <input type="date" name="DiscountEndDate">
                </div>
                <div>
                    <label>Vendor</label>
                    <select name="VendorID" required>
                        <option value="">-- Select Vendor --</option>
                        <?php 
                        if ($vendorResult->num_rows > 0) {
                            while ($row = $vendorResult->fetch_assoc()) {
                                echo "<option value='{$row['VendorID']}'>{$row['VendorName']}</option>";
                            }
                        } else {
                            echo "<option disabled>No vendors found</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="submit" value="Save Batch">
            </div>
        </form>
    </div>
</body>
</html>
