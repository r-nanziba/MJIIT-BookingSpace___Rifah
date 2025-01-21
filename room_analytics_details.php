<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get room ID from URL parameter
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

// Fetch room details
$roomQuery = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
$roomQuery->bind_param("i", $room_id);
$roomQuery->execute();
$roomResult = $roomQuery->get_result();
$roomDetails = $roomResult->fetch_assoc();

// Most booked time slots
$timeSlotQuery = "
    SELECT 
        HOUR(start_time) as hour, 
        COUNT(*) as booking_count 
    FROM 
        bookings 
    WHERE 
        room_id = ? 
    GROUP BY 
        HOUR(start_time) 
    ORDER BY 
        booking_count DESC 
    LIMIT 3
";
$timeStmnt = $conn->prepare($timeSlotQuery);
$timeStmnt->bind_param("i", $room_id);
$timeStmnt->execute();
$timeResult = $timeStmnt->get_result();

// Prepare data for chart
$chartDataQuery = "
    SELECT 
        DATE(booking_date) as booking_date, 
        COUNT(*) as booking_count 
    FROM 
        bookings 
    WHERE 
        room_id = ? 
    GROUP BY 
        DATE(booking_date) 
    ORDER BY 
        booking_date ASC
";
$chartStmnt = $conn->prepare($chartDataQuery);
$chartStmnt->bind_param("i", $room_id);
$chartStmnt->execute();
$chartResult = $chartStmnt->get_result();

$dates = [];
$counts = [];
while ($row = $chartResult->fetch_assoc()) {
    $dates[] = $row['booking_date'];
    $counts[] = $row['booking_count'];
}

// Booking status data for pie chart
$statusDataQuery = "
    SELECT 
        status, 
        COUNT(*) as status_count 
    FROM 
        bookings 
    WHERE 
        room_id = ? 
    GROUP BY 
        status
";
$statusStmnt = $conn->prepare($statusDataQuery);
$statusStmnt->bind_param("i", $room_id);
$statusStmnt->execute();
$statusResult = $statusStmnt->get_result();

$statuses = [];
$statusCounts = [];
while ($row = $statusResult->fetch_assoc()) {
    $statuses[] = $row['status'];
    $statusCounts[] = $row['status_count'];
}

// Booking history
$bookingHistoryQuery = "
    SELECT 
        booking_date, 
        start_time, 
        end_time, 
        status 
    FROM 
        bookings 
    WHERE 
        room_id = ? 
    ORDER BY 
        booking_date DESC 
    LIMIT 10
";
$historyStmnt = $conn->prepare($bookingHistoryQuery);
$historyStmnt->bind_param("i", $room_id);
$historyStmnt->execute();
$historyResult = $historyStmnt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($roomDetails['room_name']); ?> Analytics - BookingSpace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .navbar-profile {
            position: relative;
        }

        .navbar-profile i {
            font-size: 22px;
            cursor: pointer;
        }

        .dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 1;
        }

        .dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown a:hover {
            background-color: #ddd;
        }

        .navbar-profile:hover .dropdown {
            display: block;
        }

        .room-details-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .room-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .room-image {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .room-title h2 {
            margin: 0;
        }

        .analytics-section {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }

        .table {
            background-color: white;
        }

        .charts-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .chart-container {
            flex: 1;
            margin-top: 0;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">
            <img src="UTM-LOGO-FULL.png" alt="UTM Logo">
            <img src="Mjiit RoomMaster logo.png" alt="MJIIT Logo">
            <p>BookingSpace Analytics</p>
        </div>
        <div class="navbar-links">
            <a href="home.php">Home</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="rooms.php">Rooms</a>
            <a href="analytics.php">Analytics</a>
            <a href="help.php">Help</a>
        </div>
        <div class="navbar-profile">
            <i class="fa-solid fa-user"></i>
            <div class="dropdown">
                <a href="login.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="room-details-container">
        <div class="room-header">
            <img src="<?php echo htmlspecialchars($roomDetails['image']); ?>" alt="Room Image" class="room-image">
            <div class="room-title">
                <h2><?php echo htmlspecialchars($roomDetails['room_name']); ?> Analytics</h2>
            </div>
        </div>

        <div class="analytics-section">
            <h4>Room Details</h4>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($roomDetails['location']); ?></p>
            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($roomDetails['capacity']); ?> People</p>
            <p><strong>Equipment:</strong> <?php echo htmlspecialchars($roomDetails['equipment']); ?></p>
        </div>

        <div class="analytics-section">
            <h4>Most Popular Time Slots</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Hour</th>
                        <th>Number of Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($timeSlot = $timeResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $timeSlot['hour'] . ":00 - " . ($timeSlot['hour'] + 1) . ":00</td>";
                        echo "<td>" . $timeSlot['booking_count'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="analytics-section">
            <h4>Recent Booking History</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($booking = $historyResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($booking['booking_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($booking['start_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($booking['end_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($booking['status']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="charts-container">
            <div class="chart-container">
                <h4>Room Usage Over Time</h4>
                <canvas id="roomUsageChart"></canvas>
            </div>

            <div class="chart-container">
                <h4>Booking Status Distribution</h4>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Room Usage Chart
        const ctx1 = document.getElementById('roomUsageChart').getContext('2d');
        const roomUsageChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Number of Bookings',
                    data: <?php echo json_encode($counts); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Booking Status Chart
        const ctx2 = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($statuses); ?>,
                datasets: [{
                    label: 'Booking Status',
                    data: <?php echo json_encode($statusCounts); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>