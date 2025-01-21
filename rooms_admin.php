<?php
// Include database configuration file
include 'config.php';

// Fetch all rooms from the database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Booking Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #d3d3d3; /* Light grey */
            color: #000; /* Black text */
            vertical-align: middle;
    text-align: center;
}
        .table td {
    vertical-align: middle;
    text-align: center;
}
        .btn-success {
            border: none;
            border-radius: 4px;
            background-color:rgb(128, 58, 185);
        }

         .btn-danger, .btn-secondary {
            border: none;
            border-radius: 16px;
            padding: 8px 16px;
            font-size: 14px;
            width: 80px; /* Ensuring uniform width */
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 14px;
            width: 70px; /* Ensuring uniform width */
            display: inline-block;
            text-align: center;
        }
td .btn-container {
    display: flex;
    justify-content: center;
    gap: 10px;  /* Adds consistent spacing between buttons */
}
        .btn-primary {
            background-color: #FFC107; /* Yellowish color */
            color: #000;
        }
        .btn-primary:hover {
            background-color: #FFA000; /* Darker yellow */
        }
        .btn-danger {
            background-color: #DC3545; /* Red color */
            color: #fff;
        }
        .btn-danger:hover {
            background-color: #C82333; /* Darker red */
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 {
            color: #8B0000;
        }
        .no-requests {
            text-align: center;
            color: #333;
            font-size: 1.2em;
            margin: 20px 0;
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
    <!-- Navbar -->
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

    <!-- Main Content -->
    <div class="container">
        <div class="header">
            <h2><b>Room List</b></h2>
            <a href="add_room.php" class="btn btn-success">+Add New Room</a>
        </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Room ID</th>
                    <th>Room Name</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr data-room-id="<?php echo $row['room_id']; ?>">
            <td><?php echo htmlspecialchars($row['room_id']); ?></td>
            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
            <td><?php echo htmlspecialchars($row['capacity']); ?></td>
            <td><?php echo htmlspecialchars($row['equipment']); ?></td>
            <td>
                <div class="btn-container">
                    <a href="update_room.php?id=<?php echo $row['room_id']; ?>" class="btn btn-primary">Edit</a>&nbsp;
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-room-id="<?php echo $row['room_id']; ?>">Delete</button>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this room?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let roomToDelete = null;

// When delete button is clicked, store the room ID
document.querySelectorAll('[data-bs-target="#confirmDeleteModal"]').forEach(button => {
    button.addEventListener('click', function() {
        roomToDelete = this.getAttribute('data-room-id');
    });
});

// Handle the confirmation
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (roomToDelete) {
        // Create FormData object
        const formData = new FormData();
        formData.append('room_id', roomToDelete);

        // Send AJAX request
        fetch('delete_room_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                modal.hide();
                
                // Remove the row from the table
                const row = document.querySelector(`tr[data-room-id="${roomToDelete}"]`);
                if (row) {
                    row.remove();
                }
                
                // Optionally, show success message
                alert('Room deleted successfully');
                
                // Reload the page to refresh the table
                location.reload();
            } else {
                alert('Error deleting room: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the room');
        });
    }
});
</script>

    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


    </script>
    <script>
        // Toggle dropdown on click
        document.querySelector('.dropdown').addEventListener('click', function(e) {
            document.querySelector('.dropdown-content').classList.toggle('show');
            e.stopPropagation();
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
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

<?php
$conn->close();
?>
