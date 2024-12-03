<?php
require __DIR__ . '/../vendor/autoload.php';

use PlantUML\Exceptions\HttpException;

$routes = include '../src/Routing/routes.php';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// cli-server is sapi name for php built-in server
if (php_sapi_name() === 'cli-server') {
    if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER['REQUEST_URI'])) {
        return false;
    }
}

try {
    if (!isset($routes[$path])) {
        throw new HttpException(404, 'The requested page was not found');
    }

    $renderer = $routes[$path]();
    echo $renderer->getContent();
} catch (HttpException $e) {
    http_response_code($e->getStatusCode());
    echo $e->getStatusCode() . ' ' . $e->getErrorMessage();
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}
