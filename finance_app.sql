CREATE DATABASE finance_app;
USE finance_app;

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income','expense') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at DATE NOT NULL
);

-- Sample data
INSERT INTO transactions (type, amount, created_at) VALUES
('income', 2000, CURDATE()),
('expense', 500, CURDATE()),
('income', 3000, DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
('expense', 800, DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
('income', 3000, DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
('expense', 1000, DATE_SUB(CURDATE(), INTERVAL 2 DAY));

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('income','expense') NOT NULL
);

CREATE TABLE transactions1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    type ENUM('income','expense') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    month VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    birthdate DATE NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    address VARCHAR(255) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
