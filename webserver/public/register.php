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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        try {
            $conn = getDbConnection();
            
            $result = registerUser($conn, $username, $password);
            
            if ($result === true) {
                $success = 'Registration successful! You can now log in.';
                // Clear form
                $username = '';
            } else {
                $error = $result; // Error message from registerUser
            }
            
            $conn->close();
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again later.';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Staff Suggestions</title>
    <link rel="stylesheet" href="styleb.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>Staff Suggestions</h1>
            <h2>Register</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <?php echo escape($success); ?>
                    <br><a href="/login.php">Click here to login</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/register.php">
                <div class="form-group">
                    <label for="username">Company Username:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                         
                        pattern="70[0-9]{4}09"
                        maxlength="8"
                        value="<?php echo escape($username ?? ''); ?>"
                        required 
                        autofocus
                    >
                    <small>Format: Must start with <strong>70</strong> exactly 8 characters</small>
                    
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                    >
                    <small>Minimum 8 characters, include uppercase, lowercase, and numbers</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn-primary">Register</button>
            </form>
            
            <div class="auth-links">
                <p>Already have an account? <a href="/login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>