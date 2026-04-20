<?php
require 'db.php';
$tables = [


    "categorytable" => "categoryfinal.html",
    "producttable" => "product_save.php",
    "vendortable" => "vendorfinal.html",
    "batch" => "batchform.php",
    "stocktable" => "", // No form linked yet
    "stockmovement" => "", // No form linked yet
    "collectiontable" => "collectionproductform.php",
    "collectionproducttable" => "",
    "billtable" => "billcollection.php",
    "salestable" => ""
     

  ];

$menuNames = [
    "categorytable" => "Category",
    "producttable" => "Products",
    "vendortable" => "Vendors",
    "batch" => "Batch",
    "stocktable" => "Stock",
    "stockmovement" => "Stock Movement",
    "collectiontable" => "Collections",
    "collectionproducttable" => "Collection Products",
    "billtable" => "Billing",
    "salestable" => "Sales",

  ];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Combined Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet"  >
    <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8fafa;
      color: #333;

      background-color: #f9f1f0; /* Light background color for the body */
      margin: 0;
      padding: 0;
    }

    .top-menu {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color: #F7E6DF; /* warm, soft rose-tan */
      padding: 12px 30px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 16px;
      z-index: 1000;
      border-bottom: 2px solid #E5CACE;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      white-space: nowrap;
      overflow: hidden;
      font-size: 1.1rem;
    }

    .top-menu a {
      color: #68343B;
      text-decoration: none;
      padding: 4px 12px;
      font-size: 17px;
      font-weight: 500;
      border-radius: 6px;
      background-color:#F7E6DF;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .top-menu a:hover,
    .top-menu a.active {
      background-color: #C68D95;
      color: #fff;
    }

    .form-container {
      background-image: url("images/vecteezy_charming-display-of-dried-flowers-and-herbs-in-glass-jars-on_55302710.jpeg");
      background-size: cover;               /* Ensure the image covers the area */
      background-position: center center;   /* Center the image */
      background-attachment: fixed;         /* Fix the image in place when scrolling */
      background-repeat: no-repeat;
      padding: 30px;
      background-color: #ffffff;
      margin: 30px auto 20px; /* Reduced bottom margin from 30px to 20px */
      border-radius: 14px;
      max-width: 1200px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
      width: 100vw;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      height: 100vh;
      font-size: 30px;
      font-weight: bold;
      color: #D40032;

    }


    .form-container h3 {
    font-family: 'Playfair Display', serif; color:#D40032;
      font-size: 40px;
      font-weight: 600;
      color: #90F3E8;
      text-align: center;
      letter-spacing: 0.5px;
      margin-top: 20px;
    }

    /* Optional: Adding overlay for better text visibility */
    .form-container::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.6); /* White overlay with some transparency */
      z-index: -1; /* Keep the overlay behind the content */
    }

    .table-container {
      padding: 30px;
      background-color: #ffffff;
      margin: 30px auto;
      border-radius: 14px;
      max-width: 900px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }

    iframe {
      width: 100%;
      height: 600px;
      border: none;
    }

    .dashboard-container {
      display: none;
      margin: 30px auto 20px; /* Reduced bottom margin for consistency */
      max-width: 1000px;
      background: #F9F1F0; /* Light background */
      border-radius: 14px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
      padding: 24px 30px; /* Slight vertical padding reduction for balance */
      overflow-x: auto;
    }


    #dashboard-container table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 8px; /* space between rows */
      margin-top: 20px;
      font-size: 15px;
      background-color: #F9F1F0;
      border-radius: 12px;
      table-layout: auto; /* allow columns to adjust */
      padding: 10px;
      overflow: hidden;
      box-shadow: 0 6px 10px rgba(0,0,0,0.05);



      
    }

    #dashboard-container th,
    #dashboard-container td {
      padding: 14px 12px;
      text-align: left;
      white-space: nowrap;
    }

    #dashboard-container th {
      background-color: #F79489;
      color: #fff;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 13px;
      border: none;

    }

    #dashboard-container tbody tr {
      background-color: #FADCD9;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);


      
    }

    #dashboard-container tbody tr:hover {
      background-color: #F8AFA6;
      transition: 0.3s;




      
    }

    #dashboard-container td {
      border-top: 1px solid #f0e0de;
      border-bottom: 1px solid #f0e0de;
      background-color: #ffffff;
      color: #333;


      
    }

    #dashboard-container td:last-child {
      white-space: nowrap;
    }

    /* Button styling (unchanged, but matching colors) */
    #dashboard-container button {
      padding: 6px 12px;
      margin: 2px;
      font-size: 13px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s ease;
      
    }

    #dashboard-container .edit-btn {
      background-color: #A5525F;
      color: white;
    }

    #dashboard-container .edit-btn:hover {
      background-color: #843b4c;
    }

    #dashboard-container .save-btn {
      background-color: #06d6a0;
      color: white;
    }

    #dashboard-container .save-btn:hover {
      background-color: #05b387;
    }

    #dashboard-container .cancel-btn {
      background-color: #adb5bd;
      color: white;
    }

    #dashboard-container .cancel-btn:hover {
      background-color: #6c757d;
    }

    #dashboard-container .delete-btn {
      background-color: #ef476f;
      color: white;
    }

    #dashboard-container .delete-btn:hover {
      background-color: #d8335e;
    }

    /* View Dashboard & Back Buttons */
    .view-dashboard-btn,
    .go-back-btn {
      display: inline-block;
      margin-top: 16px;                 /* Slightly reduced top margin for balance */
      padding: 10px 20px;               /* More compact padding to match form style */
      font-size: 15px;                  /* Harmonized with form's clean, bold look */
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
      font-weight: bold;
    }

    /* View Dashboard Button Style */
    /* View Dashboard Button Style */
    .view-dashboard-btn {
      margin-top: 10px;
      padding: 8px 18px;
      font-size: 14px;
      border-radius: 6px;
      background-color: #A5525F;
      color: white;
      text-align: center;
      max-width: 180px;
      margin-left: auto;
      margin-right: auto;

      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 100;
      pointer-events: auto;

      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .view-dashboard-btn:hover {
      transform: translateX(-50%) translateY(-4px); /* Moves it up slightly on hover */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);     /* Optional: adds a subtle shadow effect */
    }


    .go-back-btn {
      background-color: #6c5ce7; /* Elegant purple */
      color: white;
      margin-left: 10px;
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 14px;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .go-back-btn:hover {
      background-color: #5a4ccf; /* Slightly darker shade for hover */
      transform: scale(1.05); /* Gentle zoom effect */
    }


    </style>


  <script>
    
let currentTable = "";

function loadFormAndTable(tableName, formUrl) {
  currentTable = tableName;

  // Load Form
  const formContainer = document.getElementById("form-container");
  const dashboardContainer = document.getElementById("dashboard-container");
  const viewDashboardBtn = document.getElementById("view-dashboard-btn");

  // Reset dashboard visibility
  dashboardContainer.style.display = "none";

  // Show the form if available
  if (formUrl) {
    formContainer.innerHTML = 
      `<iframe src="${formUrl}" onload="showViewDashboardButton()"></iframe>
      <button id="view-dashboard-btn" class="view-dashboard-btn" onclick="viewDashboard()">View Dashboard</button>`;
  } else {
    formContainer.innerHTML = 
      `<h3>You can view this table, but it doesn't require any input.</h3>
      <button id="view-dashboard-btn" class="view-dashboard-btn" onclick="viewDashboard()">View Dashboard</button>`;
  }

  // Ensure form-container is visible when you're not in dashboard view
  formContainer.style.display = "block";

  // Active menu style
  document.querySelectorAll('.top-menu a').forEach(el => el.classList.remove('active'));
  document.getElementById('link-' + tableName).classList.add('active');
}

function showViewDashboardButton() {
  const viewDashboardBtn = document.getElementById("view-dashboard-btn");
  viewDashboardBtn.style.display = "block"; // Show the button after form submission
}

function viewDashboard() {
  const dashboardContainer = document.getElementById("dashboard-container");
  const formContainer = document.getElementById("form-container");
  const viewDashboardBtn = document.getElementById("view-dashboard-btn");
  const goBackBtn = document.getElementById("go-back-btn");

  // Hide the form and button after dashboard is shown
  formContainer.style.display = "none";
  viewDashboardBtn.style.display = "block";
  
  // Show the dashboard and Go Back button
  dashboardContainer.style.display = "block";
  goBackBtn.style.display = "block"; // Show Go Back button when dashboard is visible
    
      // Load the table content
      loadTable(currentTable);
    }

  function goBackToForm() {
  const formContainer = document.getElementById("form-container");
  const dashboardContainer = document.getElementById("dashboard-container");

  // Hide the dashboard and show the form container again
  dashboardContainer.style.display = "none";
  formContainer.style.display = "block";
}

    function loadTable(tableName) {
      const xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
          document.getElementById("dashboard-content").innerHTML = this.responseText;
          document.getElementById("go-back-btn").style.display = "block"; // show button after load
        }
      };
      xhttp.open("GET", "fetch_table.php?table=" + tableName, true);
      xhttp.send();
    }


      function enableEdit(rowId) {
      const row = document.getElementById("row-" + rowId);
      const cells = row.querySelectorAll("td[data-editable]");
      cells.forEach(cell => {
        const val = cell.innerText;
        cell.innerHTML = `<input type='text' value='${val}' />`;
      });
      row.querySelector(".edit-btn").style.display = "none";
      row.querySelector(".save-btn").style.display = "inline-block";
      row.querySelector(".cancel-btn").style.display = "inline-block";
    }

    function cancelEdit(rowId) {
      loadTable(currentTable); // reload the table
    }

    function saveEdit(rowId, tableName, pkField) {
      const row = document.getElementById("row-" + rowId);
      const inputs = row.querySelectorAll("td[data-editable] input");
      const updatedData = {};
      inputs.forEach((input) => {
        const fieldName = input.parentElement.getAttribute('data-field');
        updatedData[fieldName] = input.value;
      });

      const formData = new FormData();
      formData.append("key", rowId);
      formData.append("pkField", pkField);
      formData.append("table", tableName);

      for (const key in updatedData) {
        formData.append(key, updatedData[key]);
      }

      fetch("update_row.php", {
          method: "POST",
          body: formData
      })
      .then(res => res.text())
      .then(res => {
          alert(res);
          loadTable(tableName);
      });
    }

    function deleteRow(rowId, tableName, pkField) {
      if (!confirm("Are you sure you want to delete this row?")) return;

      fetch(`delete.php?table=${tableName}&key=${rowId}&pkField=${pkField}`)
        .then(res => res.text())
        .then(res => {
          alert(res);
          loadTable(tableName);
        });
    }
    

  </script>
