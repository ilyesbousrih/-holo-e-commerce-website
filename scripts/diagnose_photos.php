<?php
$root = dirname(__DIR__);
require $root . '/includes/functions.php';
$products = getAllProducts();
foreach ($products as $p) {
    $img = $p['image'] ?? '';
    $exists = preg_match('/^photos\//', $img) && file_exists($root . '/' . $img) ? 'yes' : 'no';
    echo ($p['id'] ?? 'n/a') . ' => ' . $img . ' => ' . $exists . "\n";
}
