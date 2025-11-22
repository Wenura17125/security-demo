<?php
require_once '../config/database.php';

$conn = getVulnerableConnection();

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $comment = $conn->real_escape_string($_POST['comment']);
    
    // VULNERABLE: Storing unsanitized input (XSS vulnerability - intentional for demo)
    // However, we escape SQL special characters to prevent SQL injection
    $query = "INSERT INTO comments (username, comment) VALUES ('$username', '$comment')";
    $conn->query($query);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all comments
$result = $conn->query("SELECT * FROM comments ORDER BY created_at DESC");
$comments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stored XSS - Vulnerable</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Stored XSS - Vulnerable Version</h1>
        <div class="warning">⚠️ This page is intentionally vulnerable to Stored XSS</div>
        
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
                    <strong><?php echo $comment['username']; ?></strong>
                    <small><?php echo $comment['created_at']; ?></small>
                    <!-- VULNERABLE: Output without escaping -->
                    <p><?php echo $comment['comment']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="exploit-info">
            <h3>Try this XSS Attack:</h3>
            <p><strong>Comment:</strong> &lt;script&gt;alert('XSS Attack!')&lt;/script&gt;</p>
            <p>Or try: &lt;img src=x onerror="alert('Stored XSS')"&gt;</p>
            <p>The script will execute every time the page loads!</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>