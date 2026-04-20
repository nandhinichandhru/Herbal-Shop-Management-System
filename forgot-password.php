<?php
require 'db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST["email"]);
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate if the passwords match
    if ($new_password !== $confirm_password) {
        $msg = "Passwords do not match!";
    } else {
        // Use a variable for the LIKE pattern
        $email_like = "%$email%";
        
        // Check if user exists using LIKE for partial matching
        $stmt = $conn->prepare("SELECT * FROM users WHERE email LIKE ?");
        $stmt->bind_param("s", $email_like);  // Binding the variable $email_like
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update password if email is found
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password = '$hashed_password' WHERE email = '$email'");
            $msg = "Password updated successfully.";
        } else {
            $msg = "Email not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: "Playfair Display", serif;
      background: none;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      overflow: hidden;
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url("images/vecteezy_charming-display-of-dried-flowers-and-herbs-in-glass-jars-on_55302710.jpeg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      z-index: -1;
      pointer-events: none;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      padding: 35px 40px;
      border-radius: 15px;
      border: 2px solid rgba(0, 0, 0, 0.5);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
      width: 350px;
      transition: transform 0.3s ease;
    }

    .login-box:hover {
      transform: scale(1.015);
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #2c3e50;
      font-family: "Playfair Display", serif;
    }

    .login-box label {
      font-weight: 600;
      font-size: 16px;
      color: black;
      font-family: "Playfair Display", serif;
    }

    .login-box input {
      width: 100%;
      padding: 12px 15px;
      margin: 8px 0 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      font-size: 14px;
      transition: border-color 0.3s;
      font-family: "Playfair Display", serif;
    }

    .login-box input:focus {
      border-color: black;
      outline: none;
    }

    .login-box button {
      width: 100%;
      background: #d3b1c2;
      color: black;
      border: none;
      padding: 12px;
      margin-top: 10px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s, transform 0.3s;
      font-family: "Playfair Display", serif;
    }

    .login-box button:hover {
      background: #d3b1c2;
      transform: scale(1.05);
    }

    .message {
      text-align: center;
      margin-top: 15px;
      padding: 10px;
      border-radius: 8px;
      font-weight: bold;
      background-color: rgba(255, 255, 255, 0.5);
      color: #2c3e50;
      font-family: "Playfair Display", serif;
    }
  </style>
</head>

<body>
  <div class="login-box">
    <h2>Reset Password</h2>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>New Password:</label><br>
        <input type="password" name="new_password" required><br><br>
        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>
        <input type="submit" value="Reset Password">
    </form>
    <?php if (!empty($msg)): ?>
        <script type="text/javascript">
            alert("<?php echo $msg; ?>");
        </script>
    <?php endif; ?>
  </div>
</body>

</html>
