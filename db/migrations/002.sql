-- Authentication System - Users Table Migration
-- Run this after the main schema to add authentication

USE zuriel_invoice_system;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create default admin user
-- Username: admin
-- Password: admin123 (CHANGE THIS AFTER FIRST LOGIN!)
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('admin', 'admin@zurieltech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Password is 'admin123' - hashed with PASSWORD_DEFAULT
-- Users should change this immediately after first login

-- Add indexes for performance
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_status ON users(status);
CREATE INDEX idx_role ON users(role);

-- Session Management Table (optional but recommended)
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(64) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_session_token ON user_sessions(session_token);
CREATE INDEX idx_user_id ON user_sessions(user_id);
CREATE INDEX idx_expires_at ON user_sessions(expires_at);

-- Login Attempts Table (for security - track failed login attempts)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_username_ip ON login_attempts(username, ip_address);
CREATE INDEX idx_attempted_at ON login_attempts(attempted_at);

-- Add created_by and updated_by to existing tables (optional - for audit trail)
-- Uncomment these if you want to track who created/modified records

-- ALTER TABLE invoices ADD COLUMN created_by INT AFTER status;
-- ALTER TABLE invoices ADD COLUMN updated_by INT AFTER created_by;
-- ALTER TABLE invoices ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
-- ALTER TABLE invoices ADD FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;

-- ALTER TABLE receipts ADD COLUMN created_by INT AFTER status;
-- ALTER TABLE receipts ADD COLUMN updated_by INT AFTER created_by;
-- ALTER TABLE receipts ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
-- ALTER TABLE receipts ADD FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;

-- ALTER TABLE customers ADD COLUMN created_by INT AFTER updated_at;
-- ALTER TABLE customers ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- ALTER TABLE products ADD COLUMN created_by INT AFTER updated_at;
-- ALTER TABLE products ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;