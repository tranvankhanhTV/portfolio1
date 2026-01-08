CREATE DATABASE portfolio_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE portfolio_db;

-- =====================
-- BẢNG CONTACT
-- =====================
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    status ENUM('new','read','replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- (OPTIONAL) ADMIN
-- =====================
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
