<?php
/**
 * Database Configuration
 * Uses environment variables for security
 */

function getDbConnection() {
    $host = getenv('DB_HOST') ?: 'database';
    $dbname = getenv('DB_NAME') ?: 'suggestion_db';
    $username = getenv('DB_USER') ?: 'app_user';
    $password = getenv('DB_PASS') ?: '';

    try {
        $conn = new mysqli($host, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            throw new Exception("Database connection failed");
        }
        
        // Set charset to prevent SQL injection
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        throw new Exception("Database unavailable");
    }
}

/**
 * Security: Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Security: Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Security: Sanitize output to prevent XSS
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>