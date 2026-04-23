<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'HTEC - Industrial Technology Solutions') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'HTEC delivers cutting-edge industrial automation, power systems, and IIoT solutions.') ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Tailwind (self-hosted build) -->
    <link rel="stylesheet" href="<?= url('assets/css/tailwind.generated.css') ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
    <?= $extraHead ?? '' ?>
</head>
<body class="bg-htec-dark text-white font-body antialiased">

<!-- ===== NAVBAR ===== -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="<?= url() ?>" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-htec-red flex items-center justify-center font-display font-800 text-white text-sm tracking-wider">HT</div>
                <span class="font-display font-700 text-xl tracking-widest text-white group-hover:text-htec-red transition-colors">HTEC</span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-8">
                <a href="<?= url() ?>" class="nav-link <?= ($activePage ?? '') === 'home' ? 'active' : '' ?>">Home</a>
                <a href="<?= url('products.php') ?>" class="nav-link <?= ($activePage ?? '') === 'products' ? 'active' : '' ?>">Products</a>
                <a href="<?= url('services.php') ?>" class="nav-link <?= ($activePage ?? '') === 'services' ? 'active' : '' ?>">Services</a>
                <a href="<?= url('portfolio.php') ?>" class="nav-link <?= ($activePage ?? '') === 'portfolio' ? 'active' : '' ?>">Portfolio</a>
                <a href="<?= url('contact.php') ?>" class="nav-link <?= ($activePage ?? '') === 'contact' ? 'active' : '' ?>">Contact</a>
            </div>

            <!-- CTA -->
            <div class="hidden md:flex items-center gap-4">
                <a href="<?= url('contact.php') ?>" class="btn-primary text-sm px-6 py-2.5">Contact Us</a>
            </div>

            <!-- Mobile hamburger -->
            <button id="menu-btn" type="button" class="md:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5 group"
                aria-controls="mobile-menu" aria-expanded="false" aria-label="Open menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line w-4"></span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden"></div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 right-0 bg-htec-gray border-t border-htec-border z-50">
        <div class="px-6 py-6 flex flex-col gap-4">
            <a href="<?= url() ?>" class="mobile-nav-link">Home</a>
            <a href="<?= url('products.php') ?>" class="mobile-nav-link">Products</a>
            <a href="<?= url('services.php') ?>" class="mobile-nav-link">Services</a>
            <a href="<?= url('portfolio.php') ?>" class="mobile-nav-link">Portfolio</a>
            <a href="<?= url('contact.php') ?>" class="mobile-nav-link">Contact</a>
            <a href="<?= url('contact.php') ?>" class="btn-primary text-center mt-2">Contact Us</a>
        </div>
    </div>
</nav>

<!-- Flash Message -->
<div class="fixed top-24 right-6 z-50 w-80">
    <?= renderFlash() ?>
</div>

<!-- Page Content -->
<main>
