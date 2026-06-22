<?php
require_once __DIR__ . '/functions.php';
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$is_logged_in = isLoggedIn();
$is_admin = isAdmin();
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
?>
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">◯</span>
                <span class="logo-text">Holo</span>
            </a>

            <nav class="main-nav">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="products.php?category=1">Phones</a>
                <a href="products.php?category=2">Laptops</a>
                <?php if ($is_admin): ?>
                <a href="admin/dashboard.php" class="admin-link">Admin</a>
                <?php endif; ?>
            </nav>

            <div class="header-actions">
                <a href="cart.php" class="cart-link">
                    <span class="cart-icon">🛒</span>
                    <?php if ($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($is_logged_in): ?>
                <div class="user-menu">
                    <button class="user-menu-toggle" onclick="toggleUserMenu()">
                        <span class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></span>
                        <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <?php if ($is_admin): ?>
                        <a href="admin/dashboard.php">
                            <span>⚙️</span> Admin Dashboard
                        </a>
                        <?php endif; ?>
                        <a href="index.php">
                            <span>🏠</span> Home
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="logout-link">
                            <span>🚪</span> Sign Out
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="auth-links">
                    <a href="login.php" class="btn btn-secondary btn-sm">Sign In</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Sign Up</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.querySelector('.user-menu');
    if (userMenu && !userMenu.contains(e.target)) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) dropdown.classList.remove('show');
    }
});
</script>
