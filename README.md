# Web Security Vulnerabilities Demonstration

## Installation Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (recommended for local development)

### Setup Steps

1. **Clone or Download the Repository**
   ```bash
   git clone <repository-url>
   cd security-demo
   ```

2. **Database Setup**
   - Create a MySQL database named `security_demo`
   - Import the SQL file:
   ```bash
   mysql -u root -p security_demo < setup/database_setup.sql
   ```
   
   Or use phpMyAdmin to import the SQL file.

3. **Configure Database Connection**
   - Edit `config/database.php`
   - Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'security_demo');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   ```

4. **Start Web Server**
   - If using XAMPP, place the project in `htdocs/security-demo`
   - Start Apache and MySQL services
   - Access via: `http://localhost/security-demo`

5. **Test the Application**
   - Navigate to `http://localhost/security-demo/index.php`
   - Explore each vulnerability demonstration

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

## Security Features Demonstrated

### 1. SQL Injection
- **Vulnerable**: Direct string concatenation
- **Fixed**: PDO with prepared statements

### 2. Cross-Site Scripting (XSS)
- **Stored XSS**: Malicious scripts stored in database
- **Reflected XSS**: Scripts reflected from URL parameters
- **DOM-based XSS**: Client-side script injection
- **Fixed**: htmlentities() for all user output

### 3. Cross-Site Request Forgery (CSRF)
- **Vulnerable**: No token validation
- **Fixed**: Synchronizer Token Pattern

## Testing the Vulnerabilities

### SQL Injection Test
1. Go to vulnerable SQL injection page
2. Username: `admin' OR '1'='1`
3. Password: `anything`
4. Compare with secure version

### XSS Tests
1. **Stored XSS**: Enter `<script>alert('XSS')</script>` in comment
2. **Reflected XSS**: Use URL with malicious script
3. **DOM XSS**: Enter script in input field
4. Compare with secure versions

### CSRF Test
1. Login to vulnerable CSRF page
2. Open malicious attack page in new tab
3. See unauthorized order placed
4. Try same attack on secure version

## Warning

⚠️ **This application contains intentional security vulnerabilities for educational purposes only.**

- Never deploy this to a production environment
- Only use in isolated development environments
- Understand each vulnerability before testing
- Use responsibly and ethically

## License

This project is for educational purposes only.
```

---

## Report Outline (PDF)

Here's the structure for your detailed report:

### Report Structure

```
WEB SECURITY VULNERABILITIES DEMONSTRATION AND MITIGATION
Student Name | Student ID | Date

TABLE OF CONTENTS
1. Introduction
2. SQL Injection
3. Cross-Site Scripting (XSS)
4. Cross-Site Request Forgery (CSRF)
5. Conclusion
6. References

1. INTRODUCTION
   - Purpose of the project
   - Overview of vulnerabilities
   - Testing environment setup

2. SQL INJECTION
   2.1 What is SQL Injection?
   2.2 Vulnerability Demonstration
       - Code example (vulnerable)
       - Screenshot of successful attack
       - Explanation of how it works
   2.3 Security Impact
       - Data breach potential
       - Authentication bypass
       - Database manipulation
   2.4 Mitigation Using PDO
       - Code example (secure)
       - Prepared statements explanation
       - Screenshot of blocked attack
   2.5 Best Practices

3. CROSS-SITE SCRIPTING (XSS)
   3.1 What is XSS?
   3.2 Stored XSS
       - Vulnerability demonstration
       - Code example (vulnerable)
       - Screenshot of attack
       - Mitigation with htmlentities()
       - Code example (secure)
       - Screenshot of protection
   3.3 Reflected XSS
       - Vulnerability demonstration
       - Code example (vulnerable)
       - Screenshot of attack
       - Mitigation approach
       - Code example (secure)
   3.4 DOM-based XSS
       - Vulnerability demonstration
       - Code example (vulnerable)
       - Screenshot of attack
       - Mitigation using textContent
       - Code example (secure)
   3.5 Best Practices

4. CROSS-SITE REQUEST FORGERY (CSRF)
   4.1 What is CSRF?
   4.2 Vulnerability Demonstration
       - Code example (vulnerable)
       - Screenshot of malicious page
       - Screenshot of successful attack
   4.3 Security Impact
   4.4 Mitigation Using Synchronizer Token Pattern
       - Token generation
       - Token validation
       - Code example (secure)
       - Screenshot of blocked attack
   4.5 Alternative: Double-Submit Cookie Pattern
   4.6 Best Practices

5. CONCLUSION
   - Summary of vulnerabilities
   - Importance of secure coding
   - Key takeaways

6. REFERENCES
   - PHP Documentation
   - OWASP Guidelines
   - Security resources
```

### Screenshot Checklist for Report

For each vulnerability, include:

1. **SQL Injection**
   - Login form with malicious input
   - Successful unauthorized access
   - Database query in code
   - Secure version blocking attack

2. **Stored XSS**
   - Comment form with XSS payload
   - Alert box executing
   - View source showing escaped HTML
   - Secure version displaying as text

3. **Reflected XSS**
   - URL with XSS payload
   - Alert box executing
   - Secure version displaying safely

4. **DOM XSS**
   - Input with malicious script
   - JavaScript executing
   - Secure version using textContent

5. **CSRF**
   - Vulnerable order form
   - Malicious attack page
   - Unauthorized order in database
   - Secure version rejecting attack