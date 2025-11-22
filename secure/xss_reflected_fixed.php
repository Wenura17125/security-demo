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
    <title>Reflected XSS - Fixed</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Reflected XSS - Secure Version</h1>
        <div class="success-banner">✓ This page is protected against Reflected XSS</div>
        
        <form method="GET">
            <div class="form-group">
                <label>Search Products:</label>
                <!-- SECURE: Escaping output in form value -->
                <input type="text" name="search" value="<?php echo htmlentities($search, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">Search</button>
            </div>
        </form>
        
        <?php if ($search): ?>
            <div class="search-results">
                <!-- SECURE: Escaping reflected user input -->
                <h3>Search results for: <?php echo htmlentities($search, ENT_QUOTES, 'UTF-8'); ?></h3>
                
                <?php if (count($results) > 0): ?>
                    <ul>
                        <?php foreach ($results as $result): ?>
                            <li><?php echo htmlentities($result, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No results found for "<?php echo htmlentities($search, ENT_QUOTES, 'UTF-8'); ?>"</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="security-info">
            <h3>Security Measures Implemented:</h3>
            <ul>
                <li>All reflected user input is escaped with htmlentities()</li>
                <li>Both in form values and display output</li>
                <li>ENT_QUOTES prevents attribute-based XSS</li>
                <li>Malicious scripts are displayed as harmless text</li>
            </ul>
            
            <h3>Try the previous XSS attacks:</h3>
            <p>They will be safely displayed as text!</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>