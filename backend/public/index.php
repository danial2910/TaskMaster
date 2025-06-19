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

$app->run();
?>