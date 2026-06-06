<?php
declare(strict_types=1);

namespace App;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void    { $this->add('GET', $path, $handler); }
    public function post(string $path, array $handler): void   { $this->add('POST', $path, $handler); }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'handler' => $handler];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = strtok($_SERVER['REQUEST_URI'], '?');

        // Strip subfolder base path (e.g. when served from /portfolio/public/ in XAMPP).
        // SCRIPT_NAME = "/portfolio/public/index.php" → base = "/portfolio/public"
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($base !== '' && $base !== '/' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                [$class, $action] = $route['handler'];
                $controller = new $class();
                $controller->$action(...$matches);
                return;
            }
        }

        error_page(404, 'site/404');
    }
}
