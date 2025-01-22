<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if POST data exists
    if (isset($_POST['guestname']) && isset($_POST['email']) && isset($_POST['password'])) {
        $guestname = trim($_POST['guestname']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Hash the password before saving it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the guestname or email already exists
        $sql_check = "SELECT * FROM guests WHERE guestname = ? OR email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $guestname, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "<p class='error-message'>Guestname or email already exists!</p>";
        } else {
            // Insert the new guest into the database
            $sql = "INSERT INTO guests (guestname, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $guestname, $email, $hashed_password);

            if ($stmt->execute()) {
                echo "<p class='success-message'>Guest registered successfully!</p>";
            } else {
                echo "<p class='error-message'>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        $stmt_check->close();
        $conn->close();
    } else {
        echo "<p class='error-message'>Please fill in all the fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Sign Up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: url('UTMbg.png') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            animation: fadeIn 1s ease-in-out;
            padding-top: 80px;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .profile-icon {
            color: #333;
            font-size: 24px;
            margin-right: 20px;
        }

        .form-container {
            background-color: #fff;
            width: 600px;
            min-height: 450px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            opacity: 1;
            transform: scale(1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .form-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px; /* Increased from 20px */
            flex-wrap: wrap;
            gap: 20px; /* Added gap between elements */
        }

        .form-header img {
            height: 50px;
            margin: 0 15px; /* Increased from 10px */
        }

        .form-header span {
            font-size: 18px;
            font-weight: bold;
            color: rgb(43, 51, 52);
            margin-top: 10px; /* Increased from 15px */
            width: 100%;
            text-align: center;
        }

        h2 {
            margin-bottom: 30px; /* Increased from 20px */
            color: rgb(37, 173, 197);
            margin-top: 0px; /* Added negative margin to balance spacing */
        }
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }

        .form-container input[type="text"]:focus,
        .form-container input[type="email"]:focus,
        .form-container input[type="password"]:focus {
            border-color: rgb(37, 173, 197);
            outline: none;
            box-shadow: 0 0 5px rgba(37, 173, 197, 0.2);
        }

        .form-container button {
            background-color: rgb(37, 173, 197);
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            margin-top: 20px;
        }

        .form-container button:hover {
            background-color: rgb(10, 131, 153);
            transform: translateY(-1px);
        }

        .error-message {
            color: #ff3333;
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffe6e6;
            border-radius: 5px;
            display: inline-block;
        }

        .success-message {
            color: #28a745;
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #d4edda;
            border-radius: 5px;
            display: inline-block;
        }

        .back-button {
            display: block;
            margin-top: 15px;
            color: rgb(37, 173, 197);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: rgb(10, 131, 153);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <div class="form-header">
            <img src="UTM-LOGO-FULL.png" alt="UTM Logo">
            <img src="Mjiit RoomMaster logo.png" alt="MJIIT Logo">
            <span>Malaysia-Japan International Institute of Technology</span>
        </div>
        <h2>Guest Sign Up</h2>
        <form action="" method="POST">
            <input type="text" name="guestname" placeholder="Enter your name" required>
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <input type="password" name="confirm_password" placeholder="Re-enter your password" required>
            <button type="submit">Sign Up</button>
        </form>
        <a href="login.php" class="back-button">Already have an account? Login here</a>
    </div>
</body>
</html>
