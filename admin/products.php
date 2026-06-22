<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';
$error = '';
$categories = getCategories();

// Handle delete (POST with CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $product_id = (int)$_POST['delete'];
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $error = 'Invalid CSRF token.';
    } else {
        if (deleteProduct($product_id)) {
            $message = 'Product deleted successfully!';
        } else {
            $error = 'Failed to delete product.';
        }
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $csrf = $_POST['csrf_token'] ?? '';
        if (!verifyCsrfToken($csrf)) {
            $error = 'Invalid CSRF token.';
        } elseif ($_POST['action'] === 'add') {
            $product_id = addProduct($_POST);
            if ($product_id) {
                $message = 'Product added successfully!';
            } else {
                $error = 'Failed to add product.';
            }
        } elseif ($_POST['action'] === 'edit' && isset($_POST['product_id'])) {
            if (updateProduct($_POST['product_id'], $_POST)) {
                $message = 'Product updated successfully!';
            } else {
                $error = 'Failed to update product.';
            }
        }
    }
}

$products = getAllProducts();
$edit_product = null;

if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    foreach ($products as $p) {
        if ($p['id'] == $edit_id) {
            $edit_product = $p;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin - Holo</title>
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
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .btn { padding: 0.625rem 1rem; border-radius: 8px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #667eea; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.875rem; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: 600; color: #6b7280; font-size: 0.875rem; text-transform: uppercase; }
        .product-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #d1fae5; color: #059669; }
        .alert-error { background: #fee2e2; color: #dc2626; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.show { display: flex; }
        .modal { background: white; border-radius: 12px; padding: 2rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.625rem; border: 1px solid #e5e7eb; border-radius: 6px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .stock-low { color: #dc2626; font-weight: 600; }
        .stock-ok { color: #059669; }
        .logout-btn { padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo"><a href="../index.php">◯ Holo Admin</a></div>
            <nav class="admin-nav">
                <a href="dashboard.php"><span>📊</span> Dashboard</a>
                <a href="products.php" class="active"><span>📦</span> Products</a>
                <a href="orders.php"><span>🛒</span> Orders</a>
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
                <h1>Products</h1>
                <button class="btn btn-primary" onclick="openModal()">+ Add Product</button>
            </header>

            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="admin-section">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product):
                                $category = getCategoryById($product['category_id']);
                            ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" class="product-thumb" onerror="this.onerror=null;this.src='../photos/default.jpg';"></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo $category ? $category['name'] : 'N/A'; ?></td>
                                <td><?php echo number_format($product['price'], 2); ?> DT</td>
                                <td class="<?php echo $product['stock'] < 10 ? 'stock-low' : 'stock-ok'; ?>"><?php echo $product['stock']; ?></td>
                                <td><?php echo $product['featured'] ? '✓' : '-'; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $product['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="delete" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal-overlay" id="productModal">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem;"><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h2>
            <form method="POST" action="products.php">
                <input type="hidden" name="action" value="<?php echo $edit_product ? 'edit' : 'add'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <?php if ($edit_product): ?>
                <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_product && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" name="price" step="0.01" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?php echo $edit_product ? $edit_product['stock'] : '10'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Featured</label>
                        <input type="checkbox" name="featured" <?php echo ($edit_product && $edit_product['featured']) ? 'checked' : ''; ?>> Mark as featured
                    </div>
                </div>

                <div class="form-group">
                    <label>Image Path / URL</label>
                    <input type="text" name="image" value="<?php echo $edit_product ? htmlspecialchars($edit_product['image']) : ''; ?>" placeholder="photos/product-1.jpg" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" required><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Features (one per line)</label>
                    <textarea name="features" rows="4" placeholder="Feature 1&#10;Feature 2&#10;Feature 3" required><?php echo $edit_product ? htmlspecialchars(implode("\n", $edit_product['features'])) : ''; ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_product ? 'Update' : 'Add'; ?> Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('productModal').classList.add('show');
        }
        function closeModal() {
            document.getElementById('productModal').classList.remove('show');
        }
        <?php if ($edit_product): ?>
        openModal();
        <?php endif; ?>
    </script>
</body>
</html>
