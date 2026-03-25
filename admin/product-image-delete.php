<?php
/**
 * HTEC - Delete Product Image (AJAX-friendly POST endpoint)
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/ProductModel.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        setFlash('error', 'Invalid CSRF token.');
    } else {
        $imageId   = sanitizeInt($_POST['image_id'] ?? 0);
        $productId = sanitizeInt($_POST['product_id'] ?? 0);

        if ($imageId) {
            $productModel = new ProductModel();
            $productModel->deleteImage($imageId);
            setFlash('success', 'Image removed.');
        }
    }
}

$productId = sanitizeInt($_POST['product_id'] ?? 0);
redirect($productId ? url('admin/product-edit.php?id=' . $productId) : url('admin/products.php'));