</head>

<body>

<div class="top-menu">
    <?php foreach ($tables as $tableName => $formUrl): ?>
        <a href="#" id="link-<?php echo $tableName; ?>"
           onclick="loadFormAndTable('<?php echo $tableName; ?>', '<?php echo $formUrl; ?>')">
            <?php 
            // Check if a custom name exists for the table, if not, use the table name itself in uppercase
            echo isset($menuNames[$tableName]) ? $menuNames[$tableName] : strtoupper($tableName);
            ?>
        </a>
    <?php endforeach; ?>
     <!-- Logout Link -->
    <a href="logout.php" >Logout</a>
</div>


<div class="form-container" id="form-container">
  <h3>Select a table from the menu to add new entries.</h3>
  <div class="view-dashboard-btn" id="view-dashboard-btn" style="display: none;" onclick="viewDashboard()">View Dashboard</div>
</div>

<!-- <div class="view-dashboard-btn" id="view-dashboard-btn" style="display: none;" onclick="viewDashboard()">View Dashboard</div> -->

<div class="dashboard-container" id="dashboard-container" style="display: none;">
  <div id="dashboard-content"></div> <!-- content from AJAX -->
  <button class="go-back-btn" id="go-back-btn" style="display: none;" onclick="goBackToForm()">Go Back to Form</button>
</div>


</body>
</html> 









