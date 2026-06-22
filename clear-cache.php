<?php
/**
 * Clear Session Cache
 * Run this to reset the session and reload all product images
 */

// This application now stores products in the database; there is no session product cache to clear.
$message = "Cache cleared (database-backed). If you need to regenerate product images, run: php scripts/fetch_photos.php from the project root.";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Cache | Holo</title>
    <style>
        body { font-family: sans-serif; padding: 50px; text-align: center; }
        .btn { padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; }
        .success { color: #059669; background: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Cache Cleared!</h1>
    <div class="success"><?php echo $message; ?></div>
    <p><a href="index.php" class="btn">Go to Store</a></p>
</body>
</html>
