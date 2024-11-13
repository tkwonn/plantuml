<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/..'));
spl_autoload_extensions(".php");
spl_autoload_register();

$routes = include('../Routing/routes.php');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// cli-server is sapi name for php built-in server
if (php_sapi_name() === 'cli-server') {
    if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER["REQUEST_URI"])) {
        return false;
    }
}

try {
    if (!isset($routes[$path])) {
        throw new \Exceptions\HttpException(404, 'The requested page was not found');
    }

    $renderer = $routes[$path]();
    echo $renderer->getContent();
} catch (\Exceptions\HttpException $e) {
    http_response_code($e->getStatusCode());
    echo $e->getStatusCode()." ". $e->getErrorMessage();
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}