<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    private function addRoute(string $method, string $uri, array $action): void
    {
        $this->routes[] = [
            'method'  => $method,
            'uri'     => $uri,
            'pattern' => $this->uriToPattern($uri),
            'action'  => $action, // [ControllerClass::class, 'method']
        ];
    }

    private function uriToPattern(string $uri): string
    {
        // Convert {param} placeholders to named regex groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params from URI
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controllerClass, $action] = $route['action'];

                if (!class_exists($controllerClass)) {
                    throw new \RuntimeException("Controller not found: {$controllerClass}");
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    throw new \RuntimeException("Method {$action} not found in {$controllerClass}");
                }

                $controller->$action($request, $params);
                return;
            }
        }

        // No route matched
        error_log("Router 404: " . $method . " " . $uri);
        Response::abort(404);
    }

    /**
     * Load routes from config/routes.php and dispatch.
     */
    public static function load(Request $request): void
    {
        $router = new static();
        require BASE_PATH . '/config/routes.php';
        $router->dispatch($request);
    }
}
