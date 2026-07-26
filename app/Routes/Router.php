<?php

declare(strict_types=1);

namespace App\Routes;

class Router
{
    private array $routes = [];
    private array $groupMiddleware = [];
    private string $basePath = '';

    public function get(string $uri, callable|array $handler): self
    {
        return $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, callable|array $handler): self
    {
        return $this->addRoute('POST', $uri, $handler);
    }

    public function put(string $uri, callable|array $handler): self
    {
        return $this->addRoute('PUT', $uri, $handler);
    }

    public function patch(string $uri, callable|array $handler): self
    {
        return $this->addRoute('PATCH', $uri, $handler);
    }

    public function delete(string $uri, callable|array $handler): self
    {
        return $this->addRoute('DELETE', $uri, $handler);
    }

    public function options(string $uri, callable|array $handler): self
    {
        return $this->addRoute('OPTIONS', $uri, $handler);
    }

    public function group(array $middleware = [], callable $callback): self
    {
        $previousMiddleware = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);
        
        $callback($this);
        
        $this->groupMiddleware = $previousMiddleware;
        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->basePath = $prefix;
        return $this;
    }

    private function addRoute(string $method, string $uri, callable|array $handler): self
    {
        $uri = $this->basePath . '/' . trim($uri, '/');
        $uri = $uri === '' ? '/' : '/' . ltrim($uri, '/');
        
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $handler,
            'middleware' => $this->groupMiddleware,
        ];
        
        return $this;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = '/' . trim($uri, '/');
        $uri = $uri === '/' ? '/' : '/' . ltrim($uri, '/');

        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchUri($route['uri'], $uri);
            
            if ($params !== false) {
                $this->executeMiddleware($route['middleware']);
                
                $handler = $route['handler'];
                
                if (is_array($handler)) {
                    [$controllerClass, $methodName] = $handler;
                    $controller = new $controllerClass();
                    $response = $controller->$methodName(...$params);
                } else {
                    $response = $handler(...$params);
                }
                
                $this->sendResponse($response);
                return;
            }
        }

        $this->handleNotFound();
    }

    private function matchUri(string $routeUri, string $requestUri): array|false
    {
        $routeParts = explode('/', trim($routeUri, '/'));
        $requestParts = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        $params = [];

        for ($i = 0; $i < count($routeParts); $i++) {
            if (str_starts_with($routeParts[$i], '{') && str_ends_with($routeParts[$i], '}')) {
                $params[] = $requestParts[$i];
            } elseif ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }

        return $params;
    }

    private function executeMiddleware(array $middleware): void
    {
        foreach ($middleware as $middlewareClass) {
            $instance = new $middlewareClass();
            $instance->handle();
        }
    }

    private function sendResponse(mixed $response): void
    {
        if ($response instanceof \App\Views\Response) {
            echo $response->getContent();
        } elseif (is_string($response)) {
            echo $response;
        } elseif (is_array($response)) {
            header('Content-Type: application/json');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } elseif ($response === null) {
            return;
        }
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        
        $controller = new \App\Controllers\FrontendController();
        $controller->notFound();
    }
}
