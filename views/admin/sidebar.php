<?php
/**
 * HTEC Admin - Sidebar Component
 * Included by admin pages
 */
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/OtherModels.php';

requireLogin();

$contactModel = new ContactModel();
$unreadCount  = $contactModel->countUnread();
$currentUser  = getCurrentUser();

// Determine active admin page
$adminActivePage = $adminActivePage ?? '';
?>
<!-- Sidebar Overlay (mobile) -->
<div
    id="sidebar-overlay"
    class="fixed inset-0 bg-black/60 z-40 lg:hidden"
    aria-hidden="true"
    style="display:none;pointer-events:none;"
></div>

<!-- Sidebar -->
<aside id="admin-sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">HT</div>
        <div>
            <div class="sidebar-logo-text">HTEC</div>
            <div class="sidebar-logo-sub">Admin Panel</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="sidebar-section">Main</div>
        <a href="<?= url('admin/') ?>" class="sidebar-link <?= $adminActivePage === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        
        <div class="sidebar-section">Products</div>
        <a href="<?= url('admin/products.php') ?>" class="sidebar-link <?= $adminActivePage === 'products' ? 'active' : '' ?>">
            <i class="fas fa-box"></i> All Products
        </a>
        <a href="<?= url('admin/product-edit.php') ?>" class="sidebar-link <?= $adminActivePage === 'product-add' ? 'active' : '' ?>">
            <i class="fas fa-plus"></i> Add Product
        </a>

        <div class="sidebar-section">Content</div>
        <a href="<?= url('admin/portfolio.php') ?>" class="sidebar-link <?= $adminActivePage === 'portfolio' ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i> Portfolio
        </a>
        <a href="<?= url('admin/contacts.php') ?>" class="sidebar-link <?= $adminActivePage === 'contacts' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> Messages
            <?php if ($unreadCount > 0): ?>
                <span class="sidebar-badge"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a>
        
        <div class="sidebar-section">System</div>
        <a href="<?= url() ?>" target="_blank" class="sidebar-link">
            <i class="fas fa-external-link-alt"></i> View Site
        </a>
        <form method="POST" action="<?= url('admin/logout.php') ?>">
            <?= csrfField() ?>
            <button type="submit" class="sidebar-link text-red-400 hover:text-red-300 w-full text-left">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>

    <!-- User info -->
    <div class="sidebar-footer">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-htec-red flex items-center justify-center text-xs font-700">
                <?= strtoupper(substr($currentUser['username'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div class="text-white text-xs font-500"><?= htmlspecialchars($currentUser['username'] ?? '') ?></div>
                <div class="text-xs text-htec-text"><?= ucfirst($currentUser['role'] ?? '') ?></div>
            </div>
        </div>
    </div>
</aside>
