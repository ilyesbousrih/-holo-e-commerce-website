<?php
require_once 'db.php';

// Authentication helpers
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    if (!isset($_SESSION['user_id'])) return false;
    if (isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'] === 'admin';
    }
    $user = getUserById($_SESSION['user_id']);
    return $user && isset($user['role']) && $user['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header('Location: login.php');
        exit;
    }
}

// Logout helper
function logoutUser() {
    // Unset all session values
    $_SESSION = [];

    // Delete the session cookie if set
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'] ?? '/',
            $params['domain'] ?? '',
            $params['secure'] ?? false,
            $params['httponly'] ?? true
        );
    }

    // Finally destroy the session
    if (session_status() !== PHP_SESSION_NONE) {
        session_destroy();
    }
}

function getCategories() {
    global $categories_data;
    return $categories_data;
}

function normalizeProducts(array $products): array {
    $seen = [];
    $out = [];
    foreach ($products as $p) {
        $id = $p['id'] ?? null;
        if ($id === null) continue;
        if (isset($seen[$id])) continue; // skip duplicates
        $seen[$id] = true;

        // Ensure image exists for local photos/
        if (!isset($p['image']) || $p['image'] === '') {
            $p['image'] = 'photos/default.jpg';
        } else {
            $image = $p['image'];
            if (str_starts_with($image, 'photos/')) {
                $image_path = __DIR__ . '/../' . $image;
                if (!file_exists($image_path)) {
                    $p['image'] = 'photos/default.jpg';
                }
            }
        }

        $out[] = $p;
    }
    return $out;
}

function getCategoryById($id) {
    global $categories_data;
    foreach ($categories_data as $category) {
        if ($category['id'] == $id) {
            return $category;
        }
    }
    return null;
}

function hydrateProduct(array $product): array {
    $category = getCategoryById($product['category_id']);
    $product['category_name'] = $category ? $category['name'] : 'Unknown';

    if (isset($product['features']) && !is_array($product['features'])) {
        $product['features'] = array_values(array_filter(array_map('trim', preg_split("/\r\n|\r|\n/", $product['features'])), fn($item) => $item !== ''));
    }

    if (!isset($product['features']) || !is_array($product['features'])) {
        $product['features'] = [];
    }

    if (!isset($product['image']) || $product['image'] === '') {
        $product['image'] = 'photos/default.jpg';
    }

    return $product;
}

function getAllProducts() {
    $pdo = getDb();
    $stmt = $pdo->query('SELECT * FROM products ORDER BY id ASC');
    $products = $stmt->fetchAll();
    return array_map('hydrateProduct', $products);
}

function getProducts($category_id = null, $search = null) {
    $pdo = getDb();
    $query = 'SELECT * FROM products';
    $conditions = [];
    $params = [];

    if ($category_id) {
        $conditions[] = 'category_id = ?';
        $params[] = (int)$category_id;
    }

    if ($search) {
        $conditions[] = '(name LIKE ? OR description LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY id ASC';
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $products = $stmt->fetchAll();
    return array_map('hydrateProduct', $products);
}

function getFeaturedProducts() {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE featured = 1 ORDER BY id ASC');
    $stmt->execute();
    $products = $stmt->fetchAll();
    return array_map('hydrateProduct', $products);
}

function getProductById($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$id]);
    $product = $stmt->fetch();
    return $product ? hydrateProduct($product) : null;
}

function getRelatedProducts($category_id, $exclude_id, $limit = 4) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY id ASC LIMIT ?');
    $stmt->bindValue(1, (int)$category_id, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$exclude_id, PDO::PARAM_INT);
    $stmt->bindValue(3, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();
    return array_map('hydrateProduct', $products);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// CSRF helpers
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function createOrder($data, $cart_items, $total) {
    $pdo = getDb();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $stmt = $pdo->prepare('INSERT INTO orders (user_id, total, status, full_name, email, phone, address, city, state, zip, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $user_id,
        $total,
        'pending',
        sanitize($data['full_name']),
        sanitize($data['email']),
        sanitize($data['phone']),
        sanitize($data['address']),
        sanitize($data['city']),
        sanitize($data['state']),
        sanitize($data['zip']),
        sanitize($data['payment_method'])
    ]);

    $order_id = $pdo->lastInsertId();
    $item_stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
    foreach ($cart_items as $item) {
        $item_stmt->execute([
            $order_id,
            $item['product']['id'],
            $item['quantity'],
            $item['product']['price'],
        ]);
    }

    return $order_id;
}

function getOrderItems($order_id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT oi.quantity, oi.price, p.id AS product_id, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
    $stmt->execute([(int)$order_id]);

    $items = [];
    while ($row = $stmt->fetch()) {
        $items[] = [
            'product' => [
                'id' => $row['product_id'],
                'name' => $row['name'],
                'image' => $row['image'],
            ],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
        ];
    }
    return $items;
}

function getAllOrders() {
    $pdo = getDb();
    $stmt = $pdo->query('SELECT * FROM orders ORDER BY id ASC');
    $orders = $stmt->fetchAll();
    foreach ($orders as &$order) {
        $order['items'] = getOrderItems($order['id']);
    }
    return $orders;
}

