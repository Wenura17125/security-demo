<?php
require_once '../config/database.php';
session_start();

$conn = getVulnerableConnection();
$error = '';
$user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // VULNERABLE: Direct string concatenation
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SQL Injection - Vulnerable</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>SQL Injection - Vulnerable Version</h1>
        <div class="warning">⚠️ This page is intentionally vulnerable to SQL Injection</div>
        
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
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($user): ?>
            <div class="success">
                <h3>Login Successful!</h3>
                <p>Welcome, <?php echo $user['username']; ?></p>
                <p>Email: <?php echo $user['email']; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="exploit-info">
            <h3>Try this SQL Injection:</h3>
            <p><strong>Username:</strong> admin' OR '1'='1</p>
            <p><strong>Password:</strong> anything</p>
            <p>This will bypass authentication!</p>
            
            <h3>Or try:</h3>
            <p><strong>Username:</strong> admin' --</p>
            <p><strong>Password:</strong> anything</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>