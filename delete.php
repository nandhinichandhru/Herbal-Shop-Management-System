<?php
require 'db.php';

if (isset($_GET['table'], $_GET['key'], $_GET['pkField'])) {
    $table = $_GET['table'];
    $key = $_GET['key'];
    $pkField = $_GET['pkField'];

    $allowedTables = ["categorytable", "producttable","vendortable","batch","collectiontable","collectionproducttable","billtable","stocktable","stockmovement","salestable"];
    if (!in_array($table, $allowedTables)) {
        die("Invalid table.");
    }

    $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$pkField` = ?");
    $stmt->bind_param("s", $key);

    echo $stmt->execute() ? "Row deleted successfully." : "Failed to delete row.";
}
?>
