<?php
require_once '../config/database.php';
require_once 'auth.php';

startSecureSession();

// If already logged in, redirect to index
if (isLoggedIn()) {
    header('Location: /index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            $conn = getDbConnection();
            
            if (loginUser($conn, $username, $password)) {
                $conn->close();
                header('Location: /index.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
            
            $conn->close();
        } catch (Exception $e) {
            $error = 'Login failed. Please try again later.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Staff Suggestions</title>
    <link rel="stylesheet" href="styleb.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>Staff Suggestions</h1>
            <h2>Login</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="/login.php">
                <div class="form-group">
                    <label for="username">Company Username:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                  
                       	pattern="70[0-9]{4}09"
                        maxlength="8"
                        required 
                        autofocus
                    >
                    
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn-primary">Login</button>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="/register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>