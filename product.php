<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$product_id = intval($_GET['id']);
$product = getProductById($product_id);

if (!$product) {
    header('Location: products.php');
    exit;
}

$related_products = getRelatedProducts($product['category_id'], $product_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> | Holo Electronics</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="product-detail">
            <div class="container">
                <div class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span>/</span>
                    <a href="products.php">Products</a>
                    <span>/</span>
                    <span><?php echo $product['name']; ?></span>
                </div>

                <div class="product-detail-layout">
                    <div class="product-gallery">
                        <div class="main-image">
                            <img src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="product-img-large" loading="lazy" onerror="this.onerror=null;this.src='photos/default.jpg';">
                        </div>
                    </div>

                    <div class="product-info-detail">
                        <span class="product-category-badge"><?php echo $product['category_name']; ?></span>
                        <h1><?php echo $product['name']; ?></h1>
                        <p class="product-price-large"><?php echo number_format($product['price'], 2); ?> DT</p>
                        <p class="product-description"><?php echo $product['description']; ?></p>

                        <div class="product-features-list">
                            <h3>Key Features</h3>
                            <ul>
                                <?php foreach ($product['features'] as $feature): ?>
                                <li><?php echo $feature; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <form action="cart.php" method="POST" class="add-to-cart-detail">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="quantity-selector">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10">
                            </div>
                            <button type="submit" class="btn btn-primary btn-large">Add to Cart</button>
                        </form>
                    </div>
                </div>

                <?php if (count($related_products) > 0): ?>
                <div class="related-products">
                    <h2>Related Products</h2>
                    <div class="product-grid small">
                        <?php foreach ($related_products as $related): ?>
                        <div class="product-card" data-href="product.php?id=<?php echo $related['id']; ?>" role="link" tabindex="0">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" class="product-img" loading="lazy">
                            </div>
                            <div class="product-info">
                                <h3><?php echo $related['name']; ?></h3>
                                <p class="product-price"><?php echo number_format($related['price'], 2); ?> DT</p>
                                <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-secondary">View Details</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
