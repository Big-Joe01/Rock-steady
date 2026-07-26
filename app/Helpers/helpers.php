<?php

declare(strict_types=1);

use App\Models\Database;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $configs = [];
        
        if (empty($configs)) {
            $configFiles = glob(CONFIG_PATH . '/*.php');
            foreach ($configFiles as $file) {
                $name = basename($file, '.php');
                $configs[$name] = require $file;
            }
        }
        
        $keys = explode('.', $key);
        $value = $configs;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return APP_URL . '/public/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('storage')) {
    function storage(string $path): string
    {
        return APP_URL . '/storage/' . ltrim($path, '/');
    }
}

if (!function_exists('uploaded')) {
    function uploaded(string $path): string
    {
        return APP_URL . '/public/uploads/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return APP_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        $routes = require ROUTES_PATH . '/web.php';
        if (!isset($routes[$name])) {
            return '#';
        }
        
        $uri = $routes[$name];
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', (string)$value, $uri);
        }
        return url($uri);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION[CSRF_TOKEN_NAME . '_time'] = time();
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
    }
}

if (!function_exists('csrf_verify')) {
    function csrf_verify(string $token): bool
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        
        if (!hash_equals($_SESSION[CSRF_TOKEN_NAME], $token)) {
            return false;
        }
        
        $tokenTime = $_SESSION[CSRF_TOKEN_NAME . '_time'] ?? 0;
        if (time() - $tokenTime > CSRF_TOKEN_LIFETIME) {
            unset($_SESSION[CSRF_TOKEN_NAME], $_SESSION[CSRF_TOKEN_NAME . '_time']);
            return false;
        }
        
        return true;
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return isset($_SESSION['user']);
    }
}

if (!function_exists('admin_auth')) {
    function admin_auth(): ?array
    {
        return $_SESSION[ADMIN_SESSION_KEY] ?? null;
    }
}

if (!function_exists('admin_check')) {
    function admin_check(): bool
    {
        return isset($_SESSION[ADMIN_SESSION_KEY]) && $_SESSION[ADMIN_SESSION_KEY] === true;
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die;
    }
}

if (!function_exists('e')) {
    function e(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('str_slug')) {
    function str_slug(string $string): string
    {
        $string = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', strtolower($string));
        return trim($string, '-');
    }
}

if (!function_exists('str_limit')) {
    function str_limit(string $string, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($string) <= $limit) {
            return $string;
        }
        return rtrim(mb_substr($string, 0, $limit)) . $end;
    }
}

if (!function_exists('format_price')) {
    function format_price(float|int $amount, string $currency = 'USD'): string
    {
        $symbol = CURRENCIES[$currency]['symbol'] ?? '$';
        return $symbol . number_format((float)$amount, 2);
    }
}

if (!function_exists('carbon')) {
    function carbon(string $date = 'now'): \DateTime
    {
        return new \DateTime($date);
    }
}

if (!function_exists('now')) {
    function now(): \DateTime
    {
        return new \DateTime();
    }
}

if (!function_exists('cache')) {
    function cache(string $key, callable $callback, int $ttl = 3600): mixed
    {
        $cacheFile = STORAGE_PATH . "/cache/{$key}.cache";
        
        if (file_exists($cacheFile) && filemtime($cacheFile) + $ttl > time()) {
            return unserialize(file_get_contents($cacheFile));
        }
        
        $value = $callback();
        file_put_contents($cacheFile, serialize($value));
        return $value;
    }
}

if (!function_exists('cache_forget')) {
    function cache_forget(string $key): void
    {
        $cacheFile = STORAGE_PATH . "/cache/{$key}.cache";
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
}

if (!function_exists('clean_cache'): {
    function clean_cache(): void
    {
        $files = glob(STORAGE_PATH . "/cache/*.cache");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

if (!function_exists('generate_slug')) {
    function generate_slug(string $text): string
    {
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
        $text = strtolower($text);
        $text = trim($text, '-');
        return $text;
    }
}

if (!function_exists('is_new_arrival')) {
    function is_new_arrival(string $createdAt): bool
    {
        $created = new \DateTime($createdAt);
        $now = new \DateTime();
        $diff = $now->diff($created);
        return $diff->days <= NEW_ARRIVALS_DAYS;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
}

if (!function_exists('rate_limit')) {
    function rate_limit(string $key, int $maxAttempts = 5, int $decayMinutes = 1): bool
    {
        $cacheFile = STORAGE_PATH . "/cache/rate_limit_{$key}";
        
        $data = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [
            'attempts' => 0,
            'reset_at' => time() + ($decayMinutes * 60)
        ];
        
        if (time() > $data['reset_at']) {
            $data = [
                'attempts' => 0,
                'reset_at' => time() + ($decayMinutes * 60)
            ];
        }
        
        $data['attempts']++;
        
        file_put_contents($cacheFile, json_encode($data));
        
        return $data['attempts'] <= $maxAttempts;
    }
}

if (!function_exists('get_rate_limit_remaining')) {
    function get_rate_limit_remaining(string $key, int $maxAttempts = 5): int
    {
        $cacheFile = STORAGE_PATH . "/cache/rate_limit_{$key}";
        
        if (!file_exists($cacheFile)) {
            return $maxAttempts;
        }
        
        $data = json_decode(file_get_contents($cacheFile), true);
        
        if (time() > $data['reset_at']) {
            return $maxAttempts;
        }
        
        return max(0, $maxAttempts - $data['attempts']);
    }
}

if (!function_exists('pagination')) {
    function pagination(array $data, string $baseUrl = '', int $maxLinks = 5): string
    {
        if ($data['total_pages'] <= 1) {
            return '';
        }
        
        $html = '<div class="pagination">';
        
        if ($data['has_prev']) {
            $prevPage = $data['prev_page'];
            $html .= '<a href="' . $baseUrl . '?page=' . $prevPage . '" class="pagination__link pagination__link--prev">&laquo;</a>';
        }
        
        $start = max(1, $data['current_page'] - floor($maxLinks / 2));
        $end = min($data['total_pages'], $start + $maxLinks - 1);
        
        if ($end - $start < $maxLinks - 1) {
            $start = max(1, $end - $maxLinks + 1);
        }
        
        if ($start > 1) {
            $html .= '<a href="' . $baseUrl . '?page=1" class="pagination__link">1</a>';
            if ($start > 2) {
                $html .= '<span class="pagination__ellipsis">...</span>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            $active = $i === $data['current_page'] ? ' pagination__link--active' : '';
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="pagination__link' . $active . '">' . $i . '</a>';
        }
        
        if ($end < $data['total_pages']) {
            if ($end < $data['total_pages'] - 1) {
                $html .= '<span class="pagination__ellipsis">...</span>';
            }
            $html .= '<a href="' . $baseUrl . '?page=' . $data['total_pages'] . '" class="pagination__link">' . $data['total_pages'] . '</a>';
        }
        
        if ($data['has_next']) {
            $nextPage = $data['next_page'];
            $html .= '<a href="' . $baseUrl . '?page=' . $nextPage . '" class="pagination__link pagination__link--next">&raquo;</a>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
