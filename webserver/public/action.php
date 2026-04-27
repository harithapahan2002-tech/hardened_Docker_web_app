<?php
require_once '../config/database.php';
require_once 'auth.php';

startSecureSession();
requireLogin(); // Redirect to login if not authenticated

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit();
}

// CSRF protection
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    die('Invalid security token. Please try again.');
}

$fullname = trim($_POST['fullname'] ?? '');
$suggestion = trim($_POST['suggestion'] ?? '');

// Validation
if (empty($fullname) || empty($suggestion)) {
    die('All fields are required. Please go back and try again.');
}

// Length validation
if (strlen($fullname) > 64) {
    die('Display name is too long (max 64 characters).');
}

if (strlen($suggestion) > 10000) {
    die('Suggestion is too long (max 10000 characters).');
}

try {
    $conn = getDbConnection();
    
    // Insert suggestion with user association
    $stmt = $conn->prepare("INSERT INTO suggestions (user_id, fullname, suggestion) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $fullname, $suggestion);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        
        // Redirect back to index with success
        header('Location: /index.php?success=1');
        exit();
    } else {
        $stmt->close();
        $conn->close();
        die('Failed to submit suggestion. Please try again.');
    }
} catch (Exception $e) {
    error_log("Error submitting suggestion: " . $e->getMessage());
    die('An error occurred. Please try again later.');
}
?>