<?php
$servername = "Localhost";
$username = "root"; // Change if necessary
$password = "pooja2005@sql"; // Change if necessary
$database = "sample"; // Ensure this is the correct database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
