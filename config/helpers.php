<?php
/**
 * HTEC - Core Helper Functions
 */

require_once __DIR__ . '/database.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) == 443);
    $isProduction = defined('APP_ENV') && APP_ENV === 'production';

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', ($isProduction || $isHttps) ? '1' : '0');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isProduction || $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_name(SESSION_NAME);
    session_start();
}

// ============================================================
// CSRF Protection
// ============================================================

function generateCsrfToken(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCsrfToken(string $token): bool {
    return isset($_SESSION[CSRF_TOKEN_NAME]) 
        && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function csrfField(): string {
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

// ============================================================
// Flash Messages
// ============================================================

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    
    $icons = [
        'success' => '✓',
        'error'   => '✕',
        'warning' => '⚠',
        'info'    => 'ℹ',
    ];
    
    $icon = $icons[$flash['type']] ?? 'ℹ';
    $msg  = htmlspecialchars($flash['message']);
    
    return <<<HTML
    <div class="flash-message flash-{$flash['type']}" id="flash-msg">
        <span class="flash-icon">{$icon}</span>
        <span>{$msg}</span>
        <button onclick="this.parentElement.remove()" class="flash-close">&times;</button>
    </div>
    HTML;
}

// ============================================================
// Authentication
// ============================================================

function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/admin/login.php');
        exit;
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}
function requireAdmin(): void {
    requireLogin(); // เช็คว่าล็อกอินก่อน

    $user = getCurrentUser();

    if (!$user || $user['role'] !== 'admin') {
        // กัน user ธรรมดาเข้า
        header('Location: ' . APP_URL . '/admin/login.php');
        exit;
    }
}
// ============================================================
// Input Sanitization
// ============================================================

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeInt(mixed $input): int {
    return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
}

function validateEmail(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ============================================================
// URL Helpers
// ============================================================

function url(string $path = ''): string {
    return APP_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function slug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ============================================================
// Pagination Helper
// ============================================================

function paginate(int $total, int $perPage, int $currentPage): array {
    $totalPages = (int) ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $currentPage,
        'total_pages' => $totalPages,
        'offset'      => $offset,
        'has_prev'    => $currentPage > 1,
        'has_next'    => $currentPage < $totalPages,
        'prev_page'   => $currentPage - 1,
        'next_page'   => $currentPage + 1,
    ];
}

// ============================================================
// File Upload
// ============================================================

function uploadFile(array $file, string $subDir, array $allowedTypes, int $maxSize = 0): array {
    $maxSize = $maxSize ?: MAX_FILE_SIZE;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload failed with error code: ' . $file['error']];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File size exceeds limit of ' . ($maxSize / 1024 / 1024) . 'MB'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type: ' . $mimeType];
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('htec_', true) . '.' . strtolower($ext);
    $uploadPath = UPLOAD_DIR . $subDir . '/';
    
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    $fullPath = $uploadPath . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }
    
    return ['success' => true, 'path' => $subDir . '/' . $fileName];
}

function deleteFile(string $relativePath): bool {
    $fullPath = UPLOAD_DIR . ltrim($relativePath, '/');
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

// ============================================================
// Format Helpers
// ============================================================

function timeAgo(string $datetime): string {
    $time = time() - strtotime($datetime);
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time / 60) . ' min ago';
    if ($time < 86400) return floor($time / 3600) . ' hr ago';
    return date('M j, Y', strtotime($datetime));
}

function truncate(string $text, int $length = 150): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '…';
}
