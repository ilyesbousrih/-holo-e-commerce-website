<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$stats = getDashboardStats();
$recent_orders = array_slice(getAllOrders(), -5);
$recent_orders = array_reverse($recent_orders);

// Get products from session
$products = getAllProducts();
$low_stock = array_filter($products, function($p) { return $p['stock'] < 10; });
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Holo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 260px;
            background: #1a1a2e;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .admin-logo {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .admin-logo a {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        .admin-nav {
            padding: 1rem 0;
        }
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .admin-nav a span {
            font-size: 1.25rem;
        }
        .admin-main {
            flex: 1;
            margin-left: 260px;
            background: #f8fafc;
        }
        .admin-header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1a2e;
        }
        .admin-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .admin-content {
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .stat-card.primary .icon { background: #dbeafe; }
        .stat-card.success .icon { background: #d1fae5; }
        .stat-card.warning .icon { background: #fef3c7; }
        .stat-card.danger .icon { background: #fee2e2; }
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.25rem;
        }
        .stat-card p {
            color: #6b7280;
            font-size: 0.875rem;
        }
        .admin-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .section-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a2e;
        }
        .btn {
            padding: 0.625rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5a67d8;
        }
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 0.875rem;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        td {
            color: #374151;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        .stock-low {
            color: #dc2626;
            font-weight: 600;
        }
        .logout-btn {
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.875rem;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <a href="../index.php">◯ Holo Admin</a>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php" class="active">
                    <span>📊</span> Dashboard
                </a>
                <a href="products.php">
                    <span>📦</span> Products
                </a>
                <a href="orders.php">
                    <span>🛒</span> Orders
                </a>
                <a href="users.php">
                    <span>👥</span> Users
                </a>
                <a href="../index.php">
                    <span>🏪</span> Store
                </a>
            </nav>
            <div style="padding: 1rem 1.5rem; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);">
                <p style="font-size: 0.875rem; color: rgba(255,255,255,0.5); margin-bottom: 0.5rem;">Logged in as</p>
                <p style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <a href="../logout.php" class="logout-btn" style="display: inline-block; margin-top: 0.75rem;">Sign Out</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard Overview</h1>
                <div class="admin-user">
                    <span style="color: #6b7280;"><?php echo date('F j, Y'); ?></span>
                </div>
            </header>

            <div class="admin-content">
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="icon">💰</div>
                        <h3><?php echo number_format($stats['total_revenue'], 2); ?> DT</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="stat-card success">
                        <div class="icon">🛒</div>
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="stat-card warning">
                        <div class="icon">📦</div>
                        <h3><?php echo $stats['total_products']; ?></h3>
                        <p>Products</p>
                    </div>
                    <div class="stat-card danger">
                        <div class="icon">👥</div>
                        <h3><?php echo $stats['total_users']; ?></h3>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-card danger" style="background: #ffe4e6;">
                        <div class="icon">🧑‍🤝‍🧑</div>
                        <h3><?php echo $stats['total_customers']; ?></h3>
                        <p>Customers</p>
                    </div>
                </div>

                <div class="admin-section">
                    <div class="section-header">
                        <h2>Recent Orders</h2>
                        <a href="orders.php" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    <?php if (empty($recent_orders)): ?>
                        <p style="color: #6b7280; text-align: center; padding: 2rem;">No orders yet</p>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo count($order['items']); ?> items</td>
                                <td><?php echo number_format($order['total'], 2); ?> DT</td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($order['date'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <?php if (!empty($low_stock)): ?>
                <div class="admin-section">
                    <div class="section-header">
                        <h2>⚠️ Low Stock Alert</h2>
                        <a href="products.php" class="btn btn-primary btn-sm">Manage Products</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($low_stock as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td class="stock-low"><?php echo $product['stock']; ?> remaining</td>
                                <td><a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm" style="background: #fef3c7; color: #92400e;">Restock</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
