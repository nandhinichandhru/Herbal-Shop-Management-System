<?php
require 'db.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST["table"];

    // Find the primary key dynamically
    $primaryKeyQuery = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
    $primaryKeyResult = $conn->query($primaryKeyQuery);

    if (!$primaryKeyResult || $primaryKeyResult->num_rows == 0) {
        die("Error: Unable to find primary key for table $table.");
    }

    $primaryKeyRow = $primaryKeyResult->fetch_assoc();
    $primaryKey = $primaryKeyRow['Column_name']; // Extract primary key column name

    // **UPDATE Data**
    if (isset($_POST["update"])) {
        $id = $_POST["id"];
        $updatedData = $_POST["updatedData"];

        $updateSQL = "UPDATE `$table` SET ";
        $updateFields = [];

        foreach ($updatedData as $column => $value) {
            $updateFields[] = "`$column` = '" . $conn->real_escape_string($value) . "'";
        }
        
        $updateSQL .= implode(", ", $updateFields);
        $updateSQL .= " WHERE `$primaryKey` = '" . $conn->real_escape_string($id) . "'";

        if ($conn->query($updateSQL)) {
            echo "<script>alert('Record updated successfully!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
        }
    }

    // **DELETE Data**
    if (isset($_POST["delete"])) {
        $id = $_POST["id"];

        $deleteSQL = "DELETE FROM `$table` WHERE `$primaryKey` = '" . $conn->real_escape_string($id) . "'";

        if ($conn->query($deleteSQL)) {
            echo "<script>alert('Record deleted successfully!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
        }
    }
}

$conn->close();
?>
