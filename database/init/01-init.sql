-- Database initialization script
-- This runs automatically when the container first starts

USE suggestion_db;

-- Create users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(8) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create suggestions table
CREATE TABLE IF NOT EXISTS suggestions (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    fullname VARCHAR(64) NOT NULL,
    suggestion LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional - remove in production)
-- Password for all demo users is: Demo1234!
INSERT INTO users (username, password_hash) VALUES
    ('70000009', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
    ('70123409', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO suggestions (user_id, fullname, suggestion) VALUES
    (1, 'Anonymous', 'Provide free chocolate in the offices'),
    (2, 'Dr B', 'Free copies of BTTF as xmas bonuses')
ON DUPLICATE KEY UPDATE id=id;

-- Grant privileges (these are set via environment variables in docker-compose)
-- But we ensure the user exists and has correct permissions
FLUSH PRIVILEGES;