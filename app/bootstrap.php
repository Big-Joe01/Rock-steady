<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

if (empty($_ENV['APP_KEY'])) {
    $_ENV['APP_KEY'] = bin2hex(random_bytes(32));
}

define('APP_NAME', $_ENV['APP_NAME'] ?? 'ROCK STEADY');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('APP_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'));

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');
define('DB_DATABASE', $_ENV['DB_DATABASE'] ?? 'rocksteady');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');

define('SESSION_LIFETIME', (int)($_ENV['SESSION_LIFETIME'] ?? 120));
define('SESSION_DRIVER', $_ENV['SESSION_DRIVER'] ?? 'file');

define('CLOUDINARY_CLOUD_NAME', $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '');
define('CLOUDINARY_API_KEY', $_ENV['CLOUDINARY_API_KEY'] ?? '');
define('CLOUDINARY_API_SECRET', $_ENV['CLOUDINARY_API_SECRET'] ?? '');
define('CLOUDINARY_FOLDER', $_ENV['CLOUDINARY_FOLDER'] ?? 'rocksteady');

define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '');
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? '');
define('STRIPE_WEBHOOK_SECRET', $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '');
define('STRIPE_CURRENCY', $_ENV['STRIPE_CURRENCY'] ?? 'USD');

define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? '');
define('SMTP_PORT', (int)($_ENV['SMTP_PORT'] ?? 587));
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? '');
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? '');
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? '');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'ROCK STEADY');
define('SMTP_REPLY_TO', $_ENV['SMTP_REPLY_TO'] ?? '');

define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD'] ?? 'Cybertr0n#$');
define('ADMIN_SESSION_KEY', 'admin_authenticated');

define('CSRF_TOKEN_NAME', $_ENV['CSRF_TOKEN_NAME'] ?? '_token');
define('CSRF_TOKEN_LIFETIME', (int)($_ENV['CSRF_TOKEN_LIFETIME'] ?? 3600));
define('RATE_LIMIT_MAX_ATTEMPTS', (int)($_ENV['RATE_LIMIT_MAX_ATTEMPTS'] ?? 5));
define('RATE_LIMIT_DECAY_MINUTES', (int)($_ENV['RATE_LIMIT_DECAY_MINUTES'] ?? 1));

define('SITE_TAGLINE', $_ENV['SITE_TAGLINE'] ?? 'JUST KEEP ROCKING');
define('NEWSLETTER_ENABLED', filter_var($_ENV['NEWSLETTER_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN));

define('NEW_ARRIVALS_DAYS', 90);

define('CURRENCIES', [
    'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
    'EUR' => ['symbol' => '€', 'name' => 'Euro'],
    'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
]);

date_default_timezone_set('UTC');

session_start();

if (APP_ENV === 'local' && APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

require_once __DIR__ . '/Helpers/helpers.php';
