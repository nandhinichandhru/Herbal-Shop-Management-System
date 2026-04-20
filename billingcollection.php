<?php
require 'db.php';

// Step 1: Get form data
$product_ids = $_POST['product_ids'] ?? [];
$batch_ids = $_POST['batch_ids'] ?? [];
$quantities = $_POST['quantities'] ?? [];
$unit_prices = $_POST['unit_prices'] ?? [];
$discounts = $_POST['discounts'] ?? [];
$final_amounts = $_POST['final_amounts'] ?? [];
$collection_ids = $_POST['collection_ids'] ?? [];
$collection_qtys = $_POST['collection_quantities'] ?? [];

$total = $_POST['totalAmount'] ?? 0;
$discount = $_POST['overallDiscount'] ?? 0;
$final = $_POST['finalAmount'] ?? 0;
$payment = $_POST['paymentMethod'] ?? '';

$billDate = date("Y-m-d");

// Step 1: Validate that at least one product or collection is selected
$product_ids = isset($_POST['product_ids']) ? $_POST['product_ids'] : [];
$collection_ids = isset($_POST['collection_ids']) ? $_POST['collection_ids'] : [];

if (empty($product_ids) && empty($collection_ids)) {
    echo "<script>alert('❗Please select at least one product or collection to continue billing'); window.history.back();</script>";
    exit;
}

// --------------------------------------------------------------------------
// STEP 0: Check for expired batches with available stock and mark them as 'Expired'
// --------------------------------------------------------------------------

$expiredBatchQuery = "
    SELECT 
        b.BatchID, 
        b.ProductID, 
        SUM(CASE WHEN sm.Type = 'In' THEN sm.Quantity ELSE 0 END) AS InQty,
        SUM(CASE WHEN sm.Type IN ('Sale', 'Expired') THEN sm.Quantity ELSE 0 END) AS OutQty,
        (SUM(CASE WHEN sm.Type = 'In' THEN sm.Quantity ELSE 0 END) - 
         SUM(CASE WHEN sm.Type IN ('Sale', 'Expired') THEN sm.Quantity ELSE 0 END)) AS remainingQty
    FROM batch b
    LEFT JOIN stockmovement sm ON sm.BatchID = b.BatchID
    WHERE DATE(b.ExpiryDate) <= CURDATE()
    GROUP BY b.BatchID, b.ProductID
    HAVING remainingQty > 0
";

$expiredResult = $conn->query($expiredBatchQuery);

if ($expiredResult === false) {
    die(" Error executing query: " . $conn->error);
}

