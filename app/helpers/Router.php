<?php
/**
 * ELLCY — Simple URL Router
 */
class Router {
    private array $routes = [];
    private string $basePath = '';

    public function __construct(string $basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $pattern, callable $handler): void {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function post(string $pattern, callable $handler): void {
        $this->routes[] = ['POST', $pattern, $handler];
    }

    public function any(string $pattern, callable $handler): void {
        $this->routes[] = ['ANY', $pattern, $handler];
    }

    public function dispatch(): void {
        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $uri    = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
        if ($this->basePath !== '' && ($uri === $this->basePath || str_starts_with($uri, $this->basePath . '/'))) {
            $uri = substr($uri, strlen($this->basePath));
        }
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as [$routeMethod, $pattern, $handler]) {
            if ($routeMethod !== 'ANY' && $routeMethod !== $method) continue;

            // Convert :param and * to regex
            $regex = preg_replace('/:[a-zA-Z_]+/', '([^/]+)', $pattern);
            $regex = str_replace('*', '(.+)', $regex);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        require VIEWS_PATH . '/pages/404.php';
    }

    // Generate URL relative to app root
    public static function url(string $path = ''): string {
        $path = str_replace(["\r", "\n", "\\"], '', trim($path));
        if ($path === '' || $path === '/') return APP_URL . '/';
        if (APP_BASE !== '' && ($path === APP_BASE || str_starts_with($path, APP_BASE . '/'))) {
            $path = substr($path, strlen(APP_BASE));
        }
        return APP_URL . '/' . ltrim($path, '/');
    }

    // Redirect helper
    public static function redirect(string $path, int $code = 302): never {
        $parts = parse_url($path);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || str_contains($path, "\0")) {
            $path = '/';
        }
        header('Location: ' . self::url($path), true, $code);
        exit;
    }
}
