<?php

declare(strict_types=1);

namespace App\Middleware;

class AdminMiddleware
{
    public function handle(): void
    {
        if (!admin_check()) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin access required']);
            exit;
        }
    }
}
