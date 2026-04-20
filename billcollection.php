<?php
require 'db.php';

// Fetch Individual Products (from producttable + batch + stock + stockmovement)
$productQuery = "
    SELECT 
        p.ProductID, p.ProductName, 
        b.BatchID, b.BatchNo, b.SellingRate, b.DiscountRate, 
        b.DiscountStartDate, b.DiscountEndDate, b.ExpiryDate,
        s.Quantity AS totalProductQty, s.MinStockLevel,
        COALESCE(SUM(CASE WHEN sm.Type = 'In' THEN sm.Quantity ELSE 0 END), 0) - 
        COALESCE(SUM(CASE WHEN sm.Type = 'Sale' THEN sm.Quantity ELSE 0 END), 0) AS batchAvailableQty
    FROM producttable p
    JOIN batch b ON p.ProductID = b.ProductID
    JOIN stocktable s ON p.ProductID = s.ProductID
    LEFT JOIN stockmovement sm ON sm.BatchID = b.BatchID
    WHERE b.ExpiryDate > CURDATE()
    GROUP BY b.BatchID
    HAVING batchAvailableQty > 0
    ORDER BY p.ProductID, b.ExpiryDate ASC, b.DateOfTransaction ASC
";

// Execute the query
$productResult = $conn->query($productQuery);
if (!$productResult) die("Product Query Error: " . $conn->error);

$productData = [];
while ($row = $productResult->fetch_assoc()) {
    $productID = $row['ProductID'];
    if (!isset($productData[$productID])) {
        $productData[$productID] = [
            'ProductID' => $row['ProductID'],
            'ProductName' => $row['ProductName'],
            'batches' => []
        ];
    }
    $productData[$productID]['batches'][] = $row;
}

// Fetch Collection Products (to display as a separate section)
$collectionQuery = "
    SELECT c.CollectionID, c.CollectionName, c.Price, c.Quantity 
    FROM collectiontable c
    WHERE c.Quantity > 0
";
$collectionResult = $conn->query($collectionQuery);
if (!$collectionResult) die("Collection Query Error: " . $conn->error);

$collectionData = [];
while ($row = $collectionResult->fetch_assoc()) {
    $collectionData[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Billing Form</title>
     <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap"
      rel="stylesheet"
    />
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        input[readonly] { background-color: #f2f2f2; }

       




/* General Body Styling */
body {
        font-family: "Playfair Display", serif;/* font-family: 'Arial', sans-serif;*/
    background: linear-gradient(to right, #DAE7DD, #F7C9B6);
    color: #333;
    padding: 40px;
    margin: 0;
    overflow-x: hidden;
}

/* Heading Styling */
h2 {
    text-align: center;
    font-size: 40px;
    color: #333;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
     font-family: "Playfair Display", serif;
}

/* Input and Select Styling */
select, input[type="number"], input[type="text"] {
    padding: 10px 15px;
    border-radius: 8px;
    border: 2px solid #F7C9B6;
    background-color: #ffffff;
    color: #333;
    font-size: 16px;
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
   
}

/* Focus Input Styling */
select:focus, input:focus {
    border-color: #DBC39A;
    background-color: #DAE7DD;
    outline: none;
}

/* Button Styling */
button {
    background-color: #DACAC4;
    color: #333;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    font-family: "Playfair Display", serif;
}

button:hover {
    background-color: #A79C90;
    transform: scale(1.05);
}

/* Product and Collection Section Styling */
#productSection, #collectionSection {
    margin-top: 30px;
    border: 3px solid #fff;
    padding: 20px;
    border-radius: 12px;
    background-color: rgba(255, 255, 255, 0.9);
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    max-width: 90%;
    margin-left: auto;
    margin-right: auto;
    overflow: hidden;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th {
    background-color: #F7C9B6;
    color: #333;
    padding: 15px;
    font-size: 18px;
}

td {
    padding: 15px;
    text-align: center;
    background-color: #DAE7DD;
    font-size: 16px;
}

input[readonly] {
    background-color: #f7f7f7;
    border: 2px solid #c8c8c8;
    
}

/* Billing Section */
#billingSection {
    margin-top: 30px;
    padding: 20px;
    border-radius: 12px;
    background-color: #fff;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    max-width: 80%;
    margin-left: auto;
    margin-right: auto;
    border: 2px solid #DBC39A;
}

/* Total Amount Styling */
#totals {
    background-color: #DBC39A;
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
    border: 2px solid #DBC39A;
}

/* Input Fields in Totals */
#totals input {
    background-color: #DAE7DD;
    font-weight: bold;
    border-color: #DACAC4;
    width: 40%;  /* Reduced the width to make it more compact */
    margin: 10px 0;
}

/* Final Amount Styling */
input.finalAmount {
    font-weight: bold;
    color: #388e3c;
    font-size: 18px; /* Reduced font size to match smaller input */
    width: 80%;  /* Reduced the width of final amount */
}

/* Total and Payment Method Section */
.total-section, .payment-method-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.total-section input,
.payment-method-section select {
    width: 45%;  /* Reduced the width to make fields more compact */
}

/* Responsive Styling */
@media (max-width: 768px) {
    .product-info .info-item {
        width: 100%; /* Stack product info on smaller screens */
    }

    #billingSection {
        max-width: 95%; /* Ensure billing section is responsive on small screens */
    }

    #totals input {
        width: 100%; /* Ensure total inputs are fully responsive */
    }

    .total-section input,
    .payment-method-section select {
        width: 5%; /* Make inputs full width on smaller screens */
    }
}
/* Specific styling for Bill Mode, Total Amount, Final Amount, and Payment Method */

