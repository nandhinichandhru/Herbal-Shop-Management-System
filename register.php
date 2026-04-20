<?php
require 'db.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $role = $_POST["role"];

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT Email FROM representative WHERE Email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        die("Email is already registered.");
    }
    $checkStmt->close();

    // Insert into the appropriate table based on role
    if ($role == "Admin") {
        $stmt = $conn->prepare("INSERT INTO admin (Name, Email, Password) VALUES (?, ?, ?)");
    } else {
        $stmt = $conn->prepare("INSERT INTO employee (Name, Email, Password) VALUES (?, ?, ?)");
    }

    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        // Insert into representative table
        $repStmt = $conn->prepare("INSERT INTO representative (Email, Role) VALUES (?, ?)");
        $repStmt->bind_param("ss", $email, $role);
        $repStmt->execute();
        $repStmt->close();

         echo "<script>alert('Registration successful!!'); window.location.href='register.html';</script>";


        
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
