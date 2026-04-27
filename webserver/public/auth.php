<?php
/**
 * Authentication Functions
 */

// Start secure session
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0);
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Require login - redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

/**
 * Validate company username format: 70XXXX09 (exactly 8 characters)
 */
function validateCompanyUsername($username) {
    // Must be exactly 8 characters
    if (strlen($username) !== 8) {
        return false;
    }
    
    // Must start with 70
    if (substr($username, 0, 2) !== '70') {
        return false;
    }
    
    // Must end with 09
    if (substr($username, -2) !== '09') {
        return false;
    }
    
    // Middle 4 characters must be digits
    $middle = substr($username, 2, 4);
    if (!ctype_digit($middle)) {
        return false;
    }
    
    return true;
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    // Minimum 8 characters, at least one uppercase, one lowercase, one number
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number";
    }
    return true;
}

/**
 * Login user
 */
function loginUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password_hash'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            
            // Update last login
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            $updateStmt->close();
            
            $stmt->close();
            return true;
        }
    }
    
    $stmt->close();
    return false;
}

/**
 * Register new user
 */
function registerUser($conn, $username, $password) {
    // Validate username format
    if (!validateCompanyUsername($username)) {
        return "Invalid username format. Must be 8 characters: 70XXXX09";
    }
    
    // Validate password
    $passwordValidation = validatePassword($password);
    if ($passwordValidation !== true) {
        return $passwordValidation;
    }
    
    // Check if username already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        return "Username already exists";
    }
    $checkStmt->close();
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $passwordHash);
    
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return "Registration failed. Please try again.";
    }
}

/**
 * Logout user
 */
function logoutUser() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}
?>