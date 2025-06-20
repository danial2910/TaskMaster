<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Middleware\AuthMiddleware;

$app->group('/api/auth', function (RouteCollectorProxy $group) {
    $group->post('/register', AuthController::class . ':register');
    $group->post('/login', AuthController::class . ':login');
});

$app->group('/api/tasks', function (RouteCollectorProxy $group) {
    $group->get('', TaskController::class . ':index');
    $group->post('', TaskController::class . ':store');
    $group->get('/{id}', TaskController::class . ':show');
    $group->put('/{id}', TaskController::class . ':update');
    $group->delete('/{id}', TaskController::class . ':destroy');
    $group->put('/{id}/complete', TaskController::class . ':markComplete');
})->add(new AuthMiddleware());