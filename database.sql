-- Database schema for Holo E-commerce
CREATE DATABASE IF NOT EXISTS `holo_ecommerce` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `holo_ecommerce`;

CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(10) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category_id INT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    features TEXT,
    image VARCHAR(255),
    featured BOOLEAN DEFAULT FALSE,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    address TEXT NOT NULL,
    city VARCHAR(100),
    state VARCHAR(100),
    zip VARCHAR(20),
    payment_method VARCHAR(50),
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO categories (id, name, icon) VALUES
(1, 'Smartphones', '📱'),
(2, 'Laptops', '💻'),
(3, 'Audio', '🎧'),
(4, 'Accessories', '⌨️'),
(5, 'Gaming', '🕹️');

INSERT INTO users (id, username, email, password, first_name, last_name, role, created_at) VALUES
(1, 'admin', 'admin@holo.com', '$2y$10$7HIrIl6plCn4dZOKpjukGeDyE27Y/fRW6Evao5wXSU0mg4qrpznqW', 'Admin', 'User', 'admin', '2024-01-01 00:00:00'),
(2, 'john', 'john@example.com', '$2y$10$W.XnSC8OoE/CBXGtwi4hd.ZVqEbDATvL/Oon32WiiRCk5agC8yZZa', 'John', 'Doe', 'customer', '2024-01-15 10:30:00');

INSERT INTO products (id, name, category_id, price, description, features, image, featured, stock) VALUES
(1, 'Apple iPhone 15 Pro Max (Red)', 1, 4299.00, 'The iPhone 15 Pro Max in red brings Apple\'s most powerful mobile platform to life with the A17 Pro chip, a stunning 6.7-inch Super Retina XDR display, and pro-level camera performance in a premium titanium frame.', '6.7" Super Retina XDR display\nA17 Pro chip\n48MP triple camera\nTitanium frame\nAction Button\nUSB-C connectivity', 'photos/phone.jpg', TRUE, 24),
(2, 'Apple iPhone 17', 1, 4999.00, 'The iPhone 17 delivers sleek design and strong performance with an A16 Bionic chip, a 6.1-inch OLED display, and advanced camera features for crisp photos and smooth video.', '6.1" OLED display\nA16 Bionic chip\n48MP main camera\nDynamic Island\nUSB-C charging\nCinematic video', 'photos/phone2.jpeg', TRUE, 28),
(3, 'Dell XPS 15', 2, 8599.00, 'The Dell XPS 15 pairs premium materials with a bright 15.6-inch OLED display, Intel Core i9 performance, and dedicated RTX graphics for creators and professionals on the move.', '15.6" OLED display\nIntel Core i9\nNVIDIA RTX 4060\n32GB RAM\n1TB SSD\nInfinityEdge touchscreen', 'photos/laptop.jpg', TRUE, 12),
(4, 'ASUS ROG Strix G16', 2, 7599.00, 'Built for gaming and content creation, the ASUS ROG Strix G16 features a high-refresh display, powerful Intel CPU, and advanced cooling to keep performance steady during marathon sessions.', '16" QHD 240Hz display\nIntel Core i7\nNVIDIA RTX 4070\nDDR5 memory\nRGB keyboard\nComprehensive cooling', 'photos/laptop 2.jpeg', FALSE, 14),
(5, 'Sony WH-1000XM5', 3, 1249.00, 'The Sony WH-1000XM5 offers industry-leading noise cancellation, plush comfort, and detailed sound tuning for premium listening at home or on the go.', 'Industry-leading ANC\n30-hour battery\nPrecise Voice Pickup\nWireless Bluetooth\nAdaptive Sound Control\nComfort fit', 'photos/headphones.jpg', TRUE, 24),
(6, 'Redragon M711', 4, 199.00, 'The Redragon M711 is a feature-packed wired gaming mouse with adjustable weights, RGB lighting, and high-precision tracking for competitive accuracy.', 'RGB lighting\nAdjustable weights\n16K DPI\n6 programmable buttons\nErgonomic shape\nDurable switches', 'photos/mouse2.jpeg', FALSE, 52),
(7, 'Corsair iCUE Gaming PC', 5, 12999.00, 'The Corsair iCUE Gaming PC combines an RGB-lit tower, high-end components, and premium cooling to deliver a stunning gaming experience for desktop enthusiasts.', 'Custom RGB cooling\nHigh airflow case\nPremium build quality\nAdvanced cable management\nGaming-ready performance\nQuick access front panel', 'photos/desktop.jpeg', TRUE, 8),
(8, 'HP Wireless Mouse', 4, 149.00, 'The HP Wireless Mouse delivers reliable wireless performance with a comfortable shape and easy navigation for home and office use.', 'Wireless connectivity\nComfort grip\nPlug-and-play\nCompact size\nLong battery life\nHigh precision', 'photos/mouse4.jpg', FALSE, 48);
