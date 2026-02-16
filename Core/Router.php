<?php 
namespace Core;

use Core\middleware\Middleware;

/**
 * Class Router
 *
 * Handles HTTP routing for the application, mapping URIs and HTTP methods to controllers.
 * Supports middleware, multiple controller formats, and fallback to flat PHP files.
 *
 * @property array $routes Array of registered routes.
 *
 * Methods:
 * @method Router add(string $method, string $uri, mixed $controller)
 *      Registers a new route with the specified HTTP method, URI, and controller.
 *
 * @method Router get(string $uri, mixed $controller)
 *      Registers a GET route.
 *
 * @method Router post(string $uri, mixed $controller)
 *      Registers a POST route.
 *
 * @method Router delete(string $uri, mixed $controller)
 *      Registers a DELETE route.
 *
 * @method Router put(string $uri, mixed $controller)
 *      Registers a PUT route.
 *
 * @method Router patch(string $uri, mixed $controller)
 *      Registers a PATCH route.
 *
 * @method Router only(string $key)
 *      Assigns middleware to the most recently added route.
 *
 * @method mixed route(string $uri, string $method)
 *      Resolves and executes the controller for the given URI and HTTP method.
 *      Supports controller as string with '@', array [Class, method], or flat PHP file.
 *      Handles middleware and error cases.
 *
 * @method string previousUrl()
 *      Returns the previous URL from the HTTP referer header, or '/' if not available.
 *
 * @method void abort(int $code = 404)
 *      Sends an HTTP error response with the given code and loads the corresponding error view.
 */
class Router {
    protected $routes = [];

    public function add($method, $uri, $controller) {
        $this->routes[] = [
            "uri" => $uri,
            "controller" => $controller,
            'method' => strtoupper($method),
            'middleware' => null
        ];
        return $this;
    }

    public function get($uri, $controller) {
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller) {
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller) {
        return $this->add('DELETE', $uri, $controller);
    }

    public function put($uri, $controller) {
        return $this->add('PUT', $uri, $controller);
    }

    public function patch($uri, $controller) {
        return $this->add('PATCH', $uri, $controller);
    }

    public function only($key) {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;
        return $this;
    }

    public function route($uri, $method) {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::resolve($route['middleware']);

                $controller = $route['controller'];

                // CASE 1: Controller is a string like 'Sales/PublicInvoiceController@method'
                if (is_string($controller) && str_contains($controller, '@')) {
                    [$controllerPath, $methodName] = explode('@', $controller);
                    $controllerClass = 'Http\\Controllers\\' . str_replace('/', '\\', $controllerPath);

                    if (!class_exists($controllerClass)) {
                        die("Controller class <strong>$controllerClass</strong> not found.");
                    }

                    $controllerInstance = new $controllerClass();

                    if (!method_exists($controllerInstance, $methodName)) {
                        die("Method <strong>$methodName</strong> not found in $controllerClass.");
                    }

                    return call_user_func([$controllerInstance, $methodName]);
                }

                // CASE 2: Controller is an array [ControllerClass::class, 'method']
                if (is_array($controller) && count($controller) === 2) {
                    [$controllerClass, $methodName] = $controller;

                    if (!class_exists($controllerClass)) {
                        die("Controller class <strong>$controllerClass</strong> not found.");
                    }

                    $controllerInstance = new $controllerClass();

                    if (!method_exists($controllerInstance, $methodName)) {
                        die("Method <strong>$methodName</strong> not found in $controllerClass.");
                    }

                    return call_user_func([$controllerInstance, $methodName]);
                }

                // CASE 3: Fallback to flat PHP file
                if (is_string($controller)) {
                    $filePath = base_path('Http/Controllers/' . $controller);

                    if (file_exists($filePath)) {
                        return require $filePath;
                    }

                    die("Flat controller file <strong>$filePath</strong> not found.");
                }

                // If none of the above matched
                die("Invalid controller definition.");
            }
        }

        error_log("Route not found: $uri - Method: $method");
        $this->abort();
    }

    public function previousUrl() {
        return $_SERVER['HTTP_REFERER'] ?? '/';
    }

    public function abort($code = 404) {
        http_response_code($code);
        require base_path("views/{$code}.php");
        die();
    }
}
