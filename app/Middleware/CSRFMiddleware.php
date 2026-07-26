<?php

declare(strict_types=1);

namespace App\Middleware;

class CSRFMiddleware
{
    private array $except = ['/api/webhook/stripe'];

    public function handle(): void
    {
        if ($this->isExcluded()) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!csrf_verify($token)) {
                if ($this->isAjax()) {
                    http_response_code(419);
                    echo json_encode(['error' => 'CSRF token mismatch']);
                    exit;
                }
                
                $_SESSION['error'] = 'Security validation failed. Please try again.';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }
    }

    private function isExcluded(): bool
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($this->except as $pattern) {
            if (fnmatch($pattern, $currentPath)) {
                return true;
            }
        }
        
        return false;
    }

    private function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
