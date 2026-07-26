<?php

declare(strict_types=1);

namespace App\Middleware;

class RateLimitMiddleware
{
    public function handle(): void
    {
        $ip = get_client_ip();
        $key = 'rate_limit_' . md5($ip . '_' . ($_SERVER['REQUEST_URI'] ?? ''));
        
        if (!rate_limit($key, RATE_LIMIT_MAX_ATTEMPTS, RATE_LIMIT_DECAY_MINUTES)) {
            http_response_code(429);
            
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Too many requests. Please wait before trying again.',
                    'retry_after' => RATE_LIMIT_DECAY_MINUTES * 60
                ]);
                exit;
            }
            
            $_SESSION['error'] = 'Too many requests. Please wait before trying again.';
            header('Location: ' . url('/'));
            exit;
        }
    }

    private function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
