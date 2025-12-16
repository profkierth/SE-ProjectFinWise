CREATE DATABASE finance_app;
USE finance_app;

CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('income','expense') NOT NULL,
    icon VARCHAR(50) DEFAULT NULL,
    color VARCHAR(20) DEFAULT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('income','expense','report','system') DEFAULT 'system',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


/*
INSERT INTO categories (user_id, name, type, icon, color) VALUES
(1,'Salary','income','fa-coins','#2ecc71'),
(1,'Business','income','fa-briefcase','#27ae60'),
(1,'Food','expense','fa-burger','#e74c3c'),
(1,'Transport','expense','fa-bus','#f39c12'),
(1,'Bills','expense','fa-file-invoice','#c0392b'),
(1,'Shopping','expense','fa-cart-shopping','#8e44ad');
*/


CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    birthdate DATE NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    address VARCHAR(255) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) 
        DEFAULT 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


select * from users;
select * from transactions;
select * from categories;
select * from notifications;
