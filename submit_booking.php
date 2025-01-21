<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'vendor/autoload.php'; // Include the Google API Client

// Initialize Google Client
$client = new Google_Client();
$client->setApplicationName("MJIIT Booking Space");
$client->setScopes(Google_Service_Calendar::CALENDAR);
$client->setClientId('696475747552-98p0og81kg0db1da5dkg8hr6re9dghc9.apps.googleusercontent.com');  // Add new Client ID here
$client->setClientSecret('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');  // Add new Client Secret here
$client->setRedirectUri('http://localhost/MJIIT-Room-Master-main/oauth_callback.php'); // Make sure this matches your registered redirect URI
$client->setAccessType('offline'); // Offline access (for refresh token)
$client->setPrompt('select_account consent'); // Always prompt for account selection

// Check if the Google token is set in the session
if (!isset($_SESSION['google_token'])) {
    // If not, redirect the user to the OAuth consent screen for authentication
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit;
}

// Set the access token from the session
$client->setAccessToken($_SESSION['google_token']);

// Check if the access token is expired
if ($client->isAccessTokenExpired()) {
    // If the token is expired, check for the refresh token
    if ($client->getRefreshToken()) {
        // Refresh the access token using the refresh token
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        // Save the new access token in the session
        $_SESSION['google_token'] = $client->getAccessToken();
    } else {
        // If there's no refresh token, redirect user to re-authenticate
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }
}

// Initialize the Google Calendar service
$service = new Google_Service_Calendar($client);

// Now proceed with the booking logic
include 'config.php'; // Include your database connection setup

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $room = $_POST['room'];
    $date = $_POST['booking_date'];
    $checkin_time = $_POST['checkin_time'];
    $checkout_time = $_POST['checkout_time'];

    // Convert times to 24-hour format for comparison
    $opening_time = "08:00";
    $closing_time = "20:00";

    if ($checkin_time < $opening_time || $checkout_time > $closing_time) {
        $_SESSION['popup_message'] = "Booking is only allowed between 8:00 AM and 8:00 PM.";
        $_SESSION['popup_type'] = "error";
        header("Location: my_bookings.php");
        exit;
    }

    // Get room ID based on room name
    $sql_room = "SELECT room_id FROM rooms WHERE room_name = ?";
    $stmt_room = $conn->prepare($sql_room);
    $stmt_room->bind_param("s", $room);
    $stmt_room->execute();
    $stmt_room->bind_result($room_id);
    $stmt_room->fetch();
    $stmt_room->close();

    if ($room_id) {
        // Check for overlapping bookings
        $sql_check = "SELECT COUNT(*) FROM bookings 
                      WHERE room_id = ? 
                      AND booking_date = ? 
                      AND (
                          (start_time < ? AND end_time > ?) OR 
                          (start_time < ? AND end_time > ?) OR 
                          (start_time >= ? AND end_time <= ?)
                      )";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("isssssss", $room_id, $date, $checkout_time, $checkin_time, $checkin_time, $checkout_time, $checkin_time, $checkout_time);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            // Conflict exists
            $_SESSION['popup_message'] = "This room is already booked for the selected time.";
            $_SESSION['popup_type'] = "error";
            header("Location: my_bookings.php");
            exit;
        } else {
            // Insert booking into the database
            $sql = "INSERT INTO bookings (user_id, room_id, booking_date, start_time, end_time, status)
                    VALUES (?, ?, ?, ?, ?, 'Confirmed')";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisss", $user_id, $room_id, $date, $checkin_time, $checkout_time);

            if ($stmt->execute()) {
                // Get user email and room details for Google Calendar
                $sql_user = "SELECT email FROM users WHERE user_id = ?";
                $stmt_user = $conn->prepare($sql_user);
                $stmt_user->bind_param("i", $user_id);
                $stmt_user->execute();
                $stmt_user->bind_result($userEmail);
                $stmt_user->fetch();
                $stmt_user->close();

                // Get room details for Google Calendar
                $sql_room_details = "SELECT room_name, location FROM rooms WHERE room_id = ?";
                $stmt_room_details = $conn->prepare($sql_room_details);
                $stmt_room_details->bind_param("i", $room_id);
                $stmt_room_details->execute();
                $stmt_room_details->bind_result($roomName, $location);
                $stmt_room_details->fetch();
                $stmt_room_details->close();

                // Prepare the Google Calendar event
                $startDateTime = $date . 'T' . $checkin_time . ':00'; // Start time in ISO format
                $endDateTime = $date . 'T' . $checkout_time . ':00'; // End time in ISO format

                $event = new Google_Service_Calendar_Event([
                    'summary' => 'Room Booking: ' . $roomName,
                    'location' => $location,
                    'description' => 'Room booked for a seminar/event.',
                    'start' => [
                        'dateTime' => $startDateTime, // Start time in ISO 8601 format
                        'timeZone' => 'Asia/Kuala_Lumpur', // Time zone
                    ],
                    'end' => [
                        'dateTime' => $endDateTime, // End time in ISO 8601 format
                        'timeZone' => 'Asia/Kuala_Lumpur', // Time zone
                    ],
                    'attendees' => [
                        ['email' => $userEmail], // User’s email for the calendar invite
                    ],
                    'reminders' => [
                        'useDefault' => true, // Use default reminders
                    ],
                ]);

                // Insert the event into Google Calendar
                try {
                    $event = $service->events->insert('primary', $event);
                    $_SESSION['popup_message'] = "Booking request submitted successfully. Google Calendar invite sent.";
                    $_SESSION['popup_type'] = "success";
                } catch (Google_Service_Exception $e) {
                    error_log("Google API error: " . $e->getMessage());
                    $_SESSION['popup_message'] = "Error occurred while creating the event: " . $e->getMessage();
                    $_SESSION['popup_type'] = "error";
                }
                
                
            } else {
                $_SESSION['popup_message'] = "Error occurred while submitting your booking.";
                $_SESSION['popup_type'] = "error";
            }

            $stmt->close();
            // Redirect to the bookings page with a popup
            header("Location: my_bookings.php");
            exit;
        }
    } else {
        // Room not found
        $_SESSION['popup_message'] = "Room not found.";
        $_SESSION['popup_type'] = "error";
        header("Location: my_bookings.php");
        exit;
    }
} else {
    // Invalid request
    $_SESSION['popup_message'] = "Invalid request.";
    $_SESSION['popup_type'] = "error";
    header("Location: my_bookings.php");
    exit;
}

$conn->close();
