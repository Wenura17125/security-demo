# Web Security Vulnerabilities Demonstration

A comprehensive educational project demonstrating common web security vulnerabilities and their proper fixes.

## Table of Contents
1. [Installation Instructions](#installation-instructions)
2. [Project Structure](#project-structure)
3. [Detailed Vulnerability Testing Guide](#detailed-vulnerability-testing-guide)
   - [SQL Injection](#1-sql-injection)
   - [Cross-Site Scripting (XSS)](#2-cross-site-scripting-xss)
     - [Stored XSS](#21-stored-xss)
     - [Reflected XSS](#22-reflected-xss)
     - [DOM-based XSS](#23-dom-based-xss)
   - [Cross-Site Request Forgery (CSRF)](#3-cross-site-request-forgery-csrf)
4. [Security Best Practices](#security-best-practices)
5. [References](#references)

---

## Installation Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.3 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (recommended for local development)

### Setup Steps

1. **Clone or Download the Repository**
   ```bash
   git clone https://github.com/Wenura17125/security-demo.git
   cd security-demo
   ```

2. **Database Setup**
   - Create a MySQL database named `security_demo`
   - Import the SQL file:
   ```bash
   mysql -u root -p security_demo < setup/database_setup.sql
   ```
   
   Or use phpMyAdmin:
   - Open phpMyAdmin
   - Create database: `security_demo`
   - Import: `setup/database_setup.sql`

3. **Configure Database Connection**
   - Edit `config/database.php`
   - Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'security_demo');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Add your password if any
   ```

4. **Start Web Server**
   - If using XAMPP, place the project in `htdocs/security-demo`
   - Start Apache and MySQL services
   - Access via: `http://localhost/security-demo`

5. **Test the Application**
   - Navigate to `http://localhost/security-demo/index.php`
   - You should see the main page with links to all demonstrations

---

## Project Structure

```
security-demo/
├── config/
│   └── database.php          # Database configuration
├── vulnerable/                # Vulnerable implementations
│   ├── sql_injection.php
│   ├── xss_stored.php
│   ├── xss_reflected.php
│   ├── xss_dom.php
│   ├── csrf.php
│   └── csrf_attack.html
├── secure/                    # Secure implementations
│   ├── sql_injection_fixed.php
│   ├── xss_stored_fixed.php
│   ├── xss_reflected_fixed.php
│   ├── xss_dom_fixed.php
│   └── csrf_fixed.php
├── includes/
│   └── csrf_token.php        # CSRF token helper functions
├── setup/
│   └── database_setup.sql    # Database schema and sample data
├── index.php                  # Main landing page
├── style.css                  # Stylesheet
└── README.md                  # This file
```

---

## Detailed Vulnerability Testing Guide

## 1. SQL Injection

### 1.1 What is SQL Injection?

SQL Injection is a code injection technique that attackers use to insert malicious SQL statements into input fields for execution. This can lead to unauthorized access to sensitive data, data modification, or even complete database compromise.

### 1.2 Understanding the Vulnerability

**Location:** `vulnerable/sql_injection.php`

**Vulnerable Code:**
```php
// BAD: Direct string concatenation
$username = $conn->real_escape_string($_POST['username']);
$password = $conn->real_escape_string($_POST['password']);
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $conn->query($query);
```

**Why it's vulnerable:** Even with `real_escape_string()`, the query structure can be manipulated because the input is still concatenated into the SQL string, allowing attackers to alter the query logic.

### 1.3 How to Test SQL Injection

**Step-by-Step Testing:**

1. **Navigate to the vulnerable page:**
   ```
   http://localhost/security-demo/vulnerable/sql_injection.php
   ```

2. **Test Case 1: Authentication Bypass using OR condition**
   - **Username:** `admin' OR '1'='1`
   - **Password:** `anything`
   - **Click:** Login
   
   **What happens:**
   - The SQL query becomes: `SELECT * FROM users WHERE username = 'admin' OR '1'='1' AND password = 'anything'`
   - The `'1'='1'` is always true, bypassing authentication
   - You'll be logged in as the first user in the database

3. **Test Case 2: Authentication Bypass using SQL comments**
   - **Username:** `admin' --`
   - **Password:** `anything`
   - **Click:** Login
   
   **What happens:**
   - The `--` comments out the rest of the query
   - Final query: `SELECT * FROM users WHERE username = 'admin' --' AND password = 'anything'`
   - The password check is completely ignored

4. **Test Case 3: UNION-based SQL Injection**
   - **Username:** `admin' UNION SELECT 1,2,3,4,5 --`
   - **Password:** `anything`
   
   **What happens:**
   - Attempts to combine results from two queries
   - Can be used to extract data from other tables

### 1.4 How to Fix SQL Injection

**Location:** `secure/sql_injection_fixed.php`

**Secure Code:**
```php
// GOOD: Using PDO with prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
$stmt->execute([
    ':username' => $username,
    ':password' => $password
]);
$user = $stmt->fetch();
```

**Why it's secure:**
- Prepared statements separate SQL code from data
- Parameters are bound separately and treated as data only
- The database engine knows user input can never be SQL code
- No amount of malicious input can alter the query structure

### 1.5 Test the Fix

1. **Navigate to the secure page:**
   ```
   http://localhost/security-demo/secure/sql_injection_fixed.php
   ```

2. **Try the same attacks:**
   - **Username:** `admin' OR '1'='1`
   - **Password:** `anything`
   
   **Result:** Attack fails! You'll see "Invalid credentials"

**Why the attack fails:**
- The entire input `admin' OR '1'='1` is treated as a literal username
- The database looks for a user with that exact username (which doesn't exist)
- The SQL structure remains unchanged

---

## 2. Cross-Site Scripting (XSS)

### What is XSS?

Cross-Site Scripting (XSS) allows attackers to inject malicious scripts into web pages viewed by other users. These scripts can steal cookies, session tokens, or other sensitive information, and can even rewrite the content of the HTML page.

---

## 2.1 Stored XSS

### 2.1.1 Understanding Stored XSS

**Location:** `vulnerable/xss_stored.php`

**What is it?**
- Malicious script is permanently stored in the database
- Every time the page loads, the script executes for all users
- Most dangerous type of XSS

**Vulnerable Code:**
```php
// STORING (uses real_escape_string to prevent SQL errors)
$username = $conn->real_escape_string($_POST['username']);
$comment = $conn->real_escape_string($_POST['comment']);
$query = "INSERT INTO comments (username, comment) VALUES ('$username', '$comment')";
$conn->query($query);

// DISPLAYING - VULNERABLE!
echo $comment['comment']; // No escaping!
```

### 2.1.2 How to Test Stored XSS

**Step-by-Step Testing:**

1. **Navigate to the vulnerable page:**
   ```
   http://localhost/security-demo/vulnerable/xss_stored.php
   ```

2. **Test Case 1: Basic Alert Script**
   - **Username:** `TestUser`
   - **Comment:** `<script>alert('XSS Attack!')</script>`
   - **Click:** Post Comment
   
   **What happens:**
   - The script is stored in the database
   - When the page reloads, the alert box appears
   - The script executes every time anyone views the page

3. **Test Case 2: Image Tag with Error Handler**
   - **Username:** `Attacker`
   - **Comment:** `<img src=x onerror="alert('Stored XSS')">`
   
   **What happens:**
   - Image fails to load (src=x is invalid)
   - The onerror handler executes the JavaScript
   - Alert box appears

4. **Test Case 3: Cookie Theft Simulation**
   - **Username:** `Hacker`
   - **Comment:** `<script>alert('Your cookies: ' + document.cookie)</script>`
   
   **What happens:**
   - Shows all cookies accessible to JavaScript
   - In a real attack, this would be sent to the attacker's server

5. **Test Case 4: HTML Injection**
   - **Username:** `User`
   - **Comment:** `<h1 style="color:red">I can change the page!</h1>`
   
   **What happens:**
   - HTML is rendered, changing the page appearance
   - Demonstrates that any HTML/JavaScript can be injected

### 2.1.3 How to Fix Stored XSS

**Location:** `secure/xss_stored_fixed.php`

**Secure Code:**
```php
// STORING (using prepared statements)
$stmt = $pdo->prepare("INSERT INTO comments (username, comment) VALUES (:username, :comment)");
$stmt->execute([
    ':username' => $username,
    ':comment' => $comment
]);

// DISPLAYING - SECURE!
echo htmlentities($comment['comment'], ENT_QUOTES, 'UTF-8');
```

**What `htmlentities()` does:**
- Converts special characters to HTML entities
- `<` becomes `&lt;`
- `>` becomes `&gt;`
- `"` becomes `&quot;`
- `'` becomes `&#039;`

**Before escaping:**
```html
<script>alert('XSS')</script>
```

**After escaping:**
```html
&lt;script&gt;alert('XSS')&lt;/script&gt;
```

### 2.1.4 Test the Fix

1. **Navigate to the secure page:**
   ```
   http://localhost/security-demo/secure/xss_stored_fixed.php
   ```

2. **Try the same attacks:**
   - Post: `<script>alert('XSS Attack!')</script>`
   
   **Result:** The script appears as plain text, not executed!

**Why the attack fails:**
- `htmlentities()` converts `<script>` to `&lt;script&gt;`
- The browser displays it as text instead of executing it
- The malicious code is neutralized

---

## 2.2 Reflected XSS

### 2.2.1 Understanding Reflected XSS

**Location:** `vulnerable/xss_reflected.php`

**What is it?**
- Malicious script is reflected from the server in the response
- Not stored in database, but in URL or form input
- Requires user to click a malicious link

**Vulnerable Code:**
```php
$search = isset($_GET['search']) ? $_GET['search'] : '';

// VULNERABLE: Reflecting user input without escaping
echo "Search results for: " . $search;
echo "No results found for \"" . $search . "\"";
```

### 2.2.2 How to Test Reflected XSS

**Step-by-Step Testing:**

1. **Navigate to the vulnerable page:**
   ```
   http://localhost/security-demo/vulnerable/xss_reflected.php
   ```

2. **Test Case 1: Search Box Attack**
   - **In search box, enter:** `<script>alert('Reflected XSS')</script>`
   - **Click:** Search
   
   **What happens:**
   - The script is reflected in the page
   - Alert box appears immediately

3. **Test Case 2: URL Parameter Attack**
   - **Navigate to:**
   ```
   http://localhost/security-demo/vulnerable/xss_reflected.php?search=<script>alert('XSS')</script>
   ```
   
   **What happens:**
   - Same as Test Case 1
   - Demonstrates how attackers send malicious links

4. **Test Case 3: Image Tag Attack**
   - **Search for:** `<img src=x onerror="alert('Reflected XSS')">`
   
   **What happens:**
   - Alternative XSS payload
   - Works even if `<script>` is filtered

5. **Test Case 4: URL Encoded Attack**
   - **Navigate to:**
   ```
   http://localhost/security-demo/vulnerable/xss_reflected.php?search=%3Cscript%3Ealert%28%27XSS%27%29%3C%2Fscript%3E
   ```
   
   **What happens:**
   - URL encoded version of `<script>alert('XSS')</script>`
   - Still executes because server decodes it

### 2.2.3 How to Fix Reflected XSS

**Location:** `secure/xss_reflected_fixed.php`

**Secure Code:**
```php
$search = isset($_GET['search']) ? $_GET['search'] : '';

// SECURE: Escaping output
echo "Search results for: " . htmlentities($search, ENT_QUOTES, 'UTF-8');
echo "No results found for \"" . htmlentities($search, ENT_QUOTES, 'UTF-8') . "\"";
```

**Flags explanation:**
- `ENT_QUOTES`: Converts both single and double quotes
- `'UTF-8'`: Specifies character encoding to prevent encoding attacks

### 2.2.4 Test the Fix

1. **Navigate to the secure page:**
   ```
   http://localhost/security-demo/secure/xss_reflected_fixed.php
   ```

2. **Try the same attacks:**
   - Search: `<script>alert('XSS')</script>`
   
   **Result:** Script appears as text, safely displayed!

---

## 2.3 DOM-based XSS

### 2.3.1 Understanding DOM-based XSS

**Location:** `vulnerable/xss_dom.php`

**What is it?**
- The vulnerability is in the client-side JavaScript code
- Server never sees the malicious payload
- JavaScript reads from unsafe sources and writes to dangerous sinks

**Vulnerable Code:**
```javascript
// VULNERABLE: Using innerHTML with user input
function greetUser() {
    var name = document.getElementById('nameInput').value;
    var greeting = document.getElementById('greeting');
    greeting.innerHTML = '<h2>Hello, ' + name + '!</h2>';
}

// VULNERABLE: Reading from URL hash
window.onload = function() {
    if (window.location.hash) {
        var hash = decodeURIComponent(window.location.hash.substring(1));
        document.getElementById('greeting').innerHTML = hash;
    }
};
```

**Unsafe Sources:**
- `window.location.hash`
- `window.location.search`
- `document.referrer`
- User input fields

**Dangerous Sinks:**
- `innerHTML`
- `document.write()`
- `eval()`

### 2.3.2 How to Test DOM-based XSS

**Step-by-Step Testing:**

1. **Navigate to the vulnerable page:**
   ```
   http://localhost/security-demo/vulnerable/xss_dom.php
   ```

2. **Test Case 1: Input Field Attack**
   - **In name field, enter:** `<img src=x onerror="alert('DOM XSS')">`
   - **Click:** Greet Me
   
   **What happens:**
   - JavaScript sets innerHTML with user input
   - Image tag's onerror executes
   - Alert appears

3. **Test Case 2: URL Hash Attack**
   - **Navigate to:**
   ```
   http://localhost/security-demo/vulnerable/xss_dom.php#<script>alert('Hash XSS')</script>
   ```
   
   **What happens:**
   - Page load reads from window.location.hash
   - Script is inserted via innerHTML
   - Alert executes immediately

4. **Test Case 3: Input with Script Tag**
   - **In name field, enter:** `<script>alert('DOM-based XSS!')</script>`
   - **Click:** Greet Me
   
   **What happens:**
   - innerHTML executes the script

5. **Test Case 4: Event Handler Attack**
   - **In name field, enter:** `<div onmouseover="alert('XSS')">Hover me</div>`
   - **Click:** Greet Me
   - **Hover over** the greeting
   
   **What happens:**
   - Moving mouse over the text triggers the alert

### 2.3.3 How to Fix DOM-based XSS

**Location:** `secure/xss_dom_fixed.php`

**Secure Code:**
```javascript
// SECURE: Using textContent instead of innerHTML
function greetUser() {
    var name = document.getElementById('nameInput').value;
    var greeting = document.getElementById('greeting');
    
    // Create elements safely
    var h2 = document.createElement('h2');
    h2.textContent = 'Hello, ' + name + '!';
    
    // Clear and append
    greeting.innerHTML = '';
    greeting.appendChild(h2);
}

// SECURE: Sanitizing hash input
window.onload = function() {
    if (window.location.hash) {
        var hash = decodeURIComponent(window.location.hash.substring(1));
        var greetingDiv = document.getElementById('greeting');
        
        // Use textContent for safe text insertion
        greetingDiv.textContent = hash;
    }
};
```

**Why it's secure:**
- `textContent` treats everything as plain text
- `createElement()` + `appendChild()` creates DOM nodes safely
- No HTML parsing of user input occurs

### 2.3.4 Test the Fix

1. **Navigate to the secure page:**
   ```
   http://localhost/security-demo/secure/xss_dom_fixed.php
   ```

2. **Try the same attacks:**
   - Input: `<img src=x onerror="alert('DOM XSS')">`
   - URL: `#<script>alert('Hash XSS')</script>`
   
   **Result:** All attacks fail! Malicious code appears as plain text.

---

## 3. Cross-Site Request Forgery (CSRF)

### 3.1 Understanding CSRF

**Location:** `vulnerable/csrf.php`

**What is CSRF?**
- Forces authenticated users to perform unwanted actions
- Exploits the trust a website has in the user's browser
- Uses the user's active session to perform malicious requests

**Vulnerable Code:**
```php
// VULNERABLE: No token validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    // Process order without verification
}
```

**Vulnerable Form:**
```html
<!-- No CSRF token -->
<form method="POST">
    <input type="hidden" name="product_id" value="1">
    <input type="number" name="quantity" value="1">
    <button type="submit">Order Now</button>
</form>
```

### 3.2 How to Test CSRF

**Step-by-Step Testing:**

1. **Navigate to the vulnerable page:**
   ```
   http://localhost/security-demo/vulnerable/csrf.php
   ```

2. **Establish a session:**
   - Note your user ID shown on the page
   - Place a legitimate order by clicking "Order Now"
   - Check "Your Orders" section to see it works

3. **Test Case 1: Manually Created Malicious Page**
   
   Create a file named `test_csrf_attack.html` on your desktop:
   ```html
   <!DOCTYPE html>
   <html>
   <head>
       <title>Win a Free iPhone!</title>
   </head>
   <body>
       <h1>Click here to win a free iPhone!</h1>
       <form id="malicious" action="http://localhost/security-demo/vulnerable/csrf.php" method="POST">
           <input type="hidden" name="product_id" value="1">
           <input type="hidden" name="quantity" value="100">
       </form>
       <script>
           // Auto-submit the form
           document.getElementById('malicious').submit();
       </script>
   </body>
   </html>
   ```
   
   - Open this file in your browser
   - Return to the vulnerable CSRF page
   - Refresh and check "Your Orders"
   
   **What happens:**
   - An order for 100 items is placed without your consent!
   - The attack succeeds because the browser includes your session cookie

4. **Test Case 2: Using the Provided Attack Page**
   - Make sure you're on the vulnerable CSRF page
   - Click the "Open Malicious Page (in new tab)" button
   - Return to the original tab and refresh
   
   **What happens:**
   - Unauthorized order appears in your orders list
   - This simulates a real CSRF attack

5. **Test Case 3: Image-based CSRF**
   
   Add this to any website you control or in browser console on the vulnerable page:
   ```html
   <img src="http://localhost/security-demo/vulnerable/csrf.php?product_id=2&quantity=50" style="display:none">
   ```
   
   **Note:** This would work if the endpoint accepted GET requests

### 3.3 Understanding the Attack

**Attack Flow:**
1. Victim is logged into `http://localhost/security-demo`
2. Victim visits attacker's website (or receives malicious email)
3. Attacker's page contains hidden form with malicious request
4. Form auto-submits to victim's legitimate site
5. Browser includes victim's session cookie automatically
6. Server processes the request as if victim made it

**Why it works:**
- Browsers automatically include cookies with requests
- Server has no way to verify if user intended the action
- No secret token to verify legitimacy

### 3.4 How to Fix CSRF

**Location:** `secure/csrf_fixed.php` and `includes/csrf_token.php`

**Secure Code - Token Generation:**
```php
// includes/csrf_token.php
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function getCsrfTokenField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlentities($token, ENT_QUOTES, 'UTF-8') . '">';
}
```

**Secure Code - Token Validation:**
```php
// Validate token before processing
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    $error = "Invalid CSRF token. Possible CSRF attack detected!";
} else {
    // Process order
}
```

**Secure Form:**
```html
<form method="POST">
    <?php echo getCsrfTokenField(); ?>
    <input type="hidden" name="product_id" value="1">
    <input type="number" name="quantity" value="1">
    <button type="submit">Order Now</button>
</form>
```

**Why it's secure:**
- **Synchronizer Token Pattern**: Unique, unpredictable token per session
- **Token in Session**: Server stores token securely
- **Token in Form**: Each form includes the token
- **Validation**: Server verifies token matches session
- **hash_equals()**: Prevents timing attacks

### 3.5 Test the Fix

1. **Navigate to the secure page:**
   ```
   http://localhost/security-demo/secure/csrf_fixed.php
   ```

2. **Test legitimate use:**
   - Place a normal order
   - Should work perfectly

3. **Test CSRF attack:**
   - Create a malicious page targeting the secure endpoint:
   ```html
   <form action="http://localhost/security-demo/secure/csrf_fixed.php" method="POST">
       <input type="hidden" name="product_id" value="1">
       <input type="hidden" name="quantity" value="100">
   </form>
   <script>document.forms[0].submit();</script>
   ```
   
   **Result:** Attack fails! Error message: "Invalid CSRF token. Possible CSRF attack detected!"

**Why the attack fails:**
- Attacker cannot know or guess the CSRF token
- Token is unique per session and cryptographically random
- Form submission without valid token is rejected
- Even if attacker copies form HTML, they can't get the token value

---

## Security Best Practices

### 1. SQL Injection Prevention
- ✅ **Always use prepared statements** (PDO or MySQLi)
- ✅ **Never concatenate user input into SQL**
- ✅ Use parameterized queries for all database operations
- ✅ Implement principle of least privilege for database users
- ✅ Validate and sanitize input (defense in depth)

### 2. XSS Prevention
- ✅ **Escape all output:** Use `htmlentities()` or `htmlspecialchars()`
- ✅ **Use ENT_QUOTES flag** to escape both single and double quotes
- ✅ **Specify encoding:** Always use UTF-8
- ✅ **For JavaScript:** Use `textContent` instead of `innerHTML`
- ✅ **Content Security Policy (CSP):** Add CSP headers
- ✅ **HTTPOnly cookies:** Prevent JavaScript access to sensitive cookies

**CSP Header Example:**
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
```

### 3. CSRF Prevention
- ✅ **Use CSRF tokens** for all state-changing operations
- ✅ **Validate tokens** using `hash_equals()` to prevent timing attacks
- ✅ **Regenerate tokens** after important actions
- ✅ **SameSite cookies:** Set SameSite attribute on cookies
- ✅ **Double-submit cookies:** Alternative CSRF protection pattern

**SameSite Cookie Example:**
```php
session_set_cookie_params([
    'samesite' => 'Strict',
    'secure' => true,
    'httponly' => true
]);
```

### 4. General Security Practices
- ✅ Keep software updated (PHP, MySQL, frameworks)
- ✅ Use HTTPS in production
- ✅ Implement proper error handling (don't expose system details)
- ✅ Use security headers (X-Frame-Options, X-Content-Type-Options)
- ✅ Regular security audits
- ✅ Input validation on both client and server side
- ✅ Principle of least privilege
- ✅ Security logging and monitoring

---

## Common Testing Payloads

### XSS Payloads
```html
<script>alert('XSS')</script>
<img src=x onerror="alert('XSS')">
<svg onload="alert('XSS')">
<iframe src="javascript:alert('XSS')">
<body onload="alert('XSS')">
<input onfocus="alert('XSS')" autofocus>
<marquee onstart="alert('XSS')">
<details open ontoggle="alert('XSS')">
```

### SQL Injection Payloads
```sql
' OR '1'='1
' OR 1=1 --
admin' --
' UNION SELECT NULL--
' AND 1=2 UNION SELECT 1,2,3--
'; DROP TABLE users--
```

---

## References

### Official Documentation
- [PHP PDO Documentation](https://www.php.net/manual/book.pdo.php)
- [PDO Prepared Statements](https://www.php.net/manual/pdo.prepared-statements.php)
- [htmlentities() Function](https://www.php.net/manual/function.htmlentities.php)
- [PHP Security](https://www.php.net/manual/security.php)

### Security Organizations
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [OWASP SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- [OWASP CSRF Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)

### Learning Resources
- [PortSwigger Web Security Academy](https://portswigger.net/web-security)
- [Mozilla Web Security](https://infosec.mozilla.org/guidelines/web_security)
- [Google's XSS Game](https://xss-game.appspot.com/)

---

## Report Writing Guide

When documenting these vulnerabilities for your report, include:

### For Each Vulnerability:

1. **Introduction**
   - Brief description of the vulnerability
   - Why it's dangerous
   - Real-world impact examples

2. **Vulnerable Code Analysis**
   - Show the vulnerable code
   - Explain why it's vulnerable
   - Highlight the specific problem

3. **Exploitation Demonstration**
   - Step-by-step attack instructions
   - Screenshots of successful attacks
   - Explain what happened

4. **Impact Assessment**
   - What data could be compromised
   - What actions could an attacker perform
   - Severity rating (Low/Medium/High/Critical)

5. **Secure Code Implementation**
   - Show the fixed code
   - Explain the security measures
   - Highlight key differences

6. **Testing the Fix**
   - Demonstrate that attacks now fail
   - Screenshots of blocked attacks
   - Explain why the fix works

7. **Best Practices**
   - General recommendations
   - Additional security measures
   - Prevention strategies

---

## Warning

⚠️ **IMPORTANT SECURITY NOTICE**

This application contains **intentional security vulnerabilities** for educational purposes only.

**DO NOT:**
- Deploy this to any production environment
- Use this code in real applications
- Expose this to the public internet
- Use these vulnerable patterns in your own projects

**DO:**
- Use only in isolated local development environments
- Study the vulnerabilities to understand how they work
- Learn from the secure implementations
- Apply these security principles to your real projects

**Legal Notice:**
- This is for educational purposes only
- Unauthorized testing on systems you don't own is illegal
- Always get permission before security testing
- Use responsibly and ethically

---

## Troubleshooting

### Database Connection Issues
```
Error: Connection failed: Access denied for user 'root'@'localhost'
```
**Solution:** Check your database credentials in `config/database.php`

### MySQL Extension Not Found
```
Fatal error: Uncaught Error: Call to undefined function mysqli_connect()
```
**Solution:** Enable mysqli extension in php.ini

### PDO Extension Not Found
```
Fatal error: Uncaught Error: Class 'PDO' not found
```
**Solution:** Enable pdo_mysql extension in php.ini

### Session Issues
```
Warning: session_start(): Cannot send session cache limiter
```
**Solution:** Ensure session_start() is called before any output

---

## License

This project is created for educational purposes only. Use it to learn about web security vulnerabilities and their mitigations.

**Created by:** Wenura17125  
**Repository:** https://github.com/Wenura17125/security-demo  
**License:** Educational Use Only

---

## Acknowledgments

- OWASP for security guidelines and standards
- PHP Security Consortium for best practices
- Security research community for vulnerability documentation

---

**Remember:** The best defense is knowledge. Understanding how these attacks work is the first step in preventing them in your own applications!
