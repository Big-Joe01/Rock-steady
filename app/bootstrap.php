<?php

declare(strict_types=1);

// Skip autoload if vendor doesn't exist (InfinityFree doesn't support Composer)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

/*
|--------------------------------------------------------------------------
| Hardcoded Environment Variables (InfinityFree Compatible)
|--------------------------------------------------------------------------
*/

$_ENV = [
    'APP_NAME' => 'ROCK STEADY',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_URL' => 'https://your-domain.epizy.com',
    
    'DB_HOST' => 'sql123.infinityfree.com',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'your_database_name',
    'DB_USERNAME' => 'your_mysql_username',
    'DB_PASSWORD' => 'your_mysql_password',
    
    'SESSION_LIFETIME' => '120',
    'SESSION_DRIVER' => 'file',
    
    'CLOUDINARY_CLOUD_NAME' => '',
    'CLOUDINARY_API_KEY' => '',
    'CLOUDINARY_API_SECRET' => '',
    'CLOUDINARY_FOLDER' => 'rocksteady',
    
    'STRIPE_PUBLISHABLE_KEY' => '',
    'STRIPE_SECRET_KEY' => '',
    'STRIPE_WEBHOOK_SECRET' => '',
    'STRIPE_CURRENCY' => 'USD',
    
    'SMTP_HOST' => '',
    'SMTP_PORT' => '587',
    'SMTP_USERNAME' => '',
    'SMTP_PASSWORD' => '',
    'SMTP_FROM_EMAIL' => '',
    'SMTP_FROM_NAME' => 'ROCK STEADY',
    'SMTP_REPLY_TO' => '',
    
    'ADMIN_PASSWORD' => 'YourSecurePassword123!',
    
    'CSRF_TOKEN_NAME' => '_token',
    'CSRF_TOKEN_LIFETIME' => '3600',
    'RATE_LIMIT_MAX_ATTEMPTS' => '5',
    'RATE_LIMIT_DECAY_MINUTES' => '1',
    
    'SITE_TAGLINE' => 'JUST KEEP ROCKING',
    'NEWSLETTER_ENABLED' => 'true',
];

define('APP_NAME', $_ENV['APP_NAME']);
define('APP_ENV', $_ENV['APP_ENV']);
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN));
define('APP_URL', rtrim($_ENV['APP_URL'], '/'));

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_DATABASE', $_ENV['DB_DATABASE']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

define('SESSION_LIFETIME', (int)$_ENV['SESSION_LIFETIME']);
define('SESSION_DRIVER', $_ENV['SESSION_DRIVER']);

define('CLOUDINARY_CLOUD_NAME', $_ENV['CLOUDINARY_CLOUD_NAME']);
define('CLOUDINARY_API_KEY', $_ENV['CLOUDINARY_API_KEY']);
define('CLOUDINARY_API_SECRET', $_ENV['CLOUDINARY_API_SECRET']);
define('CLOUDINARY_FOLDER', $_ENV['CLOUDINARY_FOLDER']);

define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY']);
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY']);
define('STRIPE_WEBHOOK_SECRET', $_ENV['STRIPE_WEBHOOK_SECRET']);
define('STRIPE_CURRENCY', $_ENV['STRIPE_CURRENCY']);

define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_PORT', (int)$_ENV['SMTP_PORT']);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME']);
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD']);
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL']);
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME']);
define('SMTP_REPLY_TO', $_ENV['SMTP_REPLY_TO']);

define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD']);
define('ADMIN_SESSION_KEY', 'admin_authenticated');

define('CSRF_TOKEN_NAME', $_ENV['CSRF_TOKEN_NAME']);
define('CSRF_TOKEN_LIFETIME', (int)$_ENV['CSRF_TOKEN_LIFETIME']);
define('RATE_LIMIT_MAX_ATTEMPTS', (int)$_ENV['RATE_LIMIT_MAX_ATTEMPTS']);
define('RATE_LIMIT_DECAY_MINUTES', (int)$_ENV['RATE_LIMIT_DECAY_MINUTES']);

define('SITE_TAGLINE', $_ENV['SITE_TAGLINE']);
define('NEWSLETTER_ENABLED', filter_var($_ENV['NEWSLETTER_ENABLED'], FILTER_VALIDATE_BOOLEAN));

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

if (file_exists(__DIR__ . '/Helpers/helpers.php')) {
    require_once __DIR__ . '/Helpers/helpers.php';
}
