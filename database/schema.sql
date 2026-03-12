-- =============================================
-- Project730 — Database Schema
-- =============================================
-- Run this file once to set up the database.
-- Then seed an admin user (see INSERT at bottom).

CREATE DATABASE IF NOT EXISTS project730_activation1
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE project730_activation1;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100)                NOT NULL,
    email         VARCHAR(150)                NOT NULL UNIQUE,
    password_hash VARCHAR(255)                NULL,
    role          VARCHAR(30)                 NOT NULL DEFAULT 'employee',
    user_status   ENUM('Pending', 'Active')   NOT NULL DEFAULT 'Pending',
    created_at    DATETIME                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME                    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Invitation tokens table
CREATE TABLE IF NOT EXISTS invitation_tokens (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    token_hash CHAR(64)     NOT NULL UNIQUE,
    expires_at DATETIME     NOT NULL,
    used_at    DATETIME     NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_invitation_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

-- =============================================
-- Seed: Default Admin Account
-- Password: Admin@123  (hashed with PASSWORD_DEFAULT)
-- =============================================
-- Generate a new hash with:  php -r "echo password_hash('Admin@123', PASSWORD_DEFAULT);"
-- Then replace the hash below.

INSERT INTO users (full_name, email, password_hash, role, user_status)
VALUES (
    'Admin User',
    'admin@abcgas.com',
    '$2y$12$wkOLW7Xyz.pLAoOiONKD3eCe6iGxWvMT5d0Ir6DUlRqhZfq/sooEm',
    'admin',
    'Active'
)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);
