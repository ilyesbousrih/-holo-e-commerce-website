<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $_SESSION['message'] = 'Invalid CSRF token.';
        header('Location: cart.php');
        exit;
    }

    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($_POST['action'] === 'add') {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        $_SESSION['message'] = 'Product added to cart!';
        header('Location: cart.php');
        exit;
    }

    if ($_POST['action'] === 'update') {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        header('Location: cart.php');
        exit;
    }

    if ($_POST['action'] === 'remove') {
        unset($_SESSION['cart'][$product_id]);
        header('Location: cart.php');
        exit;
    }
}

$cart_items = [];
$total = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = getProductById($product_id);
    if ($product) {
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $product['price'] * $quantity
        ];
        $total += $product['price'] * $quantity;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Holo Electronics</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="cart-page">
            <div class="container">
                <h1>Shopping Cart</h1>

                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
                <?php endif; ?>

                <?php if (count($cart_items) > 0): ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="<?php echo htmlspecialchars($item['product']['image']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="product-img-thumb">
                            </div>
                            <div class="cart-item-details">
                                <h3><?php echo $item['product']['name']; ?></h3>
                                <p class="item-price"><?php echo number_format($item['product']['price'], 2); ?> DT</p>
                            </div>
                            <form action="cart.php" method="POST" class="cart-item-quantity">
                                <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" max="10">
                                <button type="submit" class="btn btn-small">Update</button>
                            </form>
                            <p class="cart-item-subtotal"><?php echo number_format($item['subtotal'], 2); ?> DT</p>
                            <form action="cart.php" method="POST" class="cart-item-remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-icon">🗑️</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo number_format($total, 2); ?> DT</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span><?php echo $total >= 150 ? 'Free' : '15.00 DT'; ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span><?php echo number_format($total >= 150 ? $total : $total + 15, 2); ?> DT</span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary btn-large btn-full">Proceed to Checkout</a>
                        <a href="products.php" class="btn btn-secondary btn-full">Continue Shopping</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="cart-empty">
                    <div class="empty-icon">🛒</div>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any products to your cart yet.</p>
                    <a href="products.php" class="btn btn-primary">Start Shopping</a>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
