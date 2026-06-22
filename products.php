<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;

$products = getProducts($category_id, $search);
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Holo Electronics</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="products-page">
            <div class="container">
                <div class="products-header">
                    <h1>All Products</h1>
                    <form class="search-form" method="GET" action="products.php">
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo $search ?? ''; ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>

                <div class="products-layout">
                    <aside class="filters">
                        <h3>Categories</h3>
                        <ul class="filter-list">
                            <li><a href="products.php" class="<?php echo !$category_id ? 'active' : ''; ?>">All Products</a></li>
                            <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="products.php?category=<?php echo $category['id']; ?>" 
                                   class="<?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                                    <?php echo $category['name']; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>

                    <div class="products-content">
                        <?php if (count($products) > 0): ?>
                        <div class="product-grid">
                            <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <a class="product-card-link" href="product.php?id=<?php echo $product['id']; ?>">
                                    <div class="product-image">
                                        <img src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="product-img" loading="lazy" onerror="this.onerror=null;this.src='photos/default.jpg';">
                                    </div>
                                    <div class="product-info">
                                        <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <p class="product-category"><?php echo htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="product-price"><?php echo number_format($product['price'], 2); ?> DT</p>
                                        <div class="product-actions">
                                            <span class="btn btn-secondary">View Details</span>
                                        </div>
                                    </div>
                                </a>
                                <form action="cart.php" method="POST" class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="no-results">
                            <p>No products found.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
