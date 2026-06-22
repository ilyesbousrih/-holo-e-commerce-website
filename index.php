<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$products = getFeaturedProducts();
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holo | Premium Electronics Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Discover the Future of Technology</h1>
                    <p>Premium electronics for the modern lifestyle. From smartphones to smart homes.</p>
                    <a href="products.php" class="btn btn-primary">Shop Now</a>
                </div>
                <div class="hero-image">
                    <img src="photos/hero.jpg" alt="Modern Technology" class="hero-img">
                </div>
            </div>
        </section>

        <section class="categories">
            <div class="container">
                <h2>Shop by Category</h2>
                <div class="category-grid">
                    <?php foreach ($categories as $category): ?>
                    <a href="products.php?category=<?php echo $category['id']; ?>" class="category-card">
                        <div class="category-icon"><?php echo $category['icon']; ?></div>
                        <h3><?php echo $category['name']; ?></h3>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="featured-products">
            <div class="container">
                <h2>Featured Products</h2>
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
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="features-grid">
                    <div class="feature">
                        <div class="feature-icon">🚚</div>
                        <h3>Free Shipping</h3>
                        <p>On orders over 150 DT</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🔒</div>
                        <h3>Secure Payment</h3>
                        <p>100% secure checkout</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">↩️</div>
                        <h3>Easy Returns</h3>
                        <p>30-day return policy</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🎧</div>
                        <h3>24/7 Support</h3>
                        <p>Dedicated support team</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
