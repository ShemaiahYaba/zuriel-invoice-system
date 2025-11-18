-- Zuriel Invoice & Receipt System Database Schema
-- Run this file to create all necessary tables

CREATE DATABASE IF NOT EXISTS zuriel_invoice_system;
USE zuriel_invoice_system;

-- Customers Table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products/Services Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    description TEXT NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Invoices Table
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    customer_name VARCHAR(255) NOT NULL,
    customer_address TEXT,
    invoice_date DATE NOT NULL,
    lpo_number VARCHAR(100),
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    amount_in_words TEXT,
    invoice_type ENUM('cash', 'credit') DEFAULT 'cash',
    status ENUM('draft', 'issued', 'paid', 'archived') DEFAULT 'issued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Invoice Items Table
CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    qty INT NOT NULL,
    description TEXT NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Receipts Table
CREATE TABLE receipts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    receipt_number VARCHAR(50) UNIQUE NOT NULL,
    receipt_date DATE NOT NULL,
    received_from VARCHAR(255) NOT NULL,
    amount_naira INT NOT NULL,
    amount_kobo INT NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_for TEXT NOT NULL,
    payment_method ENUM('cash', 'transfer', 'pos', 'other') NOT NULL,
    status ENUM('issued', 'archived') DEFAULT 'issued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Configuration Table
CREATE TABLE config (
    config_key VARCHAR(100) PRIMARY KEY,
    config_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Configuration
INSERT INTO config (config_key, config_value) VALUES
('COMPANY_NAME', 'ZURIEL TECH VENTURES'),
('COMPANY_TAGLINE', 'Innovating Tomorrow, Today'),
('COMPANY_LOGO', '/images/logo.png'),
('COMPANY_ADDRESS', 'Shop 1-041, Area 1 Shopping Plaza, Garki Abuja'),
('COMPANY_PHONE_1', '+234 (0) 908 444 4240'),
('COMPANY_PHONE_2', '+234 (0) 908 444 4140'),
('COMPANY_EMAIL', 'zurieltechventures@gmail.com'),
('INVOICE_PREFIX', 'INV'),
('INVOICE_START_NUMBER', '1'),
('RECEIPT_PREFIX', 'RCP'),
('RECEIPT_START_NUMBER', '1'),
('PRIMARY_COLOR', '#0066CC'),
('HEADER_BG_COLOR', '#0066CC'),
('CURRENCY_SYMBOL_MAJOR', '₦'),
('CURRENCY_SYMBOL_MINOR', 'K'),
('CURRENCY_NAME', 'Naira');

-- Insert Sample Data
INSERT INTO customers (name, address, phone, email) VALUES
('John Doe', '123 Main Street, Lagos', '+234 801 234 5678', 'john@example.com'),
('Jane Smith', '456 Oak Avenue, Abuja', '+234 802 345 6789', 'jane@example.com');

INSERT INTO products (description, rate) VALUES
('Website Development', 150000.00),
('Mobile App Development', 250000.00),
('IT Consultation (per hour)', 15000.00),
('Network Setup and Configuration', 75000.00),
('Software Maintenance (monthly)', 50000.00);