function getOrderById($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$id]);
    $order = $stmt->fetch();
    if (!$order) {
        return null;
    }
    $order['items'] = getOrderItems($order['id']);
    return $order;
}

function updateOrderStatus($order_id, $status) {
    $pdo = getDb();
    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    return $stmt->execute([sanitize($status), (int)$order_id]);
}

function deleteProduct($product_id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    return $stmt->execute([(int)$product_id]);
}

function parseFeaturesFromTextarea($value) {
    $lines = preg_split("/\r\n|\r|\n/", (string)$value);
    $lines = array_values(array_filter(array_map('trim', $lines), fn($l) => $l !== ''));
    return array_map('sanitize', $lines);
}

function addProduct($data) {
    $pdo = getDb();
    $features = formatProductFeatures(parseFeaturesFromTextarea($data['features'] ?? ''));

    $stmt = $pdo->prepare('INSERT INTO products (name, category_id, price, description, features, image, featured, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $result = $stmt->execute([
        sanitize($data['name']),
        (int)$data['category_id'],
        (float)$data['price'],
        sanitize($data['description']),
        $features,
        sanitize($data['image']),
        isset($data['featured']) ? 1 : 0,
        (int)$data['stock']
    ]);

    return $result ? intval(getDb()->lastInsertId()) : false;
}

function updateProduct($product_id, $data) {
    $pdo = getDb();
    $features = formatProductFeatures(parseFeaturesFromTextarea($data['features'] ?? ''));

    $stmt = $pdo->prepare('UPDATE products SET name = ?, category_id = ?, price = ?, description = ?, features = ?, image = ?, featured = ?, stock = ? WHERE id = ?');
    return $stmt->execute([
        sanitize($data['name']),
        (int)$data['category_id'],
        (float)$data['price'],
        sanitize($data['description']),
        $features,
        sanitize($data['image']),
        isset($data['featured']) ? 1 : 0,
        (int)$data['stock'],
        (int)$product_id
    ]);
}

function getDashboardStats() {
    $pdo = getDb();

    $total_products = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $total_orders = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $total_revenue = (float)$pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders')->fetchColumn();
    $total_users = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $total_customers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    $pending_orders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

    return [
        'total_products' => $total_products,
        'total_orders' => $total_orders,
        'total_revenue' => $total_revenue,
        'total_users' => $total_users,
        'total_customers' => $total_customers,
        'pending_orders' => $pending_orders
    ];
}

// User Management Functions
function getAllUsers() {
    $pdo = getDb();
    $stmt = $pdo->query('SELECT * FROM users ORDER BY id ASC');
    return $stmt->fetchAll();
}

function getUserById($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

function deleteUser($user_id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    return $stmt->execute([(int)$user_id]);
}

function updateUserRole($user_id, $new_role) {
    $pdo = getDb();
    $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
    return $stmt->execute([sanitize($new_role), (int)$user_id]);
}

function adminUpdateUser($user_id, $data) {
    $pdo = getDb();
    $fields = [
        sanitize($data['first_name']),
        sanitize($data['last_name']),
        sanitize($data['email']),
        sanitize($data['role']),
        (int)$user_id
    ];

    $sql = 'UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?';
    if (!empty($data['password'])) {
        $sql .= ', password = ?';
        $fields[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    $sql .= ' WHERE id = ?';

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($fields);
}

function adminCreateUser($data) {
    if (getUserByEmail($data['email'])) {
        return ['success' => false, 'message' => 'Email already registered'];
    }

    $pdo = getDb();
    $username = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower(strtok($data['email'], '@')));
    if ($username === '') {
        $username = 'user';
    }
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username LIKE ?');
    $stmt->execute([$username . '%']);
    $count = (int)$stmt->fetchColumn();
    if ($count > 0) {
        $username .= $count + 1;
    }

    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)');
    $result = $stmt->execute([
        $username,
        sanitize($data['email']),
        password_hash($data['password'], PASSWORD_DEFAULT),
        sanitize($data['first_name']),
        sanitize($data['last_name']),
        sanitize($data['role'])
    ]);

    return ['success' => $result, 'user' => $result ? getUserById($pdo->lastInsertId()) : null];
}

function getUserByEmail($email) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([sanitize($email)]);
    return $stmt->fetch();
}

function registerUser($email, $password, $first_name, $last_name) {
    if (getUserByEmail($email)) {
        return ['success' => false, 'message' => 'Email already registered.'];
    }

    $pdo = getDb();
    $username = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower(strtok($email, '@')));
    if ($username === '') {
        $username = 'user';
    }
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username LIKE ?');
    $stmt->execute([$username . '%']);
    $count = (int)$stmt->fetchColumn();
    if ($count > 0) {
        $username .= $count + 1;
    }

    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)');
    $result = $stmt->execute([
        $username,
        sanitize($email),
        password_hash($password, PASSWORD_DEFAULT),
        sanitize($first_name),
        sanitize($last_name),
        'customer'
    ]);

    return ['success' => $result, 'user' => $result ? getUserById($pdo->lastInsertId()) : null];
}

function loginUser($email, $password) {
    $user = getUserByEmail($email);
    if (!$user) {
        return ['success' => false, 'message' => 'Email or password is incorrect.'];
    }

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Email or password is incorrect.'];
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);

    return ['success' => true, 'user' => $user];
}

