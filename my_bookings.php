<?php
session_start();
include 'config.php';

// Handle cancellation requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking_id'])) {
    $booking_id = $_POST['cancel_booking_id'];

    // Update the status to 'Cancelled' in the database
    $sql = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        $_SESSION['popup_message'] = "Booking successfully canceled.";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "Error occurred while canceling the booking.";
        $_SESSION['popup_type'] = "error";
    }

    $stmt->close();
    header("Location: my_bookings.php");
    exit();
}

// Retrieve bookings for the logged-in user
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT b.booking_id, r.room_name, b.booking_date, b.start_time, b.end_time, b.status 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.room_id 
            WHERE b.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - MJIIT RoomMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* General Styles */
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

        /* Navbar Styling */
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
            gap: 20px;
        }

        .navbar-links a {
            color: rgb(119, 4, 4);
            text-decoration: none;
            margin-right: 0px;
            font-size: 14px;
        }

        .navbar-links a:hover {
            color: #ddd;
        }

        .navbar-profile i {
            font-size: 24px;
        }

        /* Booking Header Styling */
        .booking-header {
            background-color: #8B0000;
            color: white;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            width: 75%; /* Match width to booking list */
            margin-left: auto;
            margin-right: auto;
            margin-top: 30px; /* Adds gap between header and navbar */
        }

        /* Booking List Container */
        .my-booking-container {
            margin-top: 50px;
            padding: 20px;
            background-color: white;
            border-radius: 6px;
            width: 75%;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Booking List Styling */
        .booking-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .booking-item {
            background-color: #e0e0e0;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-details {
            flex-grow: 1;
            margin-right: 20px;
        }

        .booking-item h3 {
            color: #000000;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .booking-item p {
            margin: 5px 0;
        }

        .status-confirmed {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .cancel-btn {
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            margin-left: auto;
        }

        .cancel-btn:hover {
            background-color: #5f2a1e;
        }

        /* Button Styling */
        .btn-book-new {
            background-color: white;
            color: #8B0000;
            border: 2px solid #8B0000;
            font-weight: bold;
        }

        .btn-book-new:hover {
            background-color: #8B0000;
            color: white;
        }

        /* Profile Dropdown Styles */
.dropdown {
    position: relative;
    display: inline-block;
    margin-left: 0; /* Remove any left margin */
}

.fa-user {
    font-size: 22px;
    cursor: pointer;
    color: rgb(119, 4, 4);
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 160px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    transition: all 0.2s ease-in-out;
    opacity: 0;
    visibility: hidden;
}

.dropdown-content.show {
    display: block;
    opacity: 1;
    visibility: visible;
}

.dropdown-content a {
    color: rgb(119, 4, 4);
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    transition: background-color 0.2s;
}

.dropdown-content a:hover {
    background-color: #ddd;
    color: rgb(119, 4, 4);
}

    </style>

</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
    <div class="navbar-title">
        <img src="UTM-LOGO-FULL.png" alt="UTM Logo">
        <img src="Mjiit RoomMaster logo.png" alt="MJIIT Logo">
        <p>BookingSpace</p>
    </div>
    <div class="navbar-links">
        <a href="home.php">Home</a>
        <a href="my_bookings.php"><b>My Bookings</b></a>
        <a href="rooms.php">Rooms</a>
        <a href="analytics.php">Analytics</a>
        <a href="help.php">Help</a>
        <div class="dropdown">
            <i class="fa-solid fa-user" id="profileIcon"></i>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="profile.php">Profile</a>
                <a href="login.php">Logout</a>
            </div>
        </div>
    </div>
</div>

    <div class="booking-header mb-4">
        <h2>My Bookings</h2>
        <p>You may find the status of your bookings here</p>
    </div>

    <div class="container mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Date</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['room_name']; ?></td>
                        <td><?php echo $row['booking_date']; ?></td>
                        <td><?php echo $row['start_time']; ?></td>
                        <td><?php echo $row['end_time']; ?></td>
                        <td><?php echo ucfirst($row['status']) ?: 'Cancelled'; ?></td>
                        <td>
                            <?php if ($row['status'] !== 'Cancelled'): ?>
                                <button 
                                    class="btn btn-danger btn-sm cancel-booking-btn"
                                    data-booking-id="<?php echo $row['booking_id']; ?>">
                                    Cancel
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript for cancellation confirmation -->
    <script>
        document.querySelectorAll('.cancel-booking-btn').forEach(button => {
            button.addEventListener('click', function () {
                const bookingId = this.getAttribute('data-booking-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to cancel this booking?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, cancel it',
                    cancelButtonText: 'No, keep it',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'my_bookings.php';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'cancel_booking_id';
                        input.value = bookingId;

                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const message = "<?php echo $_SESSION['popup_message'] ?? ''; ?>";
            const type = "<?php echo $_SESSION['popup_type'] ?? ''; ?>";

            if (message) {
                Swal.fire({
                    title: type === "success" ? "Success!" : "Error!",
                    text: message,
                    icon: type,
                    confirmButtonText: "OK"
                });

                <?php unset($_SESSION['popup_message'], $_SESSION['popup_type']); ?>
            }
        });
    </script>
    <script>
    // Toggle dropdown on click
    document.querySelector('.fa-user').addEventListener('click', function(e) {
        const dropdown = document.querySelector('.dropdown-content');
        dropdown.classList.toggle('show');
        e.stopPropagation();
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (!e.target.matches('.fa-user')) {
            const dropdown = document.querySelector('.dropdown-content');
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    });
</script>
</body>
</html>
