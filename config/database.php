<?php
/**
 * HTEC - Database Configuration
 * PDO connection with error handling
 */

/**
 * Load environment variables from .env file (project root).
 */
function loadEnvFile(string $path): void {
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);

        // Remove optional surrounding quotes.
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        if ($key === '') {
            continue;
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function env(string $key, ?string $default = null): ?string {
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }
    return $value;
}

loadEnvFile(__DIR__ . '/../.env');

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'htec_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Application Settings
define('APP_NAME', env('APP_NAME', 'HTEC'));
define('APP_URL', env('APP_URL', 'http://localhost/HTEC_PROJECT/htec'));
define('APP_VERSION', env('APP_VERSION', '1.0.0'));

// Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', APP_URL . '/uploads/');
define('MAX_FILE_SIZE', (int) env('MAX_FILE_SIZE', (string) (10 * 1024 * 1024))); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_PDF_TYPES', ['application/pdf']);

// Session
define('SESSION_NAME', env('SESSION_NAME', 'htec_session'));
define('CSRF_TOKEN_NAME', env('CSRF_TOKEN_NAME', '_csrf_token'));

/**
 * Get PDO Database Connection (Singleton)
 */
function getDB(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log this instead of displaying
            error_log("Database connection failed: " . $e->getMessage());
            die(json_encode(['error' => 'Database connection failed. Please try again later.']));
        }
    }
    
    return $pdo;
}
