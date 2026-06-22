<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php?return_url=checkout.php');
    exit;
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Calculate totals
$cart_items = [];
$subtotal = 0;
$error = '';

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = getProductById($product_id);
    if ($product) {
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity
        ];
        $subtotal += $product['price'] * $quantity;
    }
}

$shipping = $subtotal >= 150 ? 0 : 15;
$total = $subtotal + $shipping;

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $error = 'Invalid CSRF token.';
    } else {
        $order_id = createOrder($_POST, $cart_items, $total);
        if ($order_id) {
            // Clear cart
            $_SESSION['cart'] = [];
            $_SESSION['order_success'] = $order_id;
            header('Location: order-success.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Holo Electronics</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="checkout-page">
            <div class="container">
                <h1>Checkout</h1>

                <div class="checkout-layout">
                    <div class="checkout-form">
                        <form action="checkout.php" method="POST">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                            <div class="form-section">
                                <h3>Contact Information</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="tel" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>Shipping Address</h3>
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" id="address" name="address" required>
                                </div>
                                <div class="form-row three-col">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" id="city" name="city" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="state">State</label>
                                        <input type="text" id="state" name="state" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="zip">ZIP Code</label>
                                        <input type="text" id="zip" name="zip" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>Payment Method</h3>
                                <div class="payment-methods">
                                    <label class="payment-method">
                                        <input type="radio" name="payment_method" value="card" checked>
                                        <span class="method-content">
                                            <span class="method-icon">💳</span>
                                            <span>Credit Card</span>
                                        </span>
                                    </label>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="card_number">Card Number</label>
                                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                                    </div>
                                </div>
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="expiry">Expiry Date</label>
                                        <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cvv">CVV</label>
                                        <input type="text" id="cvv" name="cvv" placeholder="123" required>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-primary btn-large btn-full">Complete Order</button>
                        </form>
                    </div>

                    <div class="checkout-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <div class="item-info">
                                    <div class="item-image">
                                        <img src="<?php echo htmlspecialchars($item['product']['image']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="product-img-tiny">
                                    </div>
                                    <div class="item-details">
                                        <p class="item-name"><?php echo $item['product']['name']; ?></p>
                                        <p class="item-quantity">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                </div>
                                <p class="item-price"><?php echo number_format($item['product']['price'] * $item['quantity'], 2); ?> DT</p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="summary-totals">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span><?php echo number_format($subtotal, 2); ?> DT</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span><?php echo $shipping === 0 ? 'Free' : number_format($shipping, 2) . ' DT'; ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span><?php echo number_format($total, 2); ?> DT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
