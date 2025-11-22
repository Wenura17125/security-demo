<?php
require_once '../config/database.php';
session_start();

$conn = getVulnerableConnection();

// Simulate logged-in user
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'user_' . session_id();
}

$message = '';

// Handle order submission - VULNERABLE: No CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $user_session = $_SESSION['user_id'];
    
    // Process order without CSRF validation
    $query = "INSERT INTO orders (product_id, quantity, user_session) VALUES ($product_id, $quantity, '$user_session')";
    
    if ($conn->query($query)) {
        $message = "Order placed successfully!";
    } else {
        $message = "Error placing order.";
    }
}

// Fetch products
$products = $conn->query("SELECT * FROM products")->fetch_all(MYSQLI_ASSOC);

// Fetch user's orders
$user_session = $_SESSION['user_id'];
$orders = $conn->query("SELECT o.*, p.name, p.price FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_session = '$user_session' ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSRF - Vulnerable</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>CSRF - Vulnerable Version</h1>
        <div class="warning">⚠️ This page is intentionally vulnerable to CSRF attacks</div>
        
        <p>Logged in as: <strong><?php echo htmlentities($_SESSION['user_id']); ?></strong></p>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlentities($message); ?></div>
        <?php endif; ?>
        
        <h2>Available Products</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <h3><?php echo htmlentities($product['name']); ?></h3>
                    <p>Price: $<?php echo htmlentities($product['price']); ?></p>
                    <p>Stock: <?php echo htmlentities($product['stock']); ?></p>
                    
                    <!-- VULNERABLE: No CSRF token -->
                    <form method="POST" style="display:inline;">
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
        
        <div class="exploit-info">
            <h3>CSRF Attack Demonstration:</h3>
            <p>An attacker can create a malicious page with this form:</p>
            <pre><code>&lt;form action="http://yoursite.com/vulnerable/csrf.php" method="POST"&gt;
    &lt;input type="hidden" name="product_id" value="1"&gt;
    &lt;input type="hidden" name="quantity" value="100"&gt;
&lt;/form&gt;
&lt;script&gt;document.forms[0].submit();&lt;/script&gt;</code></pre>
            <p>If a logged-in user visits this page, an order will be placed without their knowledge!</p>
            
            <h3>Test Attack:</h3>
            <a href="csrf_attack.html" target="_blank" class="danger-button">Open Malicious Page (in new tab)</a>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>