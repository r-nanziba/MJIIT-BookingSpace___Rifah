<?php
session_start();
require_once 'vendor/autoload.php'; // Include the Google API Client

// Initialize the Google Client
$client = new Google_Client();
$client->setApplicationName("MJIIT Booking Space");
$client->setScopes(Google_Service_Calendar::CALENDAR);
$client->setClientId('696475747552-98p0og81kg0db1da5dkg8hr6re9dghc9.apps.googleusercontent.com');  // Add new Client ID here
$client->setClientSecret('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');  // Add new Client Secret here
$client->setRedirectUri('http://localhost/MJIIT-Room-Master-main/oauth_callback.php'); // Make sure this matches your registered redirect URI
$client->setAuthConfig('/Applications/XAMPP/xamppfiles/htdocs/MJIIT-Room-Master-main/google-credentials.json');
$client->setAccessType('offline'); // Offline access (for refresh token)
$client->setPrompt('select_account consent'); // Always prompt for account selection

echo $client->createAuthUrl();
exit;

// If there's no code parameter in the URL, redirect the user to Google's OAuth consent screen
if (!isset($_GET['code'])) {
    // Create the OAuth URL and redirect the user to Google
    $authUrl = $client->createAuthUrl();
echo "OAuth URL: " . $authUrl; // Debugging line
exit;

}

// If the code is present, exchange it for an access token
if (isset($_GET['code'])) {
    // Fetch the access token using the authorization code
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // Check if the access token is valid
    if (isset($accessToken['error'])) {
        // If there's an error, redirect the user to the OAuth consent screen again
        header('Location: ' . $client->createAuthUrl());
        exit;
    }

    // Store the access token in the session for future use
    $_SESSION['google_token'] = $accessToken;

    // Redirect the user back to the page where they can now use their authenticated session (e.g., submit_booking.php)
    header('Location: submit_booking.php');

    
    exit;
}
?>
