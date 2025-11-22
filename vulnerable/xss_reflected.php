<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$results = [];

if ($search) {
    // Simulate search results
    $all_items = ['Laptop', 'Mouse', 'Keyboard', 'Monitor', 'Headphones'];
    foreach ($all_items as $item) {
        if (stripos($item, $search) !== false) {
            $results[] = $item;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reflected XSS - Vulnerable</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Reflected XSS - Vulnerable Version</h1>
        <div class="warning">⚠️ This page is intentionally vulnerable to Reflected XSS</div>
        
        <form method="GET">
            <div class="form-group">
                <label>Search Products:</label>
                <input type="text" name="search" value="<?php echo $search; ?>">
                <button type="submit">Search</button>
            </div>
        </form>
        
        <?php if ($search): ?>
            <!-- VULNERABLE: Reflecting user input without escaping -->
            <div class="search-results">
                <h3>Search results for: <?php echo $search; ?></h3>
                
                <?php if (count($results) > 0): ?>
                    <ul>
                        <?php foreach ($results as $result): ?>
                            <li><?php echo $result; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No results found for "<?php echo $search; ?>"</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="exploit-info">
            <h3>Try this Reflected XSS Attack:</h3>
            <p>Click this link or enter in search box:</p>
            <a href="?search=<script>alert('Reflected XSS!')</script>">
                Malicious Link
            </a>
            <p>Or try: &lt;img src=x onerror="alert('XSS')"&gt;</p>
            <p>The script executes immediately from the URL!</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>