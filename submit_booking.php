<?php
session_start();
require_once 'vendor/autoload.php'; // Include the Google API Client
include 'config.php'; // Database configuration

// Initialize Google Client
$client = new Google_Client();
$client->setApplicationName("MJIIT Booking Space");
$client->setScopes(Google_Service_Calendar::CALENDAR);
$client->setClientId('696475747552-44oedgu372b7uu9ptk8ht9fe09f4jlf6.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/MJIIT-Room-Master-main/oauth_callback.php');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

// Check if user is authenticated
if (!isset($_SESSION['google_token'])) {
    // Redirect to Google OAuth consent screen
    header('Location: ' . $client->createAuthUrl());
    exit();
}

// Set the access token
$client->setAccessToken($_SESSION['google_token']);

// Refresh token if expired
if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    $_SESSION['google_token'] = $client->getAccessToken();
}

// Process booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $room = $_POST['room'];
    $date = $_POST['booking_date'];
    $checkin_time = $_POST['checkin_time'];
    $checkout_time = $_POST['checkout_time'];

    $startDateTime = $date . 'T' . $checkin_time . ':00';
    $endDateTime = $date . 'T' . $checkout_time . ':00';

    // Check if room is available
    $sql = "SELECT room_id FROM rooms WHERE room_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $room);
    $stmt->execute();
    $stmt->bind_result($room_id);
    $stmt->fetch();
    $stmt->close();

    if ($room_id) {
        // Check for conflicts
        $sql = "SELECT COUNT(*) FROM bookings 
                WHERE room_id = ? AND booking_date = ? 
                AND ((start_time < ? AND end_time > ?) 
                     OR (start_time < ? AND end_time > ?))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $room_id, $date, $checkout_time, $checkin_time, $checkin_time, $checkout_time);
        $stmt->execute();
        $stmt->bind_result($conflict_count);
        $stmt->fetch();
        $stmt->close();

        if ($conflict_count > 0) {
            $_SESSION['popup_message'] = "Room is already booked.";
            $_SESSION['popup_type'] = "error";
            header("Location: my_bookings.php");
            exit();
        }

        // Insert booking
        $sql = "INSERT INTO bookings (user_id, room_id, booking_date, start_time, end_time, status)
                VALUES (?, ?, ?, ?, ?, 'Confirmed')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $user_id, $room_id, $date, $checkin_time, $checkout_time);
        $stmt->execute();
        $stmt->close();

        // Send Google Calendar invite
        $service = new Google_Service_Calendar($client);
        $event = new Google_Service_Calendar_Event([
            'summary' => 'Room Booking: ' . $room,
            'location' => 'MJIIT',
            'description' => 'Room booked for your event.',
            'start' => ['dateTime' => $startDateTime, 'timeZone' => 'Asia/Kuala_Lumpur'],
            'end' => ['dateTime' => $endDateTime, 'timeZone' => 'Asia/Kuala_Lumpur'],
            'attendees' => [['email' => 'your-email@example.com']], // Replace with dynamic email
        ]);

        try {
            $event = $service->events->insert('primary', $event);
            $_SESSION['popup_message'] = "Booking confirmed. Google Calendar invite sent!";
            $_SESSION['popup_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['popup_message'] = "Booking confirmed but failed to send Google Calendar invite.";
            $_SESSION['popup_type'] = "error";
        }

        // Redirect to bookings page
        header("Location: my_bookings.php");
        exit();
    } else {
        $_SESSION['popup_message'] = "Room not found.";
        $_SESSION['popup_type'] = "error";
        header("Location: my_bookings.php");
        exit();
    }
} else {
    $_SESSION['popup_message'] = "Invalid request.";
    $_SESSION['popup_type'] = "error";
    header("Location: my_bookings.php");
    exit();
}
?>