if ($expiredResult->num_rows === 0) {
  echo "<script>alert('No expired batches found.');</script>";
} else {
    echo "<script>alert('Expired batches found. Proceeding to insert into stock movement.');</script>";

    while ($expired = $expiredResult->fetch_assoc()) {
        $batchID = $expired['BatchID'];
        $productID = $expired['ProductID'];
        $qty = $expired['remainingQty'];

echo "<script>alert('Found expired batch: BatchID = $batchID, ProductID = $productID, RemainingQty = $qty');</script>";


        // Insert 'Expired' record into stockmovement
        $insertExpired = $conn->prepare("
            INSERT INTO stockmovement (ProductID, BatchID, Quantity, Type, TransactionDate)
            VALUES (?, ?, ?, 'Expired', NOW())
        ");
        $insertExpired->bind_param("iii", $productID, $batchID, $qty);

        if ($insertExpired->execute()) {
          echo "<script>alert('Inserted expired entry for BatchID $batchID');</script>";
        } else {
            echo " Error inserting expired entry: " . $insertExpired->error . "<br>";
        }

        $insertExpired->close();

        // Reduce stock in stocktable for expired quantity
        $updateStock = $conn->prepare("
            UPDATE stocktable
            SET Quantity = Quantity - ?
            WHERE ProductID = ?
        ");
        $updateStock->bind_param("ii", $qty, $productID);

        if ($updateStock->execute()) {
            echo "<script>alert('Updated stock for ProductID $productID');</script>";
        } else {
            echo " Error updating stock: " . $updateStock->error . "<br>";
        }

        $updateStock->close();
    }
}

// --------------------------------------------------------------------------
// Step 1: Product stock level validation before processing bill
// --------------------------------------------------------------------------

if (is_array($product_ids) && is_array($quantities)) {
    for ($i = 0; $i < count($product_ids); $i++) {
        $productID = trim($product_ids[$i]);
        $neededQty = $quantities[$i];

        // Get current stock and product info
        $stockSQL = "SELECT s.Quantity, s.MinStockLevel, p.ProductName 
                     FROM stocktable s 
                     JOIN producttable p ON s.ProductID = p.ProductID 
                     WHERE s.ProductID = $productID";
                     
        $stockRes = $conn->query($stockSQL);

        if (!$stockRes || $stockRes->num_rows === 0) {
            echo "<script>alert('Error: Product ID $productID not found in stocktable. Cannot proceed.'); window.location.href='billcollection.php';</script>";
            exit;
        }

        $stockRow = $stockRes->fetch_assoc();
        $availableQty = $stockRow['Quantity'];
        $minStockLevel = $stockRow['MinStockLevel'];
        $productName = $stockRow['ProductName'];

        // Alert if insufficient stock (allow partial billing)
        if ($availableQty < $neededQty) {
            echo "<script>alert('⚠️ Insufficient stock for $productName (Product ID $productID). Requested: $neededQty, Available: $availableQty. Proceeding with partial sale.');</script>";
        }

        // Alert if billing reduces stock below minimum level
        if (($availableQty - $neededQty) <= $minStockLevel) {
            echo "<script>alert('⚠️ Warning: Selling $neededQty of $productName will bring stock below minimum level ($minStockLevel). Available: $availableQty.');</script>";
        }
    }
}

// --------------------------------------------------------------------------
// Step 1.5: Collection stock validation - prevent billing if any product is out of stock
// --------------------------------------------------------------------------

if (!empty($collection_ids) && is_array($collection_ids)) {
    foreach ($collection_ids as $index => $collectionID) {
        $collectionQty = (int)($collection_qtys[$index] ?? 0);

        // Get all products in this collection
        $collectionSQL = "
            SELECT cp.ProductID, p.ProductName, s.Quantity, s.MinStockLevel
            FROM collectionproducttable cp
            JOIN stocktable s ON s.ProductID = cp.ProductID
            JOIN producttable p ON p.ProductID = cp.ProductID
            WHERE cp.CollectionID = ?
        ";
        $stmt = $conn->prepare($collectionSQL);
        $stmt->bind_param("i", $collectionID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $productID = $row['ProductID'];
            $productName = $row['ProductName'];
            $availableQty = $row['Quantity'];
            $minStockLevel = $row['MinStockLevel'];
            $totalNeeded = $collectionQty; // Assuming 1 unit of each product per collection

            // Check if available quantity is 0, preventing billing
            if ($availableQty <= 0) {
                echo "<script>alert('❌ Insufficient stock for $productName in Collection ID $collectionID. Stock is 0. Billing cannot proceed.'); window.location.href='billingcollection.php';</script>";
                exit;
            }

            if ($availableQty < $totalNeeded) {
                echo "<script>alert('⚠️ Warning: Not enough stock for $productName in Collection ID $collectionID. Requested: $totalNeeded, Available: $availableQty. Proceeding anyway.');</script>";
            }

            if (($availableQty - $totalNeeded) <= $minStockLevel) {
                echo "<script>alert('⚠️ Selling $totalNeeded of $productName in Collection ID $collectionID will bring stock below minimum level ($minStockLevel). Proceeding anyway.');</script>";
            }
        }
        $stmt->close();
    }
}

// Step 2: Begin Transaction
$conn->begin_transaction();

try {
    // Insert into Bill Table
    $insertBill = "INSERT INTO billtable (BillDate, TotalAmount, Discount, FinalAmount, PaymentMethod) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertBill);
    if (!$stmt) {
        throw new Exception("Error preparing bill insertion: " . $conn->error);
    }
    $stmt->bind_param("sddds", $billDate, $total, $discount, $final, $payment);
    if (!$stmt->execute()) {
        throw new Exception("Error executing bill insertion: " . $stmt->error);
    }
    $billID = $conn->insert_id; // Get the generated Bill ID
    if (!$billID) {
        throw new Exception("Failed to get Bill ID.");
    }
    $stmt->close();





 // -----------------------------------------
    // Step 3: Process Individual Product Sales
    // -----------------------------------------
    foreach ($product_ids as $index => $productID) {
        $neededQty = (int)$quantities[$index];
        $remainingQty = $neededQty;

        // Fetch batches FIFO-wise (non-expired)
        $batchSQL = "SELECT * FROM batch WHERE ProductID = ? AND ExpiryDate > CURDATE() ORDER BY ExpiryDate ASC, DateOfTransaction ASC";
        $batchStmt = $conn->prepare($batchSQL);
        if (!$batchStmt) {
            throw new Exception("Error preparing batch selection: " . $conn->error);
        }
        $batchStmt->bind_param("i", $productID);
        $batchStmt->execute();
        $batchResult = $batchStmt->get_result();

        // Process each batch for the product
        while ($row = $batchResult->fetch_assoc()) {
            if ($remainingQty <= 0) break; // Exit if all required quantity is processed

            $batchID = $row['BatchID'];
            $sellingRate = $row['SellingRate'];
            $discountRate = $row['DiscountRate'];
            $discountStart = $row['DiscountStartDate'];
            $discountEnd = $row['DiscountEndDate'];
            $today = date("Y-m-d");

            // Check discount based on date
            $discountAmount = 0;
            if ($discountStart <= $today && $today <= $discountEnd) {
                $discountAmount = ($sellingRate * $discountRate) / 100;
            }
            $finalRate = $sellingRate - $discountAmount;

            // Get available batch quantity
            $qtySQL = "SELECT 
                SUM(CASE WHEN Type = 'In' THEN Quantity ELSE 0 END) AS InQty,
                SUM(CASE WHEN Type IN ('Sale', 'Expired') THEN Quantity ELSE 0 END) AS OutQty
                FROM stockmovement 
                WHERE ProductID = ? AND BatchID = ?";
            $qtyStmt = $conn->prepare($qtySQL);
            if (!$qtyStmt) {
                throw new Exception("Error preparing quantity calculation: " . $conn->error);
            }
            $qtyStmt->bind_param("ii", $productID, $batchID);
            $qtyStmt->execute();
            $qtyRes = $qtyStmt->get_result()->fetch_assoc();
            $available = ($qtyRes['InQty'] ?? 0) - ($qtyRes['OutQty'] ?? 0);
            $qtyStmt->close();

            if ($available <= 0) continue; // Skip if no stock is available

            $sellQty = min($available, $remainingQty);
            $totalPrice = $sellQty * $finalRate;

            // Insert into salestable
            $saleSQL = "INSERT INTO salestable (BillID, BatchID, ProductID, Quantity, UnitPrice, TotalPrice) 
                        VALUES (?, ?, ?, ?, ?, ?)";
            $saleStmt = $conn->prepare($saleSQL);
            if (!$saleStmt) {
                throw new Exception("Error preparing sale insertion: " . $conn->error);
            }
            $saleStmt->bind_param("iiiddi", $billID, $batchID, $productID, $sellQty, $finalRate, $totalPrice);
            if (!$saleStmt->execute()) {
                throw new Exception("Error executing sale insertion: " . $saleStmt->error);
            }
            $saleStmt->close();

            // Insert into stockmovement (type = 'Sale')
            $moveSQL = "INSERT INTO stockmovement (ProductID, BatchID, Quantity, Type, TransactionDate) 
                        VALUES (?, ?, ?, 'Sale', NOW())";
            $moveStmt = $conn->prepare($moveSQL);
            if (!$moveStmt) {
                throw new Exception("Error preparing stock movement insertion: " . $conn->error);
            }
            $moveStmt->bind_param("iii", $productID, $batchID, $sellQty);
            $moveStmt->execute();
            $moveStmt->close();

            // Update stocktable
            $stockUpdate = "UPDATE stocktable SET Quantity = Quantity - ? WHERE ProductID = ?";
            $stockStmt = $conn->prepare($stockUpdate);
            if (!$stockStmt) {
                throw new Exception("Error preparing stock update: " . $conn->error);
            }
            $stockStmt->bind_param("ii", $sellQty, $productID);
            $stockStmt->execute();
            $stockStmt->close();

            $remainingQty -= $sellQty; // Update remaining quantity
        }
        $batchStmt->close();
    }

    // -----------------------------------------
    // Step 4: Process Combo Collections
    // -----------------------------------------
    foreach ($collection_ids as $key => $collectionID) {
        $qty = (int)$collection_qtys[$key];

        // Get all products in the collection
        $collSQL = "SELECT ProductID FROM collectionproducttable WHERE CollectionID = ?";
        $collStmt = $conn->prepare($collSQL);
        if (!$collStmt) {
            throw new Exception("Error preparing collection selection: " . $conn->error);
        }
        $collStmt->bind_param("i", $collectionID);
        $collStmt->execute();
        $collResult = $collStmt->get_result();

        while ($row = $collResult->fetch_assoc()) {
            $productID = $row['ProductID'];
            $totalQty = $qty;

            // Reuse same FIFO logic for batch processing
            $batchSQL = "SELECT * FROM batch WHERE ProductID = ? AND ExpiryDate > CURDATE() ORDER BY ExpiryDate ASC, DateOfTransaction ASC";
            $batchStmt = $conn->prepare($batchSQL);
            if (!$batchStmt) {
                throw new Exception("Error preparing batch selection for collection: " . $conn->error);
            }
            $batchStmt->bind_param("i", $productID);
            $batchStmt->execute();
            $batchResult = $batchStmt->get_result();

            // Process batches for each product in the collection
            while ($batch = $batchResult->fetch_assoc()) {
                if ($totalQty <= 0) break;

                $batchID = $batch['BatchID'];
                $rate = $batch['SellingRate'];
                $discRate = $batch['DiscountRate'];
                $start = $batch['DiscountStartDate'];
                $end = $batch['DiscountEndDate'];
                $today = date("Y-m-d");

                $discount = 0;
                if ($start <= $today && $today <= $end) {
                    $discount = ($rate * $discRate) / 100;
                }
                $finalRate = $rate - $discount;

                // Get available quantity in batch
                $qtySQL = "SELECT 
                            SUM(CASE WHEN Type = 'In' THEN Quantity ELSE 0 END) AS InQty,
                            SUM(CASE WHEN Type IN ('Sale', 'Expired') THEN Quantity ELSE 0 END) AS OutQty
                           FROM stockmovement 
                           WHERE ProductID = ? AND BatchID = ?";
                $qtyStmt = $conn->prepare($qtySQL);
                if (!$qtyStmt) {
                    throw new Exception("Error preparing quantity calculation for collection: " . $conn->error);
                }
                $qtyStmt->bind_param("ii", $productID, $batchID);
                $qtyStmt->execute();
                $qtyRes = $qtyStmt->get_result()->fetch_assoc();
                $available = ($qtyRes['InQty'] ?? 0) - ($qtyRes['OutQty'] ?? 0);
                $qtyStmt->close();

                $sellQty = min($available, $totalQty);
                $totalPrice = $sellQty * $finalRate;

                // Insert sale for collection
                $saleSQL = "INSERT INTO salestable (BillID, BatchID, ProductID, CollectionID, Quantity, UnitPrice, TotalPrice) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                $saleStmt = $conn->prepare($saleSQL);
                if (!$saleStmt) {
                    throw new Exception("Error preparing sale insertion for collection: " . $conn->error);
                }
                $saleStmt->bind_param("iiiiidi", $billID, $batchID, $productID, $collectionID, $sellQty, $finalRate, $totalPrice);
                if (!$saleStmt->execute()) {
                    throw new Exception("Error executing sale insertion for collection: " . $saleStmt->error);
                }
                $saleStmt->close();

                // Insert into stockmovement
                $moveSQL = "INSERT INTO stockmovement (ProductID, BatchID, Quantity, Type, TransactionDate) 
                            VALUES (?, ?, ?, 'Sale', NOW())";
                $moveStmt = $conn->prepare($moveSQL);
                if (!$moveStmt) {
                    throw new Exception("Error preparing stock movement for collection: " . $conn->error);
                }
                $moveStmt->bind_param("iii", $productID, $batchID, $sellQty);
                $moveStmt->execute();
                $moveStmt->close();

                // Update stock
                $stockUpdate = "UPDATE stocktable SET Quantity = Quantity - ? WHERE ProductID = ?";
                $stockStmt = $conn->prepare($stockUpdate);
                if (!$stockStmt) {
                    throw new Exception("Error preparing stock update for collection: " . $conn->error);
                }
                $stockStmt->bind_param("ii", $sellQty, $productID);
                $stockStmt->execute();
                $stockStmt->close();

                $totalQty -= $sellQty;
            }
            $batchStmt->close();
        }
        $collStmt->close();
    }

  // -----------------------------------------
    // Step 5: Commit Transaction
    // -----------------------------------------
    $conn->commit();

    // Display success message
    echo "<div id='success-message' style='color: green; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; margin: 20px;'>
            <strong>Billing Successful!</strong> Your bill has been processed successfully.
          </div>";

    // Redirect to invoice page
    header("Location: invoice.php?bill_id=$billID");
    exit;

} catch (Exception $e) {
    // Rollback if any error occurs
    $conn->rollback();

    // Display the error message inside a div
    echo "<div id='error-message' style='color: red; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; margin: 20px;'>
            <strong>Error:</strong> " . $e->getMessage() . "
          </div>";
}
?>

