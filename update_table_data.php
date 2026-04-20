<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["table"], $_POST["id"], $_POST["updatedData"])) {
    $table = $_POST["table"];
    $id = $_POST["id"];
    $updatedData = $_POST["updatedData"];

    $updateSQL = "UPDATE `$table` SET ";
    $updateParts = [];

    foreach ($updatedData as $column => $value) {
        $updateParts[] = "`$column` = '" . $conn->real_escape_string($value) . "'";
    }

    $updateSQL .= implode(", ", $updateParts);
    $updateSQL .= " WHERE id = '$id'";

    if ($conn->query($updateSQL)) {
        echo "Record updated successfully!";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
}
?>
