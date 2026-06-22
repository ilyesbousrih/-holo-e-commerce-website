<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['order_success'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_SESSION['order_success'];
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | Holo Electronics</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="order-success-page">
            <div class="container">
                <div class="success-card">
                    <div class="success-icon">✅</div>
                    <h1>Order Confirmed!</h1>
                    <p class="order-number">Order #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
                    <p class="success-message">Thank you for your purchase! We've sent a confirmation email with your order details.</p>
                    
                    <div class="success-actions">
                        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                        <a href="index.php" class="btn btn-secondary">Back to Home</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
