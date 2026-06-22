<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';

// Handle status update
if (isset($_POST['update_status'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $message = 'Invalid CSRF token.';
    } else {
        $order_id = (int)$_POST['order_id'];
        $status = sanitize($_POST['status']);
        if (updateOrderStatus($order_id, $status)) {
            $message = 'Order status updated!';
        }
    }
}

$orders = getAllOrders();
$orders = array_reverse($orders); // Show newest first

$statuses = ['pending', 'processing', 'completed', 'cancelled'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin - Holo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #1a1a2e; color: white; position: fixed; height: 100vh; overflow-y: auto; }
        .admin-logo { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-logo a { color: white; font-size: 1.5rem; font-weight: 700; text-decoration: none; }
        .admin-nav { padding: 1rem 0; }
        .admin-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.7); text-decoration: none; transition: all 0.2s; }
        .admin-nav a:hover, .admin-nav a.active { background: rgba(255,255,255,0.1); color: white; }
        .admin-main { flex: 1; margin-left: 260px; background: #f8fafc; }
        .admin-header { background: white; padding: 1rem 2rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .admin-content { padding: 2rem; }
        .admin-section { background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn { padding: 0.625rem 1rem; border-radius: 8px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #667eea; color: white; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.875rem; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: 600; color: #6b7280; font-size: 0.875rem; text-transform: uppercase; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #d1fae5; color: #059669; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.show { display: flex; }
        .modal { background: white; border-radius: 12px; padding: 2rem; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; }
        .order-details h3 { margin: 1.5rem 0 0.75rem; font-size: 1.1rem; }
        .order-item { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; }
        .logout-btn { padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo"><a href="../index.php">◯ Holo Admin</a></div>
            <nav class="admin-nav">
                <a href="dashboard.php"><span>📊</span> Dashboard</a>
                <a href="products.php"><span>📦</span> Products</a>
                <a href="orders.php" class="active"><span>🛒</span> Orders</a>
                <a href="users.php"><span>👥</span> Users</a>
                <a href="../index.php"><span>🏪</span> Store</a>
            </nav>
            <div style="padding: 1rem 1.5rem; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);">
                <p style="font-size: 0.875rem; color: rgba(255,255,255,0.5);">Logged in as</p>
                <p style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <a href="../logout.php" class="logout-btn" style="display: inline-block; margin-top: 0.75rem;">Sign Out</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Orders</h1>
            </header>

            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="admin-section">
                    <?php if (empty($orders)): ?>
                        <p style="color: #6b7280; text-align: center; padding: 3rem;">No orders yet</p>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['email']); ?></td>
                                <td><?php echo number_format($order['total'], 2); ?> DT</td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y g:i A', strtotime($order['date'])); ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" data-order="<?php echo htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8'); ?>" onclick="viewOrder(this)">View</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Order Details Modal -->
    <div class="modal-overlay" id="orderModal">
        <div class="modal">
            <div class="order-details" id="orderDetails">
                <!-- Content populated by JS -->
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function viewOrder(button) {
            const order = JSON.parse(button.dataset.order);
            const modal = document.getElementById('orderModal');
            const details = document.getElementById('orderDetails');

            let itemsHtml = (order.items || []).map(item => {
                const product = item.product || item;
                const name = product.name || 'Item';
                const price = Number(product.price ?? item.price ?? 0);
                const quantity = Number(item.quantity ?? 0);
                return `
                    <div class="order-item">
                        <span>${name} (Qty: ${quantity})</span>
                        <span>${(price * quantity).toFixed(2)} DT</span>
                    </div>
                `;
            }).join('');

            const statusOptions = ['pending', 'processing', 'completed', 'cancelled']
                .map(s => `<option value="${s}" ${order.status === s ? 'selected' : ''}>${s.charAt(0).toUpperCase() + s.slice(1)}</option>`)
                .join('');

            details.innerHTML = `
                <h2 style="margin-bottom: 1rem;">Order #${order.id}</h2>

                <form method="POST" action="orders.php" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <input type="hidden" name="order_id" value="${order.id}">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <label style="font-weight: 500;">Status:</label>
                        <select name="status" style="padding: 0.375rem 0.75rem; border-radius: 6px; border: 1px solid #e5e7eb;">
                            ${statusOptions}
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </form>

                <h3>Customer Information</h3>
                <p><strong>Name:</strong> ${order.full_name}</p>
                <p><strong>Email:</strong> ${order.email}</p>
                <p><strong>Phone:</strong> ${order.phone}</p>

                <h3>Shipping Address</h3>
                <p>${order.address}</p>
                <p>${order.city}, ${order.state} ${order.zip}</p>

                <h3>Order Items</h3>
                ${itemsHtml}
                <div class="order-item" style="font-weight: 600; font-size: 1.1rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 2px solid #e5e7eb;">
                    <span>Total</span>
                    <span>${Number(order.total).toFixed(2)} DT</span>
                </div>

                <h3>Payment</h3>
                <p>Method: ${order.payment_method.replace('_', ' ').toUpperCase()}</p>

                <h3>Order Date</h3>
                <p>${new Date(order.date).toLocaleString()}</p>
            `;

            modal.classList.add('show');
        }

        function closeModal() {
            document.getElementById('orderModal').classList.remove('show');
        }
    </script>
</body>
</html>
