# Security Demo - Quick Start Guide

## 🚀 Quick Setup (5 minutes)

1. **Start XAMPP**
   - Start Apache
   - Start MySQL

2. **Import Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database: `security_demo`
   - Import: `setup/database_setup.sql`

3. **Access Application**
   - Navigate to: `http://localhost/security-demo`

## 🎯 Quick Test Guide

### SQL Injection (2 minutes)
1. Go to: `vulnerable/sql_injection.php`
2. Username: `admin' OR '1'='1`
3. Password: `anything`
4. ✅ You're logged in! (Attack successful)
5. Compare with: `secure/sql_injection_fixed.php`
6. Same attack fails! (Protected)

### Stored XSS (2 minutes)
1. Go to: `vulnerable/xss_stored.php`
2. Username: `Hacker`
3. Comment: `<script>alert('XSS!')</script>`
4. ✅ Alert appears! (Attack successful)
5. Compare with: `secure/xss_stored_fixed.php`
6. Script appears as text! (Protected)

### Reflected XSS (1 minute)
1. Go to: `vulnerable/xss_reflected.php`
2. Search: `<img src=x onerror="alert('XSS')">`
3. ✅ Alert appears! (Attack successful)
4. Compare with: `secure/xss_reflected_fixed.php`
5. Script appears as text! (Protected)

### DOM-based XSS (1 minute)
1. Go to: `vulnerable/xss_dom.php`
2. Name: `<img src=x onerror="alert('DOM XSS')">`
3. Click: Greet Me
4. ✅ Alert appears! (Attack successful)
5. Compare with: `secure/xss_dom_fixed.php`
6. Script appears as text! (Protected)

### CSRF (3 minutes)
1. Go to: `vulnerable/csrf.php`
2. Place an order (note your order count)
3. Click: "Open Malicious Page (in new tab)"
4. Return to original tab and refresh
5. ✅ New order appeared! (Attack successful)
6. Compare with: `secure/csrf_fixed.php`
7. Try attack page again
8. Error message! (Protected)

## 📋 What Was Fixed

### The Main Problem
**Error:** `Fatal error: You have an error in your SQL syntax near 'XSS Attack!')</script>', 'test')'`

### The Fix
Added `mysqli_real_escape_string()` to prevent SQL syntax errors in vulnerable demos:
- ✅ `vulnerable/xss_stored.php` - Fixed
- ✅ `vulnerable/sql_injection.php` - Fixed  
- ✅ `vulnerable/csrf.php` - Fixed

### Important Notes
- The demos are STILL vulnerable (intentionally for learning)
- SQL syntax errors are fixed
- XSS, SQL Injection, and CSRF vulnerabilities remain for demonstration
- Use the `/secure` versions to see proper fixes

## 📚 Documentation

For detailed information, see:
- **Full Guide:** `README.md` - Complete testing guide with all details
- **Project Structure:** Check all files in `/vulnerable` and `/secure`

## ⚠️ Warning

**NEVER use this in production!** This is for learning only.

## 🎓 Learning Path

1. Test each vulnerability (vulnerable version)
2. Understand why it works
3. Study the secure version
4. See why the attack fails
5. Apply lessons to your projects

**Happy Learning! 🔒**
