<?php
/**
 * HTEC - Core Helper Functions
 */

require_once __DIR__ . '/database.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
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
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($_SESSION['_csrf_token_expires']) || $_SESSION['_csrf_token_expires'] < time()) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token_expires'] = time() + 3600;
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCsrfToken(string $token): bool {
    $valid = isset($_SESSION[CSRF_TOKEN_NAME], $_SESSION['_csrf_token_expires'])
        && $_SESSION['_csrf_token_expires'] >= time()
        && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);

    // Rotate after every validation attempt to reduce replay window.
    if ($valid) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token_expires'] = time() + 3600;
    }

    return $valid;
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

function requireAdmin(): void {
    requireLogin();
    if (($_SESSION['admin_role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Forbidden');
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
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
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type: ' . $mimeType];
    }
    
    $safeExtensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
    ];
    if (!isset($safeExtensions[$mimeType])) {
        return ['success' => false, 'error' => 'Unsupported MIME type'];
    }

    // For images, verify file structure is actually an image.
    if (str_starts_with($mimeType, 'image/')) {
        $imgInfo = @getimagesize($file['tmp_name']);
        if ($imgInfo === false) {
            return ['success' => false, 'error' => 'Invalid image content'];
        }
    }

    $ext = $safeExtensions[$mimeType];
    $fileName = uniqid('htec_', true) . '.' . $ext;
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
    $relativePath = ltrim($relativePath, '/\\');
    if ($relativePath === '' || str_contains($relativePath, "\0")) {
        return false;
    }

    $uploadRoot = realpath(UPLOAD_DIR);
    if ($uploadRoot === false) {
        return false;
    }

    $fullPath = realpath(UPLOAD_DIR . $relativePath);
    if ($fullPath === false) {
        return false;
    }

    // Ensure resolved file is inside upload directory.
    if (strpos($fullPath, $uploadRoot . DIRECTORY_SEPARATOR) !== 0) {
        return false;
    }

    if (is_file($fullPath)) {
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
