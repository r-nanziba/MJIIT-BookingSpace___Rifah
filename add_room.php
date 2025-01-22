<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];

    // Ensure the uploads directory exists
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Image upload logic
    $image = $_FILES['image']['name'];
    $target_file = $target_dir . uniqid() . '_' . basename($image); // Add a unique prefix to the file name
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg']; // Allowed file types

    // Validate file type
    if (!in_array($_FILES['image']['type'], $allowed_types)) {
        $_SESSION['popup_message'] = "Invalid file type. Only JPG and PNG are allowed.";
        $_SESSION['popup_type'] = "error";
        header("Location: add_room.php");
        exit;
    }

    // Validate file upload
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['popup_message'] = "File upload error: " . $_FILES['image']['error'];
        $_SESSION['popup_type'] = "error";
        header("Location: add_room.php");
        exit;
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO rooms (room_name, capacity, equipment, image) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siss", $room_name, $capacity, $equipment, $target_file);

        if ($stmt->execute()) {
            $_SESSION['popup_message'] = "Room added successfully!";
            $_SESSION['popup_type'] = "success";
        } else {
            $_SESSION['popup_message'] = "Error: " . $stmt->error;
            $_SESSION['popup_type'] = "error";
        }
        $stmt->close();
    } else {
        $_SESSION['popup_message'] = "Failed to upload image. Please check directory permissions and file size.";
        $_SESSION['popup_type'] = "error";
    }

    $conn->close();
    header("Location: add_room.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('bg website.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }

        .navbar {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.9);
            color: rgb(114, 4, 4);
            padding: 8px 20px;
            justify-content: space-between;
            width: 100%;
            border-bottom: 2px solid #8B0000;
            z-index: 10;
        }
        .navbar-title {
            display: flex;
            align-items: center;
        }
        .navbar-title img {
            max-height: 30px;
            margin-right: 10px;
        }
        .navbar-title p {
            font-weight: bold;
            font-size: 20px;
            margin: 0;
        }
        .navbar-links {
    display: flex;
    align-items: center;
    margin-left: auto;
}

.navbar-links a {
    color: rgb(119, 4, 4);
    text-decoration: none;
    margin-right: 20px;
    font-size: 14px;
}

.navbar-links a:hover {
    color: #ddd;
}

        .container {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        h3 {
            color: black;
        }

        hr {
            border-top: 2px solid black;
            margin-bottom: 20px;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-back {
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .btn-back:hover {
            background-color: #B22222;
        }
        .dropdown {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .dropdown-content.show {
            display: block;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<div class="navbar">
<div class="navbar-title">
            <img src="UTM-LOGO-FULL.png" alt="UTM Logo">
            <img src="Mjiit RoomMaster logo.png" alt="MJIIT Logo">
            <p>BookingSpace - Admin</p>
        </div>
        <div class="navbar-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="rooms_admin.php"><b>Rooms</b></a>
            <a href="adminusermanagement.php">Users</a>
            <a href="admin_analytics.php">Analytics</a>
        </div>
        <div class="dropdown">
            <i class="fa-solid fa-right-from-bracket"></i>
            <div class="dropdown-content">
                <a href="login.php">Logout</a>
            </div>
        </div>
</div>

<div class="container mt-5">
    <h3>Add Room</h3>
    <hr>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="room_name" class="form-label">Room Name</label>
            <input type="text" class="form-control" id="room_name" name="room_name" required>
        </div>
        <div class="mb-3">
            <label for="capacity" class="form-label">Capacity</label>
            <input type="number" class="form-control" id="capacity" name="capacity" required>
        </div>
        <div class="mb-3">
            <label for="equipment" class="form-label">Equipment</label>
            <input type="text" class="form-control" id="equipment" name="equipment" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Room Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
        </div>
        <div class="button-group">
            <button type="submit" class="btn btn-success">Add Room</button>
            <a href="rooms_admin.php" class="btn btn-back">Cancel</a>
        </div>
    </form>
</div>

<script>
    // SweetAlert popup logic
    document.addEventListener('DOMContentLoaded', function () {
        const message = "<?php echo $_SESSION['popup_message'] ?? ''; ?>";
        const type = "<?php echo $_SESSION['popup_type'] ?? ''; ?>";

        if (message) {
            Swal.fire({
                title: type === "success" ? "Success!" : "Error!",
                text: message,
                icon: type,
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Back to Rooms',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If "OK" is clicked
                    window.location.href = "add_room.php";  // Keeps the admin on the current page
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // If "Back to Rooms" is clicked
                    window.location.href = "rooms_admin.php";  // Redirects to the rooms page
                }
            });

            // Clear session variables after showing the popup
            <?php unset($_SESSION['popup_message'], $_SESSION['popup_type']); ?>
        }
    });

    // Toggle dropdown on click
    document.querySelector('.dropdown').addEventListener('click', function (e) {
        document.querySelector('.dropdown-content').classList.toggle('show');
        e.stopPropagation();
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function (e) {
        if (!e.target.matches('.fa-right-from-bracket')) {
            const dropdown = document.querySelector('.dropdown-content');
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    });
</script>
</body>
</html>
