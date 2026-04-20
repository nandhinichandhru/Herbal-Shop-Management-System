<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Get role from representative table
    $roleStmt = $conn->prepare("SELECT Role FROM representative WHERE Email = ?");
    $roleStmt->bind_param("s", $email);
    $roleStmt->execute();
    $roleStmt->store_result();
    $roleStmt->bind_result($role);
    
    if ($roleStmt->num_rows > 0) {
        $roleStmt->fetch();
        $roleStmt->close();

        // Check credentials in the respective table
        if ($role == "Admin") {
            $stmt = $conn->prepare("SELECT Password FROM admin WHERE Email = ?");
        } else {
            $stmt = $conn->prepare("SELECT Password FROM employee WHERE Email = ?");
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashedPassword);
        
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $hashedPassword)) {
                $_SESSION["user"] = $email;
                $_SESSION["role"] = $role;

                if ($role == "Admin") {
                    header("Location: sample.php");
                } else {
                    header("Location: newemployee.php");
                }
                exit();
            } else {
                echo "Invalid credentials.";
            }
        } else {
            echo "User not found.";
        }
        $stmt->close();
    } else {
        echo "User role not found.";
    }

    $conn->close();
}
?>
