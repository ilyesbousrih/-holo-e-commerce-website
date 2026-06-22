<?php
$sessionSavePath = __DIR__ . '/../tmp';
if (!is_dir($sessionSavePath)) {
    @mkdir($sessionSavePath, 0777, true);
}

// Only modify session save path and cookie params if a session is not yet active
if (session_status() === PHP_SESSION_NONE) {
    if (is_dir($sessionSavePath)) {
        session_save_path($sessionSavePath);
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}
// Mock database - In production, this would connect to a real database

// Categories
$categories_data = [
    ['id' => 1, 'name' => 'Smartphones', 'icon' => '📱'],
    ['id' => 2, 'name' => 'Laptops', 'icon' => '💻'],
    ['id' => 3, 'name' => 'Audio', 'icon' => '🎧'],
    ['id' => 4, 'name' => 'Accessories', 'icon' => '⌨️'],
    ['id' => 5, 'name' => 'Gaming', 'icon' => '🕹️'],
];

// Users - Admin account: admin@holo.com / admin123
$users_data = [
    [
        'id' => 1,
        'email' => 'admin@holo.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'first_name' => 'Admin',
        'last_name' => 'User',
        'role' => 'admin',
        'created_at' => '2024-01-01 00:00:00'
    ],
    [
        'id' => 2,
        'email' => 'john@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'first_name' => 'John',
        'last_name' => 'Doe',
        'role' => 'customer',
        'created_at' => '2024-01-15 10:30:00'
    ],
];

// Real Products using the new local image set
$products_data = [
    // Smartphones
    [
        'id' => 1,
        'name' => 'Apple iPhone 15 Pro Max (Red)',
        'category_id' => 1,
        'price' => 4299.00,
        'description' => 'The iPhone 15 Pro Max in red brings Apple\'s most powerful mobile platform to life with the A17 Pro chip, a stunning 6.7-inch Super Retina XDR display, and pro-level camera performance in a premium titanium frame.',
        'features' => ['6.7" Super Retina XDR display', 'A17 Pro chip', '48MP triple camera', 'Titanium frame', 'Action Button', 'USB-C connectivity'],
        'image' => 'photos/phone.jpg',
        'featured' => true,
        'stock' => 24
    ],
    [
        'id' => 2,
        'name' => 'Apple iPhone 17',
        'category_id' => 1,
        'price' => 4999.00,
        'description' => 'The iPhone 17 delivers sleek design and strong performance with an A16 Bionic chip, a 6.1-inch OLED display, and advanced camera features for crisp photos and smooth video.',
        'features' => ['6.1" OLED display', 'A16 Bionic chip', '48MP main camera', 'Dynamic Island', 'USB-C charging', 'Cinematic video'],
        'image' => 'photos/phone2.jpeg',
        'featured' => true,
        'stock' => 28
    ],
    [
        'id' => 3,
        'name' => 'Apple iPhone 15 Pro (Gold)',
        'category_id' => 1,
        'price' => 3899.00,
        'description' => 'The iPhone 15 Pro in gold combines precision engineering with a lightweight titanium design, a fast A17 Pro chip, and a professional-grade camera system for creatives and power users.',
        'features' => ['6.1" Super Retina XDR display', 'A17 Pro chip', '48MP triple camera', 'ProMotion 120Hz', 'Titanium finish', 'Advanced video modes'],
        'image' => 'photos/phone3.jpeg',
        'featured' => false,
        'stock' => 20
    ],
    [
        'id' => 4,
        'name' => 'Apple iPhone 15 Pro Max (Space Black)',
        'category_id' => 1,
        'price' => 4299.00,
        'description' => 'The iPhone 15 Pro Max in space black delivers flagship power with long battery life, a vivid Super Retina XDR display, and a professional camera system for stunning low-light photography.',
        'features' => ['6.7" Super Retina XDR display', 'A17 Pro chip', '48MP triple camera', 'ProRAW support', 'Titanium body', 'Action Button'],
        'image' => 'photos/phone4.jpg',
        'featured' => false,
        'stock' => 18
    ],
    [
        'id' => 5,
        'name' => 'Apple iPhone SE (3rd Gen)',
        'category_id' => 1,
        'price' => 1899.00,
        'description' => 'The iPhone SE (3rd Gen) offers iconic design, a powerful A15 Bionic chip, and reliable performance in a compact package with a single-camera setup and strong battery life.',
        'features' => ['4.7" Retina HD display', 'A15 Bionic chip', 'Face ID', 'Single 12MP camera', 'Compact design', 'Wireless charging'],
        'image' => 'photos/phone5.jpeg',
        'featured' => false,
        'stock' => 32
    ],
    // Laptops
    [
        'id' => 6,
        'name' => 'Dell XPS 15',
        'category_id' => 2,
        'price' => 8599.00,
        'description' => 'The Dell XPS 15 pairs premium materials with a bright 15.6-inch OLED display, Intel Core i9 performance, and dedicated RTX graphics for creators and professionals on the move.',
        'features' => ['15.6" OLED display', 'Intel Core i9', 'NVIDIA RTX 4060', '32GB RAM', '1TB SSD', 'InfinityEdge touchscreen'],
        'image' => 'photos/laptop.jpg',
        'featured' => true,
        'stock' => 12
    ],
    [
        'id' => 7,
        'name' => 'ASUS ROG Strix G16',
        'category_id' => 2,
        'price' => 7599.00,
        'description' => 'Built for gaming and content creation, the ASUS ROG Strix G16 features a high-refresh display, powerful Intel CPU, and advanced cooling to keep performance steady during marathon sessions.',
        'features' => ['16" QHD 240Hz display', 'Intel Core i7', 'NVIDIA RTX 4070', 'DDR5 memory', 'RGB keyboard', 'Comprehensive cooling'],
        'image' => 'photos/laptop 2.jpeg',
        'featured' => false,
        'stock' => 14
    ],
    [
        'id' => 8,
        'name' => 'Lenovo Legion 9i',
        'category_id' => 2,
        'price' => 8999.00,
        'description' => 'The Lenovo Legion 9i delivers premium gaming performance with a crisp display, Intel Alder Lake processor, and advanced thermal design for sustained frame rates under load.',
        'features' => ['16" QHD display', 'Intel Core i9', 'NVIDIA RTX 4080', 'Liquid metal cooling', 'RGB lighting', 'Thunderbolt 4'],
        'image' => 'photos/laptop 3.jpeg',
        'featured' => false,
        'stock' => 10
    ],
    [
        'id' => 9,
        'name' => 'MSI Raider GE78',
        'category_id' => 2,
        'price' => 8299.00,
        'description' => 'The MSI Raider GE78 brings desktop-class power to gamers with a fast display, cutting-edge RTX graphics, and a bold thermal chassis for high-performance play.',
        'features' => ['17.3" 240Hz display', 'Intel Core i9', 'NVIDIA RTX 4080', 'RGB light bar', '5 heat pipes', 'Thunderbolt 4'],
        'image' => 'photos/laptop4.jpeg',
        'featured' => false,
        'stock' => 9
    ],
    [
        'id' => 10,
        'name' => 'MSI Stealth 16',
        'category_id' => 2,
        'price' => 6999.00,
        'description' => 'The MSI Stealth 16 blends a slim chassis with strong gaming hardware, delivering portability without sacrificing performance or display quality.',
        'features' => ['16" QHD display', 'Intel Core i7', 'NVIDIA RTX 4070', 'NVMe SSD', 'RGB keyboard', 'Long battery life'],
        'image' => 'photos/laptop5.jpeg',
        'featured' => false,
        'stock' => 16
    ],
    [
        'id' => 11,
        'name' => 'ASUS TUF Gaming A15',
        'category_id' => 2,
        'price' => 5299.00,
        'description' => 'The ASUS TUF Gaming A15 is built for value gamers, offering a rugged chassis, reliable performance, and a high-refresh display for smooth play.',
        'features' => ['15.6" FHD 144Hz display', 'AMD Ryzen 9', 'NVIDIA RTX 4060', 'MIL-STD durability', 'Wi-Fi 6', 'RGB keyboard'],
        'image' => 'photos/laptop6.jpeg',
        'featured' => false,
        'stock' => 18
    ],
    // Audio
    [
        'id' => 12,
        'name' => 'Sony WH-1000XM5',
        'category_id' => 3,
        'price' => 1249.00,
        'description' => 'The Sony WH-1000XM5 offers industry-leading noise cancellation, plush comfort, and detailed sound tuning for premium listening at home or on the go.',
        'features' => ['Industry-leading ANC', '30-hour battery', 'Precise Voice Pickup', 'Wireless Bluetooth', 'Adaptive Sound Control', 'Comfort fit'],
        'image' => 'photos/headphones.jpg',
        'featured' => true,
        'stock' => 24
    ],
    [
        'id' => 13,
        'name' => 'Nothing Ear (2)',
        'category_id' => 3,
        'price' => 699.00,
        'description' => 'Nothing Ear (2) delivers transparent sound with active noise cancellation, a tactile charging case, and long-lasting wireless listening in a modern design.',
        'features' => ['Active Noise Cancellation', 'Wireless charging case', '11.6mm drivers', 'Up to 34-hour battery', 'Bluetooth 5.3', 'Fast pairing'],
        'image' => 'photos/headphone2.jpg',
        'featured' => false,
        'stock' => 30
    ],
    [
        'id' => 14,
        'name' => 'Sony MDR-EX155AP',
        'category_id' => 3,
        'price' => 149.00,
        'description' => 'Sony MDR-EX155AP wired earbuds offer clear audio and in-line controls for reliable everyday listening with a compact, comfortable design.',
        'features' => ['Wired in-ear design', 'In-line mic', 'Lightweight fit', 'Clear audio', 'Tangle-resistant cable', 'Universal compatibility'],
        'image' => 'photos/headphone3.jpg',
        'featured' => false,
        'stock' => 45
    ],
    [
        'id' => 15,
        'name' => 'JBL Live 660NC',
        'category_id' => 3,
        'price' => 699.00,
        'description' => 'JBL Live 660NC combines over-ear comfort with adaptive noise cancellation and JBL Signature Sound, making it ideal for travel and daily commutes.',
        'features' => ['Adaptive Noise Cancellation', 'Up to 50-hour battery', 'Multi-device pairing', 'Voice assistant support', 'Comfort fit', 'Detachable cable'],
        'image' => 'photos/headphone4.jpg',
        'featured' => false,
        'stock' => 28
    ],
    [
        'id' => 16,
        'name' => 'Beats Solo3 Wireless',
        'category_id' => 3,
        'price' => 799.00,
        'description' => 'Beats Solo3 Wireless delivers rich sound, comfortable on-ear design, and up to 40 hours of battery life for music lovers who need long-lasting performance.',
        'features' => ['40-hour battery', 'Wireless Bluetooth', 'Fast Fuel charging', 'Comfortable ear cushions', 'On-ear controls', 'Built-in microphone'],
        'image' => 'photos/headphone5.jpg',
        'featured' => false,
        'stock' => 35
    ],
    // Accessories
    [
        'id' => 17,
        'name' => 'Razer DeathAdder V2',
        'category_id' => 4,
        'price' => 319.00,
        'description' => 'The Razer DeathAdder V2 is a high-precision gaming mouse with optical switches, ergonomic comfort, and customizable RGB lighting for competitive gameplay.',
        'features' => ['20K DPI optical sensor', 'Optical mouse switches', 'RGB lighting', 'Ergonomic design', '6 programmable buttons', 'Speedflex cable'],
        'image' => 'photos/mouse.jpeg',
        'featured' => true,
        'stock' => 40
    ],
    [
        'id' => 18,
        'name' => 'Redragon M711',
        'category_id' => 4,
        'price' => 199.00,
        'description' => 'The Redragon M711 is a feature-packed wired gaming mouse with adjustable weights, RGB lighting, and high-precision tracking for competitive accuracy.',
        'features' => ['RGB lighting', 'Adjustable weights', '16K DPI', '6 programmable buttons', 'Ergonomic shape', 'Durable switches'],
        'image' => 'photos/mouse2.jpg',
        'featured' => false,
        'stock' => 52
    ],
    [
        'id' => 19,
        'name' => 'Logitech M185',
        'category_id' => 4,
        'price' => 129.00,
        'description' => 'The Logitech M185 wireless mouse offers simple plug-and-play connectivity, comfortable ambidextrous design, and long battery life for everyday productivity.',
        'features' => ['Wireless USB receiver', 'Compact design', 'Long battery life', 'Ambidextrous', 'Plug-and-play', 'Smooth tracking'],
        'image' => 'photos/mouse3.jpg',
        'featured' => false,
        'stock' => 60
    ],
    [
        'id' => 20,
        'name' => 'HP Wireless Mouse',
        'category_id' => 4,
        'price' => 149.00,
        'description' => 'The HP Wireless Mouse delivers reliable wireless performance with a comfortable shape and easy navigation for home and office use.',
        'features' => ['Wireless connectivity', 'Comfort grip', 'Plug-and-play', 'Compact size', 'Long battery life', 'High precision'],
        'image' => 'photos/mouse4.jpg',
        'featured' => false,
        'stock' => 48
    ],
    [
        'id' => 21,
        'name' => 'HP USB Mouse',
        'category_id' => 4,
        'price' => 99.00,
        'description' => 'The HP USB Mouse is a simple wired solution for reliable daily computing with smooth cursor control and a comfortable form factor.',
        'features' => ['Wired USB connection', 'Comfortable shape', 'Precise tracking', 'Plug-and-play', 'Lightweight design', 'Durable buttons'],
        'image' => 'photos/mouse5.jpg',
        'featured' => false,
        'stock' => 54
    ],
    // Gaming PCs
    [
        'id' => 22,
        'name' => 'Corsair iCUE Gaming PC',
        'category_id' => 5,
        'price' => 12999.00,
        'description' => 'The Corsair iCUE Gaming PC combines an RGB-lit tower, high-end components, and premium cooling to deliver a stunning gaming experience for desktop enthusiasts.',
        'features' => ['Custom RGB cooling', 'High airflow case', 'Premium build quality', 'Advanced cable management', 'Gaming-ready performance', 'Quick access front panel'],
        'image' => 'photos/desktop.jpeg',
        'featured' => true,
        'stock' => 8
    ],
    [
        'id' => 23,
        'name' => 'NZXT H510 Gaming PC',
        'category_id' => 5,
        'price' => 11999.00,
        'description' => 'The NZXT H510 Gaming PC delivers clean design, effective airflow, and powerful hardware in a modern chassis built for both gaming and content creation.',
        'features' => ['Tempered glass side panel', 'RGB front fans', 'Cable management', 'High-performance cooling', 'Stylish interior', 'Ready for upgrades'],
        'image' => 'photos/desktop2.jpeg',
        'featured' => false,
        'stock' => 10
    ],
    [
        'id' => 24,
        'name' => 'Thermaltake RGB Desktop',
        'category_id' => 5,
        'price' => 10999.00,
        'description' => 'The Thermaltake RGB Desktop pairs bold lighting with a tempered glass chassis to create a stunning centerpiece for gaming setups and productivity workstations.',
        'features' => ['RGB fan lighting', 'Tempered glass side', 'High airflow', 'Tool-free design', 'Gaming-grade cooling', 'Upgrade-ready interior'],
        'image' => 'photos/desktop3.jpeg',
        'featured' => false,
        'stock' => 12
    ],
];

$host = 'localhost';
$dbname = 'holo_ecommerce';
$db_username = 'root';
$db_password = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        icon VARCHAR(10) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        role ENUM('customer','admin') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
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
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
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
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

function getDb() {
    global $pdo;
    return $pdo;
}

function formatProductFeatures(array $features): string {
    return implode("\n", array_map('trim', array_filter($features, fn($value) => $value !== '')));
}

try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM categories');
    if ($stmt->fetchColumn() == 0) {
        $insert = $pdo->prepare('INSERT INTO categories (id, name, icon) VALUES (?, ?, ?)');
        foreach ($categories_data as $category) {
            $insert->execute([$category['id'], $category['name'], $category['icon']]);
        }
    }

    $stmt = $pdo->query('SELECT COUNT(*) FROM users');
    if ($stmt->fetchColumn() == 0) {
        $insert = $pdo->prepare('INSERT INTO users (id, username, email, password, first_name, last_name, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($users_data as $user) {
            $insert->execute([
                $user['id'],
                $user['email'],
                $user['email'],
                $user['password'],
                $user['first_name'],
                $user['last_name'],
                $user['role'],
                $user['created_at']
            ]);
        }
    }

    $stmt = $pdo->query('SELECT COUNT(*) FROM products');
    if ($stmt->fetchColumn() == 0) {
        $insert = $pdo->prepare('INSERT INTO products (id, name, category_id, price, description, features, image, featured, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($products_data as $product) {
            $insert->execute([
                $product['id'],
                $product['name'],
                $product['category_id'],
                $product['price'],
                $product['description'],
                formatProductFeatures($product['features']),
                $product['image'],
                $product['featured'] ? 1 : 0,
                $product['stock']
            ]);
        }
    }
} catch (Throwable $e) {
    // If the database seed fails, log the error for debugging and continue.
    @file_put_contents(__DIR__ . '/../tmp/db_seed_error.log', date('c') . ' - ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
}
