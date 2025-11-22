<?php
require_once '../config/database.php';

$pdo = getSecureConnection();

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $username = $_POST['username'];
    $comment = $_POST['comment'];
    
    // SECURE: Using prepared statements
    $stmt = $pdo->prepare("INSERT INTO comments (username, comment) VALUES (:username, :comment)");
    $stmt->execute([
        ':username' => $username,
        ':comment' => $comment
    ]);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all comments
$stmt = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC");
$comments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stored XSS - Fixed</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Stored XSS - Secure Version</h1>
        <div class="success-banner">✓ This page is protected against Stored XSS using htmlentities()</div>
        
        <div class="comment-form">
            <h2>Add Comment</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Comment:</label>
                    <textarea name="comment" rows="4" required></textarea>
                </div>
                <button type="submit">Post Comment</button>
            </form>
        </div>
        
        <div class="comments-section">
            <h2>Comments</h2>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <!-- SECURE: Output with htmlentities() -->
                    <strong><?php echo htmlentities($comment['username'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <small><?php echo htmlentities($comment['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <p><?php echo htmlentities($comment['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="security-info">
            <h3>Security Measures Implemented:</h3>
            <ul>
                <li>All user input is escaped using htmlentities() before output</li>
                <li>ENT_QUOTES flag escapes both single and double quotes</li>
                <li>UTF-8 encoding specified for proper character handling</li>
                <li>Script tags are converted to harmless text</li>
            </ul>
            
            <h3>Try the previous XSS attacks:</h3>
            <p>They will be displayed as text, not executed as code!</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>