<?php

class Router
{
    private $routes = [];
    private $notFoundCallback;

    public function __construct()
    {
        $this->notFoundCallback = function () {
            http_response_code(404);
            echo "404 - Page Not Found";
        };
    }

    /**
     * Add a GET route
     */
    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Add a POST route
     */
    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Add a PUT route
     */
    public function put($path, $callback)
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * Add a DELETE route
     */
    public function delete($path, $callback)
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * Add any HTTP method route
     */
    public function any($path, $callback)
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $callback);
        }
    }

    /**
     * Set custom 404 handler
     */
    public function setNotFound($callback)
    {
        $this->notFoundCallback = $callback;
    }

    /**
     * Add a route to the routes array
     */
    private function addRoute($method, $path, $callback)
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback
        ];

        echo "<!-- Route added: $method $path -->\n";
    }

    /**
     * Run the router
     */
    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        echo "<!-- Request: $requestMethod $requestPath -->\n";
        echo "<!-- Total routes: " . count($this->routes) . " -->\n";

        foreach ($this->routes as $route) {
            echo "<!-- Checking route: {$route['method']} {$route['path']} -->\n";

            if ($route['method'] === $requestMethod && preg_match($route['pattern'], $requestPath, $matches)) {
                echo "<!-- Route matched! -->\n";

                // Remove the full match from matches array
                array_shift($matches);

                // Call the callback with parameters
                $result = call_user_func_array($route['callback'], $matches);

                // If callback returns something, output it
                if ($result !== null) {
                    echo $result;
                }

                return;
            }
        }

        echo "<!-- No route matched, calling 404 handler -->\n";

        // No route found, call 404 handler
        $result = call_user_func($this->notFoundCallback);
        if ($result !== null) {
            echo $result;
        }
    }

    /**
     * Get all registered routes (for debugging)
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
