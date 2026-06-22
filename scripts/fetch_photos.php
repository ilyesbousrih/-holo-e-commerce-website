<?php
// CLI script to fetch external product images and generate placeholders for missing local images.
// Run: php scripts/fetch_photos.php

$root = dirname(__DIR__);
$photosDir = $root . '/photos';
if (!is_dir($photosDir)) {
    mkdir($photosDir, 0755, true);
}

require_once $root . '/includes/functions.php';
// Fetch products from database
$products = getAllProducts();
if (!is_array($products) || count($products) === 0) {
    echo "No products found in database.\n";
    exit(1);
}
$pdo = getDb();

$placeholderBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQCAIAAAD+7Q0kAAAACXBIWXMAAAsTAAALEwEAmpwYAAABG0lEQVR4nO3QMQEAAAgDINc/9K3hB6AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADAJ4RlAAAZn7u0QAAAAASUVORK5CYII='; // simple placeholder PNG (small)
$placeholderData = base64_decode($placeholderBase64);

$downloaded = 0;
$created = 0;
$errors = 0;

foreach ($products as &$p) {
    $id = $p['id'] ?? null;
    if ($id === null) continue;

    $image = $p['image'] ?? '';

    if (preg_match('#^https?://#i', $image)) {
        // try to download
        $ext = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $target = $photosDir . '/product-' . $id . '.' . $ext;

        echo "Downloading product {$id} from {$image}... ";

        // use cURL
        $ch = curl_init($image);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($data !== false && $httpCode >= 200 && $httpCode < 400 && strlen($data) > 100) {
            file_put_contents($target, $data);
            $newPath = 'photos/product-' . $id . '.' . $ext;
            // update DB
            $stmt = $pdo->prepare('UPDATE products SET image = ? WHERE id = ?');
            $stmt->execute([$newPath, $id]);
            echo "saved to {$target}\n";
            $downloaded++;
        } else {
            echo "failed ({$httpCode}) {$err}\n";
            // fallback to placeholder
            $target = $photosDir . '/product-' . $id . '.png';
            file_put_contents($target, $placeholderData);
            $newPath = 'photos/product-' . $id . '.png';
            $stmt = $pdo->prepare('UPDATE products SET image = ? WHERE id = ?');
            $stmt->execute([$newPath, $id]);
            $created++;
        }
    } elseif (strpos($image, 'photos/') === 0) {
        $localPath = $root . '/' . $image;
        if (!file_exists($localPath)) {
            echo "Creating placeholder for missing {$image}... ";
            // ensure directory
            $dir = dirname($localPath);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents($localPath, $placeholderData);
            echo "created\n";
            $created++;
        } else {
            // exists
        }
    } else {
        // unknown scheme, create placeholder
        $target = $photosDir . '/product-' . $id . '.png';
        if (!file_exists($target)) {
            file_put_contents($target, $placeholderData);
            $newPath = 'photos/product-' . $id . '.png';
            $stmt = $pdo->prepare('UPDATE products SET image = ? WHERE id = ?');
            $stmt->execute([$newPath, $id]);
            $created++;
            echo "Created placeholder for product {$id}\n";
        }
    }
}

echo "\nSummary:\nDownloaded: {$downloaded}\nPlaceholders created: {$created}\nErrors: {$errors}\n";
echo "Note: Run this script from project root: php scripts/fetch_photos.php\n";

exit(0);
