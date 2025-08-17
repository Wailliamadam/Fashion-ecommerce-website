
-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ======================================
-- 1. DROP TABLES IF EXIST (FOR RESET)
-- ======================================
DROP TABLE IF EXISTS product_tags;
DROP TABLE IF EXISTS product_sizes;
DROP TABLE IF EXISTS product_colors;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS sizes;
DROP TABLE IF EXISTS colors;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS categories;

-- ======================================
-- 2. TABLE CREATION
-- ======================================

-- CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id INT DEFAULT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- BRANDS
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

-- COLORS
CREATE TABLE colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- SIZES
CREATE TABLE sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- TAGS
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- PRODUCTS
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    brand_id INT,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- PRODUCT-COLORS (Many-to-many)
CREATE TABLE product_colors (
    product_id INT,
    color_id INT,
    PRIMARY KEY (product_id, color_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE CASCADE
);

-- PRODUCT-SIZES (Many-to-many)
CREATE TABLE product_sizes (
    product_id INT,
    size_id INT,
    PRIMARY KEY (product_id, size_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
);

-- PRODUCT-TAGS (Many-to-many)
CREATE TABLE product_tags (
    product_id INT,
    tag_id INT,
    PRIMARY KEY (product_id, tag_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- ======================================
-- 3. INSERT MASTER DATA
-- ======================================

-- CATEGORIES
INSERT INTO categories (name) VALUES
('Bag'), ('Clothing'), ('Shoe'), ('Accessories'), ('Kid'), ('Men'), ('Women');

-- BRANDS
INSERT INTO brands (name) VALUES
('LV'), ('Chanel'), ('Hermes'), ('Gucci');

-- COLORS
INSERT INTO colors (name) VALUES
('Black'), ('Blue'), ('Orange'), ('Red'), ('White'), ('Pink');

-- SIZES
INSERT INTO sizes (name) VALUES
('XS'), ('S'), ('M'), ('XL'), ('2XL'), ('XXL'), ('3XL'), ('4XL');

-- TAGS
INSERT INTO tags (name) VALUES
('Product'), ('Bag'), ('Shoe'), ('Fashion'), ('Clothing'), ('Hats'), ('Accessories');


-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Admin Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    password VARCHAR(255)
);


-- Insert one admin (password = 'admin123')
INSERT INTO admins (username, password)
VALUES ('admin', '$2y$10$5MHZUP9TnMevXm9d.g8w9e7JPCSRrUEp5r4UtqfZzNOYRHZtEZeq2'); 
-- Password is hashed version of 'admin123'



CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL
);

-- Sample data for male fashion e-commerce
INSERT INTO faqs (category, question, answer) VALUES
('Orders', 'I’ve just placed an order, when will I get my delivery?', 'Orders placed before 3pm EST are processed the same business day. Delivery typically takes 2-5 business days depending on your location.'),
('Orders', 'Can I modify my order after placing it?', 'We process orders quickly to ensure fast delivery. Please contact our customer service within 30 minutes of placing your order for modification requests.'),
('Delivery', 'What are your delivery options?', 'We offer standard (3-5 business days), express (2 business days), and overnight delivery options.'),
('Delivery', 'Do you offer international shipping?', 'Yes, we ship to over 50 countries worldwide. International delivery times vary by destination.'),
('Payments', 'When do I get charged?', 'Your payment method is charged immediately when you place your order.'),
('Payments', 'What payment methods do you accept?', 'We accept all major credit cards, PayPal, Apple Pay, and Google Pay.'),
('Returns', 'How do I return an item?', 'You can initiate a return through your account dashboard. We offer free returns within 30 days of delivery.'),
('Returns', 'How long does it take to process a refund?', 'Refunds are processed within 3-5 business days after we receive your returned items.');


-- CREATE TABLE users (
--     id INT(11) NOT NULL AUTO_INCREMENT,
--     username VARCHAR(50) NOT NULL UNIQUE,
--     email VARCHAR(100) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,
--     PRIMARY KEY (id)
-- );

-- DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
