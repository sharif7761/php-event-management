-- Step 1: Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS event_management;

-- Step 2: Use the database
USE event_management;

-- Step 3: Create the users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Step 4: Create the events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    user_id INT NOT NULL,
    max_capacity INT NOT NULL,
    event_datetime DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
    );

-- Step 5: Create the event_attendees table
CREATE TABLE IF NOT EXISTS event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    attendee_name VARCHAR(100) NOT NULL,
    attendee_email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id)
    );

-- Step 6: Insert admin user. password: admin123
INSERT INTO users (name, email, password, role) VALUES
    ('Admin User', 'admin@example.com', '$2y$10$abcdefghijABCDEFGHIJabcdefghijABCDEFGHIJabcdefghijABCDEFGHIJ', 'admin');

-- Step 7: Insert regular user. password: user123
INSERT INTO users (name, email, password, role) VALUES
    ('Regular User', 'user@example.com', '$2y$10$klmnopqrstKLMNOPQRSTklmnopqrstKLMNOPQRSTklmnopqrstKLMNOPQRST', 'user');
