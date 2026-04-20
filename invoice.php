<?php
require 'db.php';

$billID = $_GET['bill_id'] ?? 0;
$billingMode = $_GET['billingMode'] ?? 'both'; // Default to 'both' if not set

if (!$billID) {
    echo "Invalid invoice request.";
    exit;
}

// Fetch Bill Info
$billSQL = "SELECT * FROM billtable WHERE BillID = ?";
$stmt = $conn->prepare($billSQL);
$stmt->bind_param("i", $billID);
$stmt->execute();
$billResult = $stmt->get_result();
$bill = $billResult->fetch_assoc();
$stmt->close();

if (!$bill) {
    echo "Bill not found.";
    exit;
}

// Query for individual products (exclude collection products)
$productSales = [];
if ($billingMode === 'products' || $billingMode === 'both') {
    $productSQL = "SELECT p.ProductName, s.Quantity, s.UnitPrice, (s.Quantity * s.UnitPrice) as Total
                   FROM salestable s
                   JOIN producttable p ON p.ProductID = s.ProductID
                   WHERE s.BillID = ? AND s.CollectionID IS NULL";
    $stmt = $conn->prepare($productSQL);
    $stmt->bind_param("i", $billID);
    $stmt->execute();
    $productSales = $stmt->get_result();
    $stmt->close();
}

// Query for collection products
// Query for collection products
$collectionSales = [];
if ($billingMode === 'collections' || $billingMode === 'both') {
    // Updated query to fetch collections and their associated products
 // Updated SQL to select CollectionID along with CollectionName and Price
$collectionSQL = "SELECT c.CollectionID, c.CollectionName, c.Price, p.ProductName 
                  FROM collectionproducttable cp
                  JOIN collectiontable c ON c.CollectionID = cp.CollectionID
                  JOIN producttable p ON p.ProductID = cp.ProductID
                  JOIN salestable s ON s.CollectionID = cp.CollectionID
                  WHERE s.BillID = ?
                  GROUP BY c.CollectionID, p.ProductID"; // Group by collection and product


    // Prepare the statement and check for errors
    $stmt = $conn->prepare($collectionSQL);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("i", $billID);  // Bind BillID parameter

    // Execute the statement
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    // Fetch the results
    $collectionSales = $stmt->get_result();
    $stmt->close();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $billID ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
     <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&display=swap" rel="stylesheet">
<!-- Add this in your <head> section for Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&display=swap" rel="stylesheet">
<!-- Include this in your <head> if you haven't already -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Include Font Awesome in <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <style>
    /* Base Styling */
        body::before {
        content: "";
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        background-image: 
        linear-gradient(rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.3))

            url("images/vecteezy_charming-display-of-dried-flowers-and-herbs-in-glass-jars-on_55302710.jpeg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        z-index: -1;
        pointer-events: none;
        
            font-family: "Playfair Display", serif;/*font-family: 'Arial', sans-serif;*/
            /*background: linear-gradient(to right, #DAE7DD, #F7C9B6);*/
            color: #333;
            padding: 40px;
            margin: 0;
            overflow-x: hidden;
        }



.shop-name-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px 20px;
    text-align: center;
    backdrop-filter: blur(4px);
    background-color: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    margin: 20px auto;
    width: fit-content;
    max-width: 95%;
    gap: 15px;
}

.shop-name-icon {
    font-size: 2.8em;

    color: #7B1E1E; /* Deep elegant maroon */
    text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
}

.shop-name {
    font-family: 'Cinzel Decorative', serif;
    font-size: 3.2em;
    font-weight: 700;
    color: #7B1E1E; /* Deep elegant maroon */
    letter-spacing: 2px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5), 0 0 10px rgba(255, 255, 255, 0.1);
    padding: 0 10px;
}






    h2, h3  {
        text-align: center;
        /*color: #555;*/
        margin-top: 0;
        font-family: "Playfair Display", serif; /*font-family: 'Poppins', sans-serif;*/
        color:black;
        font-size:30px;
       
        
    }

    .container {
        max-width: 900px;
        margin: auto;
         /*background: linear-gradient(to right, #DAE7DD, #F7C9B6); */
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border: 2px solid #DBC39A;
    }
   

    .invoice-header {
        margin-bottom: 30px;
       
    }
    /*.invoice-header p{
        color:	 #E6DCCF; 
        font-size:25px;
        font-weight: bold;
        
    }*/
.invoice-header p {
    background-color: rgba(255, 255, 255, 0.85);
    display: inline-block;         /* Only as wide as the content */
    padding: 8px 16px;
    border-radius: 10px;
    color: #222;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    margin-top: 10px;
    text-align: left;
     font: size 14px;
}

.invoice-header .label {
    font-weight: bold;
    margin-right: 10px;
     
}

.invoice-header .value {
   color: #8B0000;
    font-weight: bold;
    font-size: 1.4em;
    background-color: rgba(255, 255, 255, 0.85);
}


    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
    }

    th, td {
        border: 1px solid #c8c8c8;
        padding: 12px;
        text-align: center;
        font-size: 18px;
        color:black;
        
    }

    th {
        background-color: #F7C9B6;
        color: #333;
        font-weight: bold;
        font-size: 18px;
        color:black;
    }

    tr:nth-child(even) {
        background-color: #DAE7DD;
    }

    /* Summary Section */
    .summary {
        margin-top: 30px;
        font-size:20px;
        /*color: #333;*/ color:black;
        text-align: center;

    }

   
