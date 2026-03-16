<?php
// Front controller - route requests

require_once __DIR__ . '/../src/bootstrap.php';

$routes = require __DIR__ . '/../config/routes.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

if (isset($routes[$path])) {
    [$controllerClass, $method] = $routes[$path];
    $controller = new $controllerClass($GLOBALS['pdo']);
    $controller->$method();
} else {
    http_response_code(404);
    echo "Page not found";
}
