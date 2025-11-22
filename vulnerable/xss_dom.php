<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DOM-based XSS - Vulnerable</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>DOM-based XSS - Vulnerable Version</h1>
        <div class="warning">⚠️ This page is intentionally vulnerable to DOM-based XSS</div>
        
        <div class="form-group">
            <label>Enter your name:</label>
            <input type="text" id="nameInput" placeholder="Your name">
            <button onclick="greetUser()">Greet Me</button>
        </div>
        
        <div id="greeting" class="greeting-box"></div>
        
        <div class="exploit-info">
            <h3>Try this DOM-based XSS Attack:</h3>
            <p>Enter in the name field:</p>
            <code>&lt;img src=x onerror="alert('DOM XSS')"&gt;</code>
            <p>Or try:</p>
            <code>&lt;script&gt;alert('DOM-based XSS!')&lt;/script&gt;</code>
            <p>The script executes through DOM manipulation!</p>
            
            <h3>URL-based attack:</h3>
            <p>Try adding to URL: <code>#&lt;img src=x onerror="alert('XSS')"&gt;</code></p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
    
    <script>
        // VULNERABLE: Using innerHTML with user input
        function greetUser() {
            var name = document.getElementById('nameInput').value;
            var greeting = document.getElementById('greeting');
            greeting.innerHTML = '<h2>Hello, ' + name + '!</h2>';
        }
        
        // VULNERABLE: Reading from URL hash without sanitization
        window.onload = function() {
            if (window.location.hash) {
                var hash = decodeURIComponent(window.location.hash.substring(1));
                document.getElementById('greeting').innerHTML = hash;
            }
        };
    </script>
</body>
</html>