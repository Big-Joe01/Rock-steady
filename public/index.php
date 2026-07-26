<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define('RESOURCES_PATH', __DIR__ . '/../resources/views');
define('STORAGE_PATH', __DIR__ . '/../storage');

require_once __DIR__ . '/../app/bootstrap.php';

$router = require ROUTES_PATH . '/web.php';

try {
    $router->dispatch();
} catch (\Throwable $e) {
    if (APP_DEBUG) {
        throw $e;
    }
    
    Logger::critical($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    http_response_code(500);
    
    if ($router instanceof \App\Routes\Router) {
        $controller = new \App\Controllers\FrontendController();
        $controller->notFound();
    } else {
        echo 'Something went wrong. Please try again later.';
    }
}
