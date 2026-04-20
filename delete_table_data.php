<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["table"], $_POST["id"])) {
    $table = $_POST["table"];
    $id = $_POST["id"];

    $deleteSQL = "DELETE FROM `$table` WHERE id = '$id'";

    if ($conn->query($deleteSQL)) {
        echo "Record deleted successfully!";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
}
?>