/* Bill Mode Section */
#billingSection .total-section input,
#billingSection .payment-method-section select {
    width: 25%;  /* Set width to 25% to make them smaller */
    font-size: 14px;  /* Reduced font size for smaller inputs */
    padding: 8px 12px; /* Ensure some padding for readability */
    border-radius: 8px;
    border: 2px solid #F7C9B6;
    background-color: #ffffff;
    transition: all 0.3s ease;
}

//* Ensuring Total Price has the same width as other input fields */
#totals input, .finalAmount {
    width: 100%;  /* Ensures it takes the full width */
    font-weight: bold;
    background-color: #DAE7DD;  /* Match the background color */
    border-color: #DACAC4;       /* Border color */
    color: #388e3c;              /* Green color for the text */
    font-size: 16px;             /* Match the font size */
    padding: 10px 15px;          /* Match padding for consistency */
    border-radius: 8px;
}

/* Final Amount Input Field */
input.finalAmount {
    font-size: 16px;             /* Adjust font size to match other fields */
    color: #388e3c;              /* Green color */
    background-color: #DAE7DD;   /* Matching background */
    border: 2px solid #F7C9B6;   /* Consistent border style */
    font-weight: bold;           /* Bold text */
    width: 100%;                 /* Full width */
    padding: 10px 15px;          /* Same padding */
}

/* Payment Method Field */
#billingSection .payment-method-section select {
    width: 25%;  /* Smaller width for the Payment Method dropdown */
    font-size: 14px;  /* Reduced font size */
    padding: 8px 12px; /* Reduced padding */
    border-radius: 8px;
    border: 2px solid #F7C9B6;
    background-color: #ffffff;
}

/* Ensuring responsiveness */
@media (max-width: 768px) {
    #billingSection .total-section input,
    #billingSection .payment-method-section select,
    #billingSection #totalAmount,
    input.finalAmount {
        width: 100%;  /* Full width on smaller screens for better usability */
    }
}
    </style>


