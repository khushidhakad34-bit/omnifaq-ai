<?php
/**
 * OmniFAQ AI - PDO Database Connection Handler
 * ----------------------------------------------------------------------------
 * Connects securely to MySQL using PDO with error reporting, prepared statement
 * enforcement, and UTF-8 character encoding.
 */

// Detect environment: local vs cloud/production
$isLocal = false;
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    if ($host === 'localhost' || $host === '127.0.0.1' || preg_match('/^192\.168\./', $host) || preg_match('/^10\./', $host)) {
        $isLocal = true;
    }
} else if (php_sapi_name() === 'cli') {
    $isLocal = true;
}

// Database Configuration - Supports Cloud Environment Variables (e.g. Render) with local fallback
define('DB_HOST', getenv('DB_HOST') ?: ($isLocal ? '127.0.0.1' : 'localhost'));
define('DB_NAME', getenv('DB_NAME') ?: 'ai_faq_db');
define('DB_USER', getenv('DB_USER') ?: ($isLocal ? 'root' : 'root'));
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// Google Gemini API Configuration
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');
define('GEMINI_MODEL', getenv('GEMINI_MODEL') ?: 'gemini-1.5-flash');

/**
 * Returns a singleton PDO database connection instance.
 *
 * @return PDO
 */
function getDbConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // Enforce native prepared statements
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error'   => 'Database Connection Failed',
                'details' => 'Unable to establish PDO connection: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    return $pdo;
}
