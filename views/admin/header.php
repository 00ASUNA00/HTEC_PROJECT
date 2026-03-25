<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($adminTitle ?? 'Admin') ?> — HTEC Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    htec: { red: '#E31837', dark: '#0A0A0A', gray: '#111111', mid: '#1A1A1A', border: '#2A2A2A', text: '#A0A0A0' }
                },
                fontFamily: { display: ['Syne', 'sans-serif'], body: ['DM Sans', 'sans-serif'] }
            }
        }
    }
    </script>
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head>
<body class="bg-htec-dark text-white font-body antialiased">
<?php include __DIR__ . '/sidebar.php'; ?>
<div id="admin-main">
    <!-- Topbar -->
    <header id="admin-topbar">
        <div class="flex items-center gap-4">
            <button id="sidebar-toggle" class="lg:hidden text-htec-text hover:text-white">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <span class="topbar-title"><?= htmlspecialchars($adminTitle ?? 'Dashboard') ?></span>
        </div>
        <div class="topbar-actions">
            <span class="topbar-user hidden md:block">
                <i class="fas fa-circle text-green-400 text-xs mr-1"></i>
                <?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>
            </span>
            <form method="POST" action="<?= url('admin/logout.php') ?>" style="display:inline">
                <?= csrfField() ?>
                <button type="submit" class="text-htec-text hover:text-white text-sm" aria-label="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </header>
    <!-- Flash -->
    <div class="px-8 pt-4">
        <?= renderFlash() ?>
    </div>
    <div id="admin-content">