<script>
    // Ensure the products and collections data is correctly passed from PHP to JavaScript
    let products = <?php echo json_encode($productData); ?>;
    let collections = <?php echo json_encode($collectionData); ?>;

    console.log('Products:', products); // Check if products data is passed correctly
    console.log('Collections:', collections); // Check if collections data is passed correctly

    // Handle display of sections based on selected billing mode
    function onModeChange(select) {
        let mode = select.value;
        document.getElementById("productSection").style.display = (mode === 'individual' || mode === 'both') ? 'block' : 'none';
        document.getElementById("collectionSection").style.display = (mode === 'collection' || mode === 'both') ? 'block' : 'none';
    }

    // Add a new row for individual products
    function addProductRow() {
        let table = document.getElementById("productTable");
        let row = table.insertRow(-1);
        row.innerHTML = `
            <td>
                <select name="product_ids[]" onchange="fillBatches(this)">
                    <option value="">-- Select --</option>
                    ${Object.entries(products).map(([id, p]) => `<option value="${id}">${p.ProductName}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" name="quantities[]" min="1" required oninput="calculateRow(this)"></td>
            <td><input type="text" name="unit_prices[]" readonly oninput="calculateRow(this)"></td>
            <td><input type="text" name="discounts[]" readonly oninput="calculateRow(this)"></td>
            <td><input type="text" name="final_amounts[]" class="finalAmount" readonly></td>
            <td><button type="button" onclick="removeRow(this)">Remove</button></td>
        `;
    }

    // Fill batches and their corresponding data when a product is selected
    function fillBatches(select) {
        const productID = select.value;
        if (!products[productID]) return;

        const batches = products[productID].batches;
        const table = document.getElementById("productTable");

        batches.forEach(batch => {
            const today = new Date().toISOString().split('T')[0];  // Get today's date
            const discountApplicable = batch.DiscountStartDate <= today && today <= batch.DiscountEndDate;  // Check if the discount is applicable
            const discount = discountApplicable ? (parseFloat(batch.SellingRate) * parseFloat(batch.DiscountRate) / 100) : 0;

            let row = table.insertRow(-1);
            row.innerHTML = `
                <td>
                    <input type="hidden" name="product_ids[]" value="${batch.ProductID}">
                    <input type="hidden" name="batch_ids[]" value="${batch.BatchID}">
                    ${batch.ProductName} (Batch ID ${batch.BatchID})
                </td>
                <td><input type="number" name="quantities[]" max="${batch.batchAvailableQty}" min="1" required oninput="calculateRow(this)"></td>
                <td><input type="text" name="unit_prices[]" value="${batch.SellingRate}" readonly oninput="calculateRow(this)"></td>
                <td><input type="text" name="discounts[]" value="${discount.toFixed(2)}" readonly oninput="calculateRow(this)"></td>
                <td><input type="text" name="final_amounts[]" class="finalAmount" readonly></td>
                <td><button type="button" onclick="removeRow(this)">Remove</button></td>
            `;
        });

        select.closest("tr").remove(); // Remove the original product select dropdown
    }
// Add a new row for product collections 

    function addCollectionRow() {
        let table = document.getElementById("collectionTable");
        let row = table.insertRow(-1);
        row.innerHTML = `
            <td>
                <select name="collection_ids[]" onchange="fillCollectionDetails(this)">
                    <option value="">-- Select --</option>
                    ${collections.map(c => `<option value="${c.CollectionID}" data-price="${c.Price}" data-qty="${c.Quantity}">${c.CollectionName}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" name="collection_quantities[]" min="1" required oninput="calculateCollectionRow(this)"></td>
            <td><input type="text" name="collection_prices[]" readonly oninput="calculateCollectionRow(this)"></td>
            <td><input type="text" name="collection_final_amounts[]" readonly class="finalAmount"></td>
            <td><button type="button" onclick="removeRow(this)">Remove</button></td>
        `;
    }  

 // Fill the price and quantity for the selected collection
    function fillCollectionDetails(select) {
        let price = select.options[select.selectedIndex].dataset.price;
        let row = select.closest("tr");
        row.cells[2].querySelector("input").value = price;
        calculateCollectionRow(row.cells[1].querySelector("input"));
    }  


    // Calculate final amount for individual product rows
    function calculateRow(inputElement) {
        let row = inputElement.closest('tr');
        let quantity = parseFloat(row.querySelector('input[name="quantities[]"]').value);
        let unitPrice = parseFloat(row.querySelector('input[name="unit_prices[]"]').value);
        let discount = parseFloat(row.querySelector('input[name="discounts[]"]').value); // Already in percentage form

        if (quantity && unitPrice) {
            let discountAmount = discount; // Apply discount directly
            let finalAmount = (unitPrice - discountAmount) * quantity;
            row.querySelector('input[name="final_amounts[]"]').value = finalAmount.toFixed(2);
        }
        calculateTotal(); // Recalculate total after any change in row
    }
// Calculate final amount for collection rows
    function calculateCollectionRow(input) {
        let row = input.closest("tr");
        let qty = parseFloat(row.cells[1].querySelector('input').value) || 0;
        let price = parseFloat(row.cells[2].querySelector('input').value) || 0;
        row.cells[3].querySelector('input').value = (qty * price).toFixed(2);
        calculateTotal(); // Recalculate the total after each update
    }

     // Remove a row from either individual products or collections table
    function removeRow(button) {
        button.closest("tr").remove();
        calculateTotal(); // Recalculate the total after row removal
    }
 // Calculate for collections
        document.querySelectorAll("input[name='collection_final_amounts[]']").forEach(input => {
            const collectionAmount = parseFloat(input.value) || 0;
            totalAmount += collectionAmount;
        });


    // Calculate the total, overall discount, and final amount
    function calculateTotal() {
        let totalAmount = 0; // total before discount
        let totalDiscount = 0;
        let finalAmount = 0;

        // Calculate total amount (before discount) and total discount for products
        const rows = document.querySelectorAll("#productTable tr");
        rows.forEach(row => {
            const qtyInput = row.querySelector('input[name="quantities[]"]');
            const priceInput = row.querySelector('input[name="unit_prices[]"]');
            const discountInput = row.querySelector('input[name="discounts[]"]');

            if (qtyInput && priceInput) {
                let quantity = parseFloat(qtyInput.value) || 0;
                let unitPrice = parseFloat(priceInput.value) || 0;
                let discount = parseFloat(discountInput?.value) || 0;

                totalAmount += quantity * unitPrice;
                totalDiscount += quantity * discount;
            }
        });

        // Add collections (assuming no discount on collections)
        document.querySelectorAll("input[name='collection_final_amounts[]']").forEach(input => {
            const collectionAmount = parseFloat(input.value) || 0;
            totalAmount += collectionAmount;
        });

        // Calculate final amount using formula: FA = TA - OD
        finalAmount = totalAmount - totalDiscount;

        // Update hidden inputs
        document.getElementById("totalAmount").value = totalAmount.toFixed(2);
        document.getElementById("overallDiscount").value = totalDiscount.toFixed(2);
        document.getElementById("finalAmount").value = finalAmount.toFixed(2);

        // Update displayed inputs
        document.getElementById("totalAmountDisplay").value = totalAmount.toFixed(2);
        document.getElementById("overallDiscountDisplay").value = totalDiscount.toFixed(2);
        document.getElementById("finalAmountDisplay").value = finalAmount.toFixed(2);
    }

    // Ensure total calculation before form submission
    function submitForm() {
        calculateTotal(); // Calculate totals before form submission
    }

    // Get all collection data before submitting the form
    function getCollectionData() {
        const collectionIds = [];
        const collectionQuantities = [];
        const collectionPrices = [];
        const collectionFinalAmounts = [];

        document.querySelectorAll("select[name='collection_ids[]']").forEach((select, index) => {
            collectionIds.push(select.value);
            collectionQuantities.push(document.querySelectorAll("input[name='collection_quantities[]']")[index].value);
            collectionPrices.push(document.querySelectorAll("input[name='collection_prices[]']")[index].value);
            collectionFinalAmounts.push(document.querySelectorAll("input[name='collection_final_amounts[]']")[index].value);
        });

        // Log collection data for debugging
        console.log("Collection IDs:", collectionIds);
        console.log("Collection Quantities:", collectionQuantities);
        console.log("Collection Prices:", collectionPrices);
        console.log("Collection Final Amounts:", collectionFinalAmounts);
    }

    // Example of how to call the function before submitting the form
   function submitForm() {
    calculateTotal(); // Always calculate before submission

    // Get selected product IDs
    const selectedProductIds = Array.from(document.querySelectorAll("input[name='product_ids[]']")).map(input => input.value);
    
    // Get selected collection IDs
    const selectedCollectionIds = Array.from(document.querySelectorAll("select[name='collection_ids[]']")).map(select => select.value);

    // Check if at least one is selected
    const hasProduct = selectedProductIds.some(id => id.trim() !== "");
    const hasCollection = selectedCollectionIds.some(id => id.trim() !== "");

    if (!hasProduct && !hasCollection) {
        alert("Please select at least one product or collection.");
        return false; // prevent form submission
    }

    // Optional: Debug logs
    console.log("Selected Products:", selectedProductIds);
    console.log("Selected Collections:", selectedCollectionIds);

    // Now submit the form
    document.getElementById("myForm").submit();
}


    window.onload = function () {
        const modeSelector = document.querySelector("select[onchange='onModeChange(this)']");
        onModeChange(modeSelector); // Hide sections initially
    };
</script>




</head>
<body>

<h2>Billing Form</h2>

<!-- Mode Selection: Product Only, Collection Only, or Both -->
<label>Choose Billing Mode:</label>
<select onchange="onModeChange(this)">
    <option value=""></option>
    <option value="both">Both</option>
    <option value="individual">Individual Products</option>
    <option value="collection">Collections</option>
</select>

<form action="billingcollection.php" method="post" onsubmit="submitForm()">
    <!-- Individual Products Section -->
    <div id="productSection" style="display:none">
    
        <h3>Individual Products</h3>
        <table id="productTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button type="button" onclick="addProductRow()">Add Product</button>
    </div><br>

   <!-- Collections Section --> 
<div id="collectionSection" style="display:none">
    <h3>Collections</h3>
    <table id="collectionTable">
        <thead>
            <tr>
                <th>Collection</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Final Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <button type="button" onclick="addCollectionRow()">Add Collection</button>
</div><br>

    <!-- Hidden Fields for Total and Final Amount Calculation -->
    <input type="hidden" id="totalAmount" name="totalAmount">
    <input type="hidden" id="overallDiscount" name="overallDiscount">
    <input type="hidden" id="finalAmount" name="finalAmount">

    <!-- Display Total Amount -->
    <div>
        <label>Total Amount: </label>
        <input type="text" id="totalAmountDisplay" readonly>
    </div><br>
    <div>
        <label>Overall Discount: </label>
        <input type="text" id="overallDiscountDisplay" readonly>
    </div><br>
    <div>
        <label>Final Amount: </label>
        <input type="text" id="finalAmountDisplay" readonly>
    </div><br>

    <label>Payment Method: </label>
    <select name="paymentMethod" required>
        <option value="">-- Select --</option>
        <option value="Cash">Cash</option>
        <option value="Card">Card</option>
        <option value="UPI">UPI</option>
    </select><br><br>

    <button type="submit">Generate Bill</button>
</form>

</body>
</html>
