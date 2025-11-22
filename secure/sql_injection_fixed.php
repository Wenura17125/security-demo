<?php
require_once '../config/database.php';
session_start();

$pdo = getSecureConnection();
$error = '';
$user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // SECURE: Using prepared statements with PDO
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute([
        ':username' => $username,
        ':password' => $password
    ]);
    
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SQL Injection - Fixed</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>SQL Injection - Secure Version</h1>
        <div class="success-banner">✓ This page is protected against SQL Injection using PDO Prepared Statements</div>
        
        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlentities($error); ?></div>
        <?php endif; ?>
        
        <?php if ($user): ?>
            <div class="success">
                <h3>Login Successful!</h3>
                <p>Welcome, <?php echo htmlentities($user['username']); ?></p>
                <p>Email: <?php echo htmlentities($user['email']); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="security-info">
            <h3>Security Measures Implemented:</h3>
            <ul>
                <li>Using PDO with prepared statements</li>
                <li>Parameter binding prevents SQL injection</li>
                <li>User input is never directly concatenated into queries</li>
                <li>Additional output escaping with htmlentities()</li>
            </ul>
            
            <h3>Try the previous SQL Injection attacks:</h3>
            <p>They will no longer work! The input is treated as literal data.</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>