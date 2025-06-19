<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database;
use App\AuthMiddleware;

// Middleware for JSON responses
$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response->withHeader('Content-Type', 'application/json');
});

// Initialize database
try {
    $db = new Database();
} catch (Exception $e) {
    die($e->getMessage());
}

// API Endpoints

// 1. Register
$app->post('/api/auth/register', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();
    if (empty($data['username']) || empty($data['password'])) {
        return $response->withJson(['error' => 'Username and password required'], 400);
    }
    $user = $db->createUser($data['username'], $data['password']);
    if (!$user) {
        return $response->withJson(['error' => 'Username already exists'], 400);
    }
    return $response->withJson(['message' => 'User registered'], 201);
});

// 2. Login
$app->post('/api/auth/login', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();
    $user = $db->getUser($data['username']);
    if ($user && password_verify($data['password'], $user['password'])) {
        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $user['username']
        ];
        $jwt = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');
        return $response->withJson(['token' => $jwt]);
    }
    return $response->withJson(['error' => 'Invalid credentials'], 401);
});

// Protected routes with JWT middleware
$authMiddleware = new AuthMiddleware();

// 3. Get User Tasks
$app->get('/api/tasks', function (Request $request, Response $response) use ($db) {
    $user = $request->getAttribute('user');
    $tasks = $db->getTasks($user->sub);
    return $response->withJson($tasks);
})->add($authMiddleware);

// 4. Create Task
$app->post('/api/tasks', function (Request $request, Response $response) use ($db) {
    $user = $request->getAttribute('user');
    $data = $request->getParsedBody();
    if (empty($data['title'])) {
        return $response->withJson(['error' => 'Title required'], 400);
    }
    $task = $db->createTask($user->sub, $data['title'], $data['description'] ?? '', $data['status'] ?? 'pending');
    if (!$task) {
        return $response->withJson(['error' => 'User not found'], 404);
    }
    return $response->withJson($task, 201);
})->add($authMiddleware);

// 5. Get Single Task
$app->get('/api/tasks/{id}', function (Request $request, Response $response, $args) use ($db) {
    $user = $request->getAttribute('user');
    $task = $db->getTask($args['id'], $user->sub);
    if (!$task) {
        return $response->withJson(['error' => 'Task not found'], 404);
    }
    return $response->withJson($task);
})->add($authMiddleware);

// 6. Update Task
$app->put('/api/tasks/{id}', function (Request $request, Response $response, $args) use ($db) {
    $user = $request->getAttribute('user');
    $data = $request->getParsedBody();
    $task = $db->updateTask($args['id'], $user->sub, $data['title'] ?? null, $data['description'] ?? null, $data['status'] ?? null);
    if (!$task) {
        return $response->withJson(['error' => 'Task not found'], 404);
    }
    return $response->withJson($task);
})->add($authMiddleware);

// 7. Delete Task
$app->delete('/api/tasks/{id}', function (Request $request, Response $response, $args) use ($db) {
    $user = $request->getAttribute('user');
    $deleted = $db->deleteTask($args['id'], $user->sub);
    if (!$deleted) {
        return $response->withJson(['error' => 'Task not found'], 404);
    }
    return $response->withJson(['message' => 'Task deleted']);
})->add($authMiddleware);

// 8. Update Task Status
$app->patch('/api/tasks/{id}/status', function (Request $request, Response $response, $args) use ($db) {
    $user = $request->getAttribute('user');
    $data = $request->getParsedBody();
    if (empty($data['status'])) {
        return $response->withJson(['error' => 'Status required'], 400);
    }
    $task = $db->updateTaskStatus($args['id'], $user->sub, $data['status']);
    if (!$task) {
        return $response->withJson(['error' => 'Task not found'], 404);
    }
    return $response->withJson($task);
})->add($authMiddleware);
?>