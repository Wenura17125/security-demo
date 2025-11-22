<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DOM-based XSS - Fixed</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>DOM-based XSS - Secure Version</h1>
        <div class="success-banner">✓ This page is protected against DOM-based XSS</div>
        
        <div class="form-group">
            <label>Enter your name:</label>
            <input type="text" id="nameInput" placeholder="Your name">
            <button onclick="greetUser()">Greet Me</button>
        </div>
        
        <div id="greeting" class="greeting-box"></div>
        
        <div class="security-info">
            <h3>Security Measures Implemented:</h3>
            <ul>
                <li>Using textContent instead of innerHTML</li>
                <li>Creating DOM elements programmatically</li>
                <li>Sanitizing user input before DOM manipulation</li>
                <li>Avoiding direct use of URL fragments in innerHTML</li>
            </ul>
            
            <h3>Try the previous DOM XSS attacks:</h3>
            <p>They will be safely displayed as text without execution!</p>
        </div>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
    
    <script>
        // SECURE: Using textContent and DOM methods
        function greetUser() {
            var name = document.getElementById('nameInput').value;
            var greeting = document.getElementById('greeting');
            
            // Clear previous content
            greeting.innerHTML = '';
            
            // Create elements programmatically
            var heading = document.createElement('h2');
            heading.textContent = 'Hello, ' + name + '!'; // textContent escapes HTML
            
            greeting.appendChild(heading);
        }
        
        // SECURE: Sanitizing URL hash
        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        window.onload = function() {
            if (window.location.hash) {
                var hash = decodeURIComponent(window.location.hash.substring(1));
                var greeting = document.getElementById('greeting');
                
                // Using textContent instead of innerHTML
                var p = document.createElement('p');
                p.textContent = 'From URL: ' + hash;
                greeting.appendChild(p);
            }
        };
    </script>
</body>
</html>