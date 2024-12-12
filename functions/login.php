<?php
include_once 'user.php';
$user = new User($database);

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $emailOrUsername = $_POST['email-username'];
    $password = $_POST['password'];

    // Check if inputs are not empty
    if (!empty($emailOrUsername) && !empty($password)) {
        // Try to log the user in
        if ($user->login($emailOrUsername, $password)) {
            // Return success response
            echo json_encode(['success' => true]);
        } else {
            // Return error response for invalid credentials
            echo json_encode(['success' => false, 'message' => 'Invalid credentials, please try again.']);
        }
    } else {
        // Return error for empty fields
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    }
} else {
    // If request method is not POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
