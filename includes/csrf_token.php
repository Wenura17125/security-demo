<?php
// CSRF Token Generation and Validation

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
?>