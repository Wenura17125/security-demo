CREATE DATABASE IF NOT EXISTS security_demo;
USE security_demo;

-- Users table for SQL Injection demo
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
INSERT INTO users (username, password, email) VALUES
('admin', 'admin123', 'admin@example.com'),
('john_doe', 'password123', 'john@example.com'),
('jane_smith', 'pass456', 'jane@example.com');

-- Comments table for Stored XSS demo
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table for CSRF demo
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0
);

INSERT INTO products (name, price, stock) VALUES
('Laptop', 999.99, 10),
('Mouse', 29.99, 50),
('Keyboard', 79.99, 30);

-- Orders table for CSRF demo
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    quantity INT,
    user_session VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);