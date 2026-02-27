<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $params = [];

    public function get($route, $handler)
    {
        $this->addRoute('GET', $route, $handler);
    }

    public function any($route, $handler)
    {
        $this->addRoute('GET', $route, $handler);
        $this->addRoute('POST', $route, $handler);
        $this->addRoute('PUT', $route, $handler);
        $this->addRoute('DELETE', $route, $handler);
    }

    public function post($route, $handler)
    {
        $this->addRoute('POST', $route, $handler);
    }

    public function put($route, $handler)
    {
        $this->addRoute('PUT', $route, $handler);
    }

    public function delete($route, $handler)
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    private function addRoute($method, $route, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'handler' => $handler
        ];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['route']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->params = $matches;

                // Handle closures
                if (is_callable($route['handler'])) {
                    return call_user_func_array($route['handler'], $this->params);
                }

                list($controller, $method) = explode('@', $route['handler']);
                $controllerClass = "App\\Controllers\\{$controller}";
                
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        try {
                            // Clear any output before calling controller
                            if (ob_get_level()) {
                                ob_clean();
                            }
                            $result = $controllerInstance->$method(...$this->params);
                            // If controller returns something (shouldn't happen with exit, but just in case)
                            if ($result !== null) {
                                header('Content-Type: application/json');
                                echo is_string($result) ? $result : json_encode($result);
                            }
                            exit;
                        } catch (\Exception $e) {
                            if (ob_get_level()) {
                                ob_clean();
                            }
                            header('Content-Type: application/json');
                            http_response_code(500);
                            echo json_encode([
                                'success' => false,
                                'error' => 'Server error',
                                'message' => $e->getMessage(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine()
                            ]);
                            exit;
                        }
                    } else {
                        if (ob_get_level()) {
                            ob_clean();
                        }
                        header('Content-Type: application/json');
                        http_response_code(500);
                        echo json_encode(['success' => false, 'error' => "Method {$method} not found in {$controllerClass}"]);
                        exit;
                    }
                } else {
                    if (ob_get_level()) {
                        ob_clean();
                    }
                    header('Content-Type: application/json');
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => "Controller {$controllerClass} not found"]);
                    exit;
                }
            }
        }

        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Route not found', 'method' => $method, 'uri' => $uri]);
        exit;
    }

    private function convertToRegex($route)
    {
        $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
}
