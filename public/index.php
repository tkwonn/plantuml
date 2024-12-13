<?php
require __DIR__ . '/../vendor/autoload.php';

use PlantUML\Exceptions\HttpException;

$routes = include '../src/routes.php';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// cli-server is sapi name for php built-in server
if (php_sapi_name() === 'cli-server') {
    if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER['REQUEST_URI'])) {
        return false;
    }
}

if (isset($routes[$path])) {
    try {
        $renderer = $routes[$path]();
        // Set raw HTTP headers
        foreach ($renderer->getFields() as $name => $value) {
            $sanitized_value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
            if ($sanitized_value && $sanitized_value === $value) {
                header("{$name}: {$sanitized_value}");
            } else {
                throw new Exception('Failed setting header - original: ' . $value . ', sanitized: ' . $sanitized_value);
            }
        }
        echo $renderer->getContent();
    } catch (HttpException $e) {
        http_response_code($e->getStatusCode());
        echo $e->getStatusCode() . ' ' . $e->getErrorMessage();
    } catch (Exception $e) {
        http_response_code(500);
        echo $e->getMessage();
    }
} else {
    http_response_code(404);
    echo '404 Not Found: The requested route was not found on this server.';
}
