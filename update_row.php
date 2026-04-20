<?php
require 'db.php';

if (isset($_POST['table'], $_POST['key'], $_POST['pkField'])) {
    $table = $_POST['table'];
    $key = $_POST['key'];
    $pkField = $_POST['pkField'];

    $allowedTables = ["categorytable", "producttable", "collectiontable" ,"vendortable","billtable","batch","collectionproducttable","stocktable","stockmovement","salestable"];
    if (!in_array($table, $allowedTables)) {
        echo "Invalid table.";
        exit;
    }

    $columns = array_filter($_POST, fn($k) => !in_array($k, ['table', 'key', 'pkField']), ARRAY_FILTER_USE_KEY);
    $sets = [];
    $params = [];
    $types = '';

    foreach ($columns as $col => $val) {
        $sets[] = "`$col` = ?";
        $params[] = $val;
        $types .= 's';
    }

    $query = "UPDATE `$table` SET " . implode(", ", $sets) . " WHERE `$pkField` = ?";
    $params[] = $key;
    $types .= 's';

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    echo $stmt->execute() ? "Row updated successfully." : "Update failed.";
}
?>
