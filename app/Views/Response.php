<?php

declare(strict_types=1);

namespace App\Views;

class Response
{
    private string $content = '';
    private int $statusCode = 200;
    private array $headers = [];

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public static function json(array $data, int $statusCode = 200): self
    {
        $response = new self(json_encode($data, JSON_UNESCAPED_UNICODE), $statusCode);
        $response->headers['Content-Type'] = 'application/json';
        return $response;
    }

    public static function view(string $view, array $data = [], int $statusCode = 200): self
    {
        $content = self::renderView($view, $data);
        return new self($content, $statusCode);
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->headers['Location'] = $url;
        return $response;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        echo $this->content;
    }

    private static function renderView(string $view, array $data): string
    {
        extract($data);
        
        $viewFile = RESOURCES_PATH . "/views/{$view}.php";
        
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
        }
        
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        
        if (defined('RESOURCES_PATH') && file_exists(RESOURCES_PATH . '/views/layouts/main.php')) {
            $data['content'] = $content;
            extract($data);
            ob_start();
            include RESOURCES_PATH . '/views/layouts/main.php';
            return ob_get_clean();
        }
        
        return $content;
    }
}