.summary p {
    background-color: rgba(255, 255, 255, 0.85); /* Light background for better contrast */
    display: block;  /* Makes the <p> take full width */
    width: 100%;  /* Ensures it stretches fully */
    padding: 8px 16px;
    border-radius: 10px;
    color: #222;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;  /* Ensures padding is included in the width */
    margin-top: 10px;  /* Adds space between paragraphs */
    display: flex;       /* Applies flex layout */
    justify-content: space-between;  /* Aligns items to the left and right */
    align-items: center;  /* Centers the items vertically */
}

.summary .value {
    display: block;                /* Moves to next line */
    width: auto;                   /* Adjusts the width based on content */
    color: #8B0000;
    font-weight: bold;
    font-size: 1.4em;
    background-color: rgba(255, 255, 255, 0.85);
    padding: 4px 10px;
    border-radius: 8px;
    box-sizing: border-box;       /* Includes padding in width */
}

    /* Print Button */
    .print-btn {
        text-align: center;
        margin-top: 20px;
    }

    .print-btn button {
        background-color: #DACAC4;
        color: #333;
        border: none;
        padding: 12px 25px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .print-btn button:hover {
        background-color: #A79C90;
        transform: scale(1.05);
    }




    /* Responsive Design */
    @media screen and (max-width: 768px) {
        body {
            padding: 20px;
        }

        .container {
            padding: 20px;
        }

        h2 {
            font-size: 24px;
        }

        h3 {
            font-size: 18px;
        }

        .summary {
            font-size: 1em;
        }

        .print-btn button {
            font-size: 15px;
            padding: 10px 20px;
        }
    }
</style>




</head>
<body>
    <div class="container">
        <div class="invoice-header">
      <!-- Shop name with icon -->
        <div class="shop-name-container">
            <i class="fas fa-seedling shop-name-icon"></i> <!-- Try different icons from the list below -->
            <div class="shop-name">Herbal Harmony</div>
        </div>

        <h3>Invoice #: <?= $billID ?></h3>

        <p>
            <span class="label">Date:</span>
            <span class="value"><?= $bill['BillDate'] ?></span>
        </p><br>

        <p>
            <span class="label">Payment Method:</span>
            <span class="value"><?= $bill['PaymentMethod'] ?></span>
        </p>
    </div>


    <!-- Displaying Products -->
    <?php if ($billingMode === 'products' || $billingMode === 'both'): ?>
        <!-- ✅ PRODUCT TABLE DISPLAY -->
        <h3>Products</h3>
        <table border="1">
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
            <?php while ($row = $productSales->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ProductName'] ?></td>
                    <td><?= $row['Quantity'] ?></td>
                    <td><?= number_format($row['UnitPrice'], 2) ?></td>
                    <td><?= number_format($row['Total'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>


    <?php if ($billingMode === 'collections' || $billingMode === 'both'): ?>
        <!-- ✅ COLLECTION TABLE DISPLAY -->
        <h3>Collections</h3>
        <table border="1">
            <tr>
                <th>Collection Name</th>
                <th>Price</th>
                <th>Products</th>
            </tr>
            <?php
            $lastCollectionID = null;
            $productList = [];
            $collectionPrice = 0;
            while ($row = $collectionSales->fetch_assoc()):
                if ($row['CollectionID'] !== $lastCollectionID):
                    if ($lastCollectionID !== null):
                        echo "<tr>
                                <td>{$lastCollectionName}</td>
                                <td>" . number_format($collectionPrice, 2) . "</td>
                                <td>" . implode(', ', $productList) . "</td>
                            </tr>";
                    endif;
                    $lastCollectionID = $row['CollectionID'];
                    $lastCollectionName = $row['CollectionName'];
                    $collectionPrice = $row['Price'];
                    $productList = [$row['ProductName']];
                else:
                    $productList[] = $row['ProductName'];
                endif;
            endwhile;
            if ($lastCollectionID !== null):
                echo "<tr>
                        <td>{$lastCollectionName}</td>
                        <td>" . number_format($collectionPrice, 2) . "</td>
                        <td>" . implode(', ', $productList) . "</td>
                    </tr>";
            endif;
            ?>
        </table>
    <?php endif; ?>





        <?php if ($productSales->num_rows === 0 && $collectionSales->num_rows === 0): ?>
            <p style="text-align:center; font-weight:bold; margin-top: 20px; color: red;">
                

             echo "<script>alert(' No items billed in this invoice.'); window.location.href='billcollection.php';</script>";
          

            </p>
        <?php endif; ?>


       <div class="summary">
            <p><strong>Total Amount:</strong> 
                <span class="value">₹<?= number_format($bill['TotalAmount'], 2) ?></span>
            </p>
            <p><strong>Discount:</strong> 
                <span class="value">₹<?= number_format($bill['Discount'], 2) ?></span>
            </p>
            <p><strong>Final Amount:</strong> 
                <span class="value">₹<?= number_format($bill['FinalAmount'], 2) ?></span>
            </p>
        </div>

        <div class="print-btn">
            <button onclick="window.print()">🖨️ Print Invoice</button>
            <button onclick="window.location.href='billcollection.php'">🔙 Back to Billing</button>
        </div>
    </div>
</body>
</html>
