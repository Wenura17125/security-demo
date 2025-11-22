<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Security Vulnerabilities Demo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔒 Web Security Vulnerabilities Demonstration</h1>
            <p class="subtitle">Educational demonstration of common web vulnerabilities and their fixes</p>
        </header>
        
        <div class="warning-banner">
            ⚠️ <strong>Warning:</strong> These pages contain intentional security vulnerabilities for educational purposes only. 
            Never deploy this code to a production environment!
        </div>
        
        <section class="vulnerability-section">
            <h2>1. SQL Injection</h2>
            <p>SQL Injection allows attackers to interfere with database queries, potentially accessing or modifying unauthorized data.</p>
            
            <div class="demo-links">
                <div class="demo-card vulnerable">
                    <h3>Vulnerable Version</h3>
                    <p>Uses string concatenation for SQL queries</p>
                    <a href="vulnerable/sql_injection.php" class="btn btn-danger">View Vulnerable</a>
                </div>
                
                <div class="demo-card secure">
                    <h3>Secure Version</h3>
                    <p>Uses PDO with prepared statements</p>
                    <a href="secure/sql_injection_fixed.php" class="btn btn-success">View Secure</a>
                </div>
            </div>
        </section>
        
        <section class="vulnerability-section">
            <h2>2. Cross-Site Scripting (XSS)</h2>
            <p>XSS allows attackers to inject malicious scripts into web pages viewed by other users.</p>
            
            <h3>2.1 Stored XSS</h3>
            <div class="demo-links">
                <div class="demo-card vulnerable">
                    <h3>Vulnerable Version</h3>
                    <p>Stores and displays unsanitized user input</p>
                    <a href="vulnerable/xss_stored.php" class="btn btn-danger">View Vulnerable</a>
                </div>
                
                <div class="demo-card secure">
                    <h3>Secure Version</h3>
                    <p>Uses htmlentities() for output escaping</p>
                    <a href="secure/xss_stored_fixed.php" class="btn btn-success">View Secure</a>
                </div>
            </div>
            
            <h3>2.2 Reflected XSS</h3>
            <div class="demo-links">
                <div class="demo-card vulnerable">
                    <h3>Vulnerable Version</h3>
                    <p>Reflects user input without sanitization</p>
                    <a href="vulnerable/xss_reflected.php" class="btn btn-danger">View Vulnerable</a>
                </div>
                
                <div class="demo-card secure">
                    <h3>Secure Version</h3>
                    <p>Escapes all reflected user input</p>
                    <a href="secure/xss_reflected_fixed.php" class="btn btn-success">View Secure</a>
                </div>
            </div>
            
            <h3>2.3 DOM-based XSS</h3>
            <div class="demo-links">
                <div class="demo-card vulnerable">
                    <h3>Vulnerable Version</h3>
                    <p>Uses innerHTML with user-controlled data</p>
                    <a href="vulnerable/xss_dom.php" class="btn btn-danger">View Vulnerable</a>
                </div>
                
                <div class="demo-card secure">
                    <h3>Secure Version</h3>
                    <p>Uses textContent and safe DOM methods</p>
                    <a href="secure/xss_dom_fixed.php" class="btn btn-success">View Secure</a>
                </div>
            </div>
        </section>
        
        <section class="vulnerability-section">
            <h2>3. Cross-Site Request Forgery (CSRF)</h2>
            <p>CSRF tricks users into performing unwanted actions on a web application where they're authenticated.</p>
            
            <div class="demo-links">
                <div class="demo-card vulnerable">
                    <h3>Vulnerable Version</h3>
                    <p>No CSRF protection on state-changing operations</p>
                    <a href="vulnerable/csrf.php" class="btn btn-danger">View Vulnerable</a>
                </div>
                
                <div class="demo-card secure">
                    <h3>Secure Version</h3>
                    <p>Implements Synchronizer Token Pattern</p>
                    <a href="secure/csrf_fixed.php" class="btn btn-success">View Secure</a>
                </div>
            </div>
        </section>
        
        <section class="info-section">
            <h2>📚 Learning Resources</h2>
            <ul>
                <li><a href="https://www.php.net/manual/book.pdo" target="_blank">PHP PDO Documentation</a></li>
                <li><a href="https://www.php.net/manual/pdo.prepared-statements" target="_blank">PDO Prepared Statements</a></li>
                <li><a href="https://www.php.net/manual/function.htmlentities" target="_blank">htmlentities() Function</a></li>
                <li><a href="https://owasp.org/www-community/attacks/csrf" target="_blank">OWASP CSRF Guide</a></li>
                <li><a href="https://owasp.org/www-project-top-ten/" target="_blank">OWASP Top 10</a></li>
            </ul>
        </section>
        
        <footer>
            <p>Created for educational purposes | <strong>DO NOT use in production!</strong></p>
        </footer>
    </div>
</body>
</html>