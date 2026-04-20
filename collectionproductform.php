<?php
require 'db.php'; // Your DB connection

// Fetch products from producttable
$product_result = $conn->query("SELECT ProductID, ProductName FROM producttable");

// Store products in a PHP array
$products = [];
while ($row = $product_result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Collection with Multiple Products</title>
     <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap"
      rel="stylesheet"
    />
    <style>
   /* General Body Styling */
body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(to right, #37474F, #B2DFDB); /* Elegant gradient */
    color: #333;
    padding: 40px;
    margin: 0;
}

/* Heading Styling */
h2 {
    text-align: center;
    font-size: 30px;
    color: #00695C; /* Deep teal text color */
    font-weight: 600;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

/* Form Container Styling */
.form-container {
    max-width: 600px;
    margin: auto;
    padding: 30px;
    border-radius: 10px;
    background-color: #FFFFFF;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #90A4AE; /* Soft gray border */
    font-family: "Playfair Display", serif;
}

/* Input and Select Styling */
select, input[type="number"], input[type="text"], input[type="email"], textarea {
    padding: 12px 15px;
    border-radius: 6px;
    border: 1px solid #90A4AE; /* Soft gray border */
    background-color: #ECEFF1; /* Light gray */
    color: #333;
    font-size: 16px;
    width: 100%;
    box-sizing: border-box;
}

/* Remove focus color change */
select:focus, input:focus {
    border-color: #90A4AE; /* Keep original border color */
    background-color: #ECEFF1; /* Keep original background color */
    outline: none; /* Remove focus outline */
}

/* General button styling for both <input> and <button> */
button, input[type="submit"] {
    background-color: #00897B; /* Dark teal button */
    color: #fff;
    border: none;
    padding: 14px 22px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
    margin-top: 20px;
    font-weight: 600;
    text-align: center;
    font-family: "Playfair Display", serif;
}

/* Hover effect for both buttons */
button:hover, input[type="submit"]:hover {
    background-color: #00796B; /* Lighter teal on hover */
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
    margin-bottom: 8px;
    font-weight: 600;
    color: #00695C;
}

/* Textarea Styling */
textarea {
    resize: vertical;
    height: 100px;
    background-color: #ECEFF1;
}

.product-row {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px; /* Space between select and remove button */
}

.remove-btn {
    padding: 5px 10px;
    color: #B22222;
    background-color: #f8d7da;
    border: 1px solid #f5c2c7;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.remove-btn:hover {
    background-color: #f1b0b7;
    color: #7f1d1d;
}

.add-btn {
    margin-top: 10px;
    background-color: #90A4AE; /* Soft gray border */
    color: white;
    padding: 8px;
    cursor: pointer;
}

.add-btn:hover {
    background-color: #45a049;
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
        <h2>Add New Collection</h2>
        <form method="POST" action="collectionprocess.php">
            <label>Collection Name:</label>
            <input type="text" name="collection_name" required><br><br>

            <label>Price:</label>
            <input type="number" name="price" step="0.01" required><br><br>

            <label>Quantity:</label>
            <input type="number" name="quantity" required><br><br>

            <div id="productContainer">
                <label>Products:</label><br>
                <div class="product-row">
                    <select name="product_ids[]" required>
                        <option value="">-- Select Product --</option>
                        <?php foreach ($products as $product) { ?>
                            <option value="<?= $product['ProductID'] ?>"><?= $product['ProductName'] ?></option>
                        <?php } ?>
                    </select>
                    <span class="remove-btn" onclick="removeRow(this)">Remove</span>

                </div>
            </div>

            <button type="button" class="add-btn" onclick="addProductRow()">+ Add Another Product</button><br><br>

            <input type="submit" value="Add Collection">
        </form>
    </div>

 <script>
    const products = <?php echo json_encode($products); ?>;

    function addProductRow() {
        const quantityInput = document.querySelector('input[name="quantity"]');
        const maxQuantity = parseInt(quantityInput.value);
        const currentRows = document.querySelectorAll('.product-row').length;

        if (!maxQuantity || maxQuantity <= 0) {
            alert("Please enter a valid quantity first.");
            return;
        }

        if (currentRows >= maxQuantity) {
            alert("You can only add up to " + maxQuantity + " products.");
            return;
        }

        const container = document.getElementById("productContainer");
        const row = document.createElement("div");
        row.classList.add("product-row");

        let selectHTML = `<select name="product_ids[]" required><option value="">-- Select Product --</option>`;
        products.forEach(p => {
            selectHTML += `<option value="${p.ProductID}">${p.ProductName}</option>`;
        });
        selectHTML += `</select>`;

        row.innerHTML = selectHTML + `<span class="remove-btn" onclick="removeRow(this)">[Remove]</span>`;
        container.appendChild(row);
    }

    function removeRow(el) {
        el.parentElement.remove();
    }

    // Validate on submit
    document.querySelector("form").addEventListener("submit", function (e) {
        const quantity = parseInt(document.querySelector('input[name="quantity"]').value);
        const rows = document.querySelectorAll('.product-row').length;

        if (rows !== quantity) {
            alert("You entered quantity as " + quantity + ", but added " + rows + " products.\nPlease match the quantity.");
            e.preventDefault(); // Stop form submission
        }
    });
      // Prevent duplicate product selection
        document.querySelector("form").addEventListener("submit", function(e) {
            const selected = [...document.querySelectorAll("select[name='product_ids[]']")].map(s => s.value);
            const hasDuplicates = new Set(selected).size !== selected.length;

            if (hasDuplicates) {
                e.preventDefault();
                alert("Duplicate products selected! Please select different products.");
            }
        });
</script>

</body>
</html>
