<?php
require 'db.php';

if (isset($_GET['table'])) {
    $table = $_GET['table'];
    $allowedTables = ["categorytable", "producttable", "batch",
    "collectiontable","collectionproducttable","vendortable","billtable","stocktable","stockmovement","salestable"];

    if (!in_array($table, $allowedTables)) {
        echo "Invalid table selected.";
        exit;
    }

    $result = $conn->query("SELECT * FROM `$table`");

    if ($result->num_rows > 0) {
        echo "<h3>Table: " . strtoupper($table) . "</h3>";
        echo "<table><tr>";
        $fields = $result->fetch_fields();

        $fieldNames = array_map(fn($f) => $f->name, $fields);
        $primaryKey = $fieldNames[0]; // Use the first column as the key

        foreach ($fieldNames as $field) {
            echo "<th>" . strtoupper($field) . "</th>";
        }
        echo "<th>ACTION</th></tr>";

        while ($row = $result->fetch_assoc()) {
            $rowKey = htmlspecialchars($row[$primaryKey]);
            echo "<tr id='row-$rowKey'>";
            foreach ($fieldNames as $key) {
                echo "<td data-editable data-field='$key'>" . htmlspecialchars($row[$key]) . "</td>";
            }

            echo "<td>
                <button class='edit-btn' onclick='enableEdit(\"$rowKey\")'>Edit</button>
                <button class='save-btn' onclick='saveEdit(\"$rowKey\", \"$table\", \"$primaryKey\")' style='display:none;'>Save</button>
                <button class='cancel-btn' onclick='cancelEdit(\"$rowKey\")' style='display:none;'>Cancel</button>
                <button class='delete-btn' onclick='deleteRow(\"$rowKey\", \"$table\", \"$primaryKey\")'>Delete</button>
            </td></tr>";
        }

        echo "</table>";
    } else {
        echo "No data found.";
    }
}
?>