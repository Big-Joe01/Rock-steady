<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Views\Response;

abstract class Controller
{
    protected Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    protected function view(string $view, array $data = [], int $statusCode = 200): Response
    {
        return Response::view($view, $data, $statusCode);
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }

    protected function back(): Response
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return $this->redirect($referer);
    }

    protected function withSuccess(string $message): void
    {
        $_SESSION['success'] = $message;
    }

    protected function withError(string $message): void
    {
        $_SESSION['error'] = $message;
    }

    protected function withInput(array $except = []): void
    {
        $_SESSION['old'] = array_diff_key($_POST, array_flip($except));
    }

    protected function getOldInput(array $keys = []): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        
        if (empty($keys)) {
            return $old;
        }
        
        return array_intersect_key($old, array_flip($keys));
    }

    protected function getFlashMessage(string $type): ?string
    {
        $message = $_SESSION[$type] ?? null;
        unset($_SESSION[$type]);
        return $message;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function sanitizeInput(array $input): array
    {
        $sanitized = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    protected function validateRequired(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        return $errors;
    }

    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function paginate(string $table, int $perPage = 12, string $orderBy = 'id DESC'): array
    {
        $page = (int)($_GET['page'] ?? 1);
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        
        $total = \App\Models\Database::count($table);
        $totalPages = (int)ceil($total / $perPage);
        
        $sql = "SELECT * FROM {$table} ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $items = \App\Models\Database::fetchAll($sql, [$perPage, $offset]);
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
            'prev_page' => $page - 1,
            'next_page' => $page + 1,
        ];
    }
}
