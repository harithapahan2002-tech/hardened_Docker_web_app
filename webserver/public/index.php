<?php
require_once '../config/database.php';
require_once 'auth.php';
#INDEX.PHP
startSecureSession();
requireLogin(); // Redirect to login if not authenticated

try {
    $conn = getDbConnection();
    
    // Fetch all suggestions with user information
    $stmt = $conn->prepare("
        SELECT s.id, s.fullname, s.suggestion, s.created_at, u.username 
        FROM suggestions s 
        JOIN users u ON s.user_id = u.id 
        ORDER BY s.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error fetching suggestions: " . $e->getMessage());
    $suggestions = [];
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Suggestions</title>
    <link rel="stylesheet" href="styleb.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Staff Suggestions</h1>
            <div class="user-info">
                <span>Welcome, <?php echo escape($_SESSION['username']); ?></span>
                <a href="/logout.php" class="btn-secondary">Logout</a>
            </div>
        </div>
        
        <h3>Share your constructive ideas to improve our workplace!</h3>
        
        <div class="suggestion-form">
            <form action="/action.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo escape($csrfToken); ?>">
                
                <div class="form-group">
                    <label for="fullname">Display Name:</label>
                    <input 
                        type="text" 
                        id="fullname" 
                        name="fullname" 
                        placeholder="Your name (or Anonymous)" 
                        maxlength="64"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="suggestion">Your Suggestion:</label>
                    <textarea 
                        id="suggestion" 
                        name="suggestion" 
                        rows="5" 
                        placeholder="Share your ideas to improve our workplace..."
                        required
                    ></textarea>
                </div>
                
                <button type="submit" class="btn-primary">Submit Suggestion</button>
            </form>
        </div>
        
        <p class="info-text">Submit your suggestions to help us create a better working environment.</p>
        
       <div class="suggestions-list">
            <h2>Recent Suggestions</h2>
            
            <?php if (empty($suggestions)): ?>
                <p class="no-suggestions">No suggestions yet. Be the first to share your ideas!</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Display Name</th>
                            <th style="width: 60%;">Suggestion</th>
                            <th style="width: 20%;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suggestions as $suggestion): ?>
                            <tr>
                                <td><?php echo escape($suggestion['fullname']); ?></td>
                                <td><?php echo escape($suggestion['suggestion']); ?></td>
                                <td><?php echo escape(date('M d, Y', strtotime($suggestion['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
</body>
</html>