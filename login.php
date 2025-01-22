<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if the user exists in the 'users' table
    $sql_user = "SELECT user_id, username, password FROM users WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);

    if ($stmt_user === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();

    // Check if the user exists in the 'admins' table
    $sql_admin = "SELECT admin_id, username, password FROM admins WHERE username = ?";
    $stmt_admin = $conn->prepare($sql_admin);

    if ($stmt_admin === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt_admin->bind_param("s", $username);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    $admin = $result_admin->fetch_assoc();

    // Check if the user exists in the 'guests' table
    $sql_guest = "SELECT guest_id, guestname, password FROM guests WHERE guestname = ?";
    $stmt_guest = $conn->prepare($sql_guest);

    if ($stmt_guest === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt_guest->bind_param("s", $username);
    $stmt_guest->execute();
    $result_guest = $stmt_guest->get_result();
    $guest = $result_guest->fetch_assoc();

    // Check if the user is in 'admins' table and verify password
    if ($admin) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['admin_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } elseif ($user) {
        // Check if the user is in 'users' table and verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'user';
            header("Location: home.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } elseif ($guest) {
        if (password_verify($password, $guest['password'])) {
            $_SESSION['user_id'] = $guest['guest_id'];    // Changed from guest_id
            $_SESSION['username'] = $guest['guestname'];   // Changed from guestname
            $_SESSION['role'] = 'guest';
            header("Location: home.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "User not found.";
    }

    $stmt_user->close();
    $stmt_admin->close();
    $stmt_guest->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
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
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;  /* Changed from center to flex-start */
            min-height: 100vh;
            animation: fadeIn 1s ease-in-out;
            padding-top: 80px;  /* Added padding to move the form up */
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .form-container {
            background-color: #fff;
            width: 500px;  /* Set fixed width */
            min-height: 450px;  /* Set minimum height */
            padding: 40px 30px;  /* Increased padding */
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            opacity: 1;  /* Changed from 0.95 to 1 for solid appearance */
            transform: scale(1);
            transition: transform 0.3s ease;
            animation: formFadeIn 2s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @keyframes formFadeIn {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        .form-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;  /* Increased margin */
            flex-wrap: wrap;  /* Added to handle narrow screens */
        }

        .form-header img {
            height: 70px; /* Increase this value to make the logo larger */
    max-width: 350px; /* Optionally set a max-width for proper scaling */
    margin: 0 10px;
        }

        .form-header span {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 15px;  /* Added space above text */
            width: 100%;  /* Makes the text take full width */
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;  /* Added space below heading */
            color: #2a9d8f;  /* Added color to match theme */
        }

        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 14px;  /* Increased padding */
            margin: 12px 0;  /* Increased margin */
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }

        .form-container input[type="text"]:focus,
        .form-container input[type="password"]:focus {
            border-color: #2a9d8f;
            outline: none;
            box-shadow: 0 0 5px rgba(42, 157, 143, 0.2);  /* Added subtle focus shadow */
        }

        .form-container .button-container {
            display: flex;
            gap: 12px;  /* Increased gap */
            margin-top: 30px;  /* Increased margin */
        }

        .form-container button {
            padding: 12px;  /* Increased padding */
            border: none;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;  /* Made text bolder */
        }

        .form-container button[type="submit"] {
            background-color: #2a9d8f;
            color: #fff;
        }

        .form-container button[type="submit"]:hover {
            background-color: #21867a;
            transform: translateY(-1px);  /* Added subtle lift effect */
        }

        .form-container button[type="button"] {
            background-color: rgb(37, 173, 197);
            color: #fff;
        }

        .form-container button[type="button"]:hover {
            background-color: rgb(10, 131, 153);
            transform: translateY(-1px);  /* Added subtle lift effect */
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
    </style>
</head>
<body>
    <!-- Form Container -->
    <div class="form-container">
        <div class="form-header">
            <img src="UTM-LOGO-FULL.png" alt="UTM Logo">
   
            <span>Malaysia-Japan International Institute of Technology</span>
        </div>
        <h2>Sign In</h2>
        <?php
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Enter your username" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <div class="button-container">
                <button type="submit">Login</button>
                <button type="button" onclick="location.href='guest_signup.php';">Guest Sign Up</button>
            </div>
        </form>
    </div>
</body>
</html>
