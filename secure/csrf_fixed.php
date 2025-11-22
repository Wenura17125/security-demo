<?php
require_once '../config/database.php';
require_once '../includes/csrf_token.php';
session_start();

$pdo = getSecureConnection();

// Simulate logged-in user
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'user_' . session_id();
}

$message = '';
$error = '';

// Handle order submission - SECURE: With CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    
    // SECURE: Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $error = "Invalid CSRF token. Possible CSRF attack detected!";
    } else {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $user_session = $_SESSION['user_id'];
        
        // Process order with prepared statement
        $stmt = $pdo->prepare("INSERT INTO orders (product_id, quantity, user_session) VALUES (:product_id, :quantity, :user_session)");
        
        if ($stmt->execute([
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':user_session' => $user_session
        ])) {
            $message = "Order placed successfully!";
            // Regenerate token after use (optional, for extra security)
            unset($_SESSION['csrf_token']);
        } else {
            $error = "Error placing order.";
        }
    }
}

// Fetch products
$products = $pdo->query("SELECT * FROM products")->fetchAll();

// Fetch user's orders
$stmt = $pdo->prepare("SELECT o.*, p.name, p.price FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_session = :user_session ORDER BY o.created_at DESC");
$stmt->execute([':user_session' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSRF - Fixed</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>CSRF - Secure Version</h1>
        <div class="success-banner">✓ This page is protected against CSRF using Synchronizer Token Pattern</div>
        
        <p>Logged in as: <strong><?php echo htmlentities($_SESSION['user_id']); ?></strong></p>
        
        <?php if ($message): ?>
            <div class="success"><?php echo htmlentities($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlentities($error); ?></div>
        <?php endif; ?>
        
        <h2>Available Products</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <h3><?php echo htmlentities($product['name']); ?></h3>
                    <p>Price: $<?php echo htmlentities($product['price']); ?></p>
                    <p>Stock: <?php echo htmlentities($product['stock']); ?></p>
                    
                    <!-- SECURE: Includes CSRF token -->
                    <form method="POST" style="display:inline;">
                        <?php echo getCsrfTokenField(); ?>
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        <button type="submit">Order Now</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        
        <h2>Your Orders</h2>
        <table class="orders-table">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlentities($order['name']); ?></td>
                    <td><?php echo htmlentities($order['quantity']); ?></td>
                    <td>$<?php echo number_format($order['price'] * $order['quantity'], 2); ?></td>
                    <td><?php echo htmlentities($order['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <div class="security-info">
            <h3>Security Measures Implemented:</h3>
            <ul>
                <li><strong>Synchronizer Token Pattern:</strong> Unique token generated per session</li>
                <li>Token validated on every state-changing request</li>
                <li>Token stored securely in session</li>
                <li>Uses hash_equals() to prevent timing attacks</li>
                <li>Token regenerated after use (optional)</li>
            </ul>
            
            <h3>How it works:</h3>
            <ol>
                <li>Server generates random token and stores in session</li>
                <li>Token included as hidden field in all forms</li>
                <li>On submission, server validates token matches session</li>
                <li>Attacker cannot guess or obtain the token</li>
            </ol>
            
            <p><strong>Try the CSRF attack now:</strong> It will be blocked!</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>