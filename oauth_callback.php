<?php
session_start();
require_once 'vendor/autoload.php'; // Include the Google API Client

// Initialize the Google Client
$client = new Google_Client();
$client->setApplicationName("MJIIT Booking Space");
$client->setScopes(Google_Service_Calendar::CALENDAR);
$client->setClientId('696475747552-44oedgu372b7uu9ptk8ht9fe09f4jlf6.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/MJIIT-Room-Master-main/oauth_callback.php'); // Make sure this matches your registered redirect URI
$client->setAuthConfig('/Applications/XAMPP/xamppfiles/htdocs/MJIIT-Room-Master-main/google-credentials.json');
$client->setAccessType('offline'); // Offline access (for refresh token)
$client->setPrompt('select_account consent'); // Always prompt for account selection

// If authorization code is provided
if (isset($_GET['code'])) {
    try {
        $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($accessToken['error'])) {
            throw new Exception('Error fetching access token: ' . $accessToken['error']);
        }

        // Store access token in session
        $_SESSION['google_token'] = $accessToken;

        // Redirect back to submit booking
        header('Location: submit_booking.php');
        exit();
    } catch (Exception $e) {
        echo "OAuth Error: " . $e->getMessage();
    }
} else {
    echo "No authorization code found.";
}
?>
