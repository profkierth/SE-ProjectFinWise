CREATE DATABASE finance_app;
USE finance_app;

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income','expense') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at DATE NOT NULL
);


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

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('income','expense','report','system') DEFAULT 'system',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO notifications (user_id, title, message, type)
VALUES
(1, 'New Expense Added', 'You recorded ₱250 for transportation.', 'expense'),
(1, 'Income Update', 'Your ₱8,000 salary was added.', 'income'),
(1, 'Weekly Report', 'Your weekly summary is now available.', 'report');



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

select * from users;
select * from transactions;
select * from categories;
select * from notification;
