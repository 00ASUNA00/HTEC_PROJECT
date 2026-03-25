<?php
/**
 * HTEC - Admin Logout
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/OtherModels.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    http_response_code(405);
    exit('Method Not Allowed');
}

if (isLoggedIn()) {
    $userModel = new UserModel();
    $userModel->logout();
}

setFlash('success', 'You have been signed out.');
redirect(url('admin/login.php'));
