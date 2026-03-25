<?php
/**
 * HTEC - Admin Login
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/OtherModels.php';

// Already logged in → dashboard
if (isLoggedIn()) redirect(url('admin/'));

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $attemptKey = 'login_attempts_' . hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '|' . ($_POST['username'] ?? ''));
        $attempts = $_SESSION[$attemptKey] ?? ['count' => 0, 'until' => 0];
        if (($attempts['until'] ?? 0) > time()) {
            $wait = (int) max(1, $attempts['until'] - time());
            $error = "Too many failed attempts. Try again in {$wait} seconds.";
        }

        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$error && (!$username || !$password)) {
            $error = 'Both username and password are required.';
        } elseif (!$error) {
            $userModel = new UserModel();
            $user = $userModel->findByUsername($username);

            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                unset($_SESSION[$attemptKey]);
                $userModel->login($user);
                setFlash('success', 'Welcome back, ' . $user['username'] . '!');
                redirect(url('admin/'));
            } else {
                $attempts['count'] = (int) ($attempts['count'] ?? 0) + 1;
                if ($attempts['count'] >= 5) {
                    $attempts['until'] = time() + 300;
                    $attempts['count'] = 0;
                }
                $_SESSION[$attemptKey] = $attempts;
                $error = 'Invalid username or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — HTEC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Tailwind (self-hosted build) -->
    <link rel="stylesheet" href="<?= url('assets/css/tailwind.generated.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
    <style>
        .login-bg {
            background-image:
                linear-gradient(rgba(42,42,42,0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(42,42,42,0.3) 1px, transparent 1px);
            background-size: 50px 50px;
        }
    </style>
</head>
<body class="bg-htec-dark text-white font-body antialiased min-h-screen flex items-center justify-center login-bg">
    <div class="absolute inset-0 bg-gradient-radial from-htec-red/5 to-transparent pointer-events-none"></div>

    <div class="relative w-full max-w-md px-6">
        <!-- Logo -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-htec-red flex items-center justify-center font-display font-800 text-white">HT</div>
                <span class="font-display font-700 text-2xl tracking-widest">HTEC</span>
            </div>
            <p class="text-htec-text text-sm tracking-widest uppercase font-display">Admin Panel</p>
        </div>

        <!-- Card -->
        <div class="bg-htec-gray border border-htec-border p-8">
            <h1 class="font-display font-700 text-xl mb-2">Sign In</h1>
            <p class="text-htec-text text-sm mb-8">Enter your credentials to access the dashboard.</p>

            <?php if ($error): ?>
            <div class="flash-message flash-error mb-6">
                <span class="flash-icon">✕</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= csrfField() ?>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        class="form-input" placeholder="admin" autocomplete="username" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="pwd-input"
                            class="form-input pr-10" placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" onclick="togglePwd()" class="absolute right-3 top-1/2 -translate-y-1/2 text-htec-text hover:text-white text-sm">
                            <i id="pwd-eye" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full justify-center py-3.5 mt-2">
                    Sign In <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </form>

        </div>

        <p class="text-center text-htec-text text-xs mt-6">
            <a href="<?= url() ?>" class="hover:text-white transition-colors">← Back to Website</a>
        </p>
    </div>

    <script>
    function togglePwd() {
        const input = document.getElementById('pwd-input');
        const eye = document.getElementById('pwd-eye');
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    </script>
</body>
</html>
