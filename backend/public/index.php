<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Initialize Slim app
$app = AppFactory::create();

// Load dependencies
require __DIR__ . '/../src/dependencies.php';

// Load routes
require __DIR__ . '/../src/routes.php';

// Serve frontend
$app->get('/', function ($request, $response) {
    $file = __DIR__ . '/../../frontend/index.html';
    $response->getBody()->write(file_get_contents($file));
    return $response->withHeader('Content-Type', 'text/html');
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
});

$app->run();
?>