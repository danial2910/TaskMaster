<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Database configuration
$host = 'localhost';
$db = 'task_management';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// JWT Secret Key
$jwtSecret = 'your-secret-key-here';

// Create Slim App
$app = AppFactory::create();

// Add middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware (replaces ContentTypeMiddleware in Slim 4)
$app->addBodyParsingMiddleware();

// CORS Middleware
// CORS Middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

// Handle CORS preflight for PHP built-in server
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    exit(0);
}

// Add this OPTIONS handler for preflight requests
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});


// Error handling middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// JWT Middleware
function jwtMiddleware($pdo, $jwtSecret) {
    return function ($request, $handler) use ($pdo, $jwtSecret) {
        $authorization = $request->getHeader('Authorization');
        
        if (!$authorization) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Authorization header required']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        $token = str_replace('Bearer ', '', $authorization[0]);
        
        try {
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->user_id);
            return $handler->handle($request);
        } catch (Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    };
}

// Helper function to hash passwords
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Helper function to verify passwords
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Routes

// User Registration
$app->post('/api/auth/register', function (Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        $response->getBody()->write(json_encode(['error' => 'Username, email, and password are required']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$data['email'], $data['username']]);
    
    if ($stmt->fetch()) {
        $response->getBody()->write(json_encode(['error' => 'User already exists']));
        return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
    }
    
    // Create new user
    $hashedPassword = hashPassword($data['password']);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    
    try {
        $stmt->execute([$data['username'], $data['email'], $hashedPassword]);
        $response->getBody()->write(json_encode(['message' => 'User registered successfully']));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => 'Registration failed']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// User Login
$app->post('/api/auth/login', function (Request $request, Response $response) use ($pdo, $jwtSecret) {
    $data = $request->getParsedBody();
    
    if (!isset($data['email']) || !isset($data['password'])) {
        $response->getBody()->write(json_encode(['error' => 'Email and password are required']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
    
    // Find user
    $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();
    
    if (!$user || !verifyPassword($data['password'], $user['password'])) {
        $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
    
    // Generate JWT token
    $payload = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'iat' => time(),
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    
    $token = JWT::encode($payload, $jwtSecret, 'HS256');
    
    $response->getBody()->write(json_encode([
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Get all tasks for authenticated user
$app->get('/api/tasks', function (Request $request, Response $response) use ($pdo) {
    $userId = $request->getAttribute('user_id');
    
    $stmt = $pdo->prepare("
        SELECT id, title, description, status, priority, due_date, created_at, updated_at 
        FROM tasks 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    $tasks = $stmt->fetchAll();
    
    $response->getBody()->write(json_encode($tasks));
    return $response->withHeader('Content-Type', 'application/json');
})->add(jwtMiddleware($pdo, $jwtSecret));

// Create new task
$app->post('/api/tasks', function (Request $request, Response $response) use ($pdo) {
    $userId = $request->getAttribute('user_id');
    $data = $request->getParsedBody();
    
    if (!isset($data['title'])) {
        $response->getBody()->write(json_encode(['error' => 'Title is required']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO tasks (user_id, title, description, status, priority, due_date, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    try {
        $stmt->execute([
            $userId,
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? 'pending',
            $data['priority'] ?? 'medium',
            $data['due_date'] ?? null
        ]);
        
        $taskId = $pdo->lastInsertId();
        
        // Get the created task
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch();
        
        $response->getBody()->write(json_encode($task));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => 'Failed to create task']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
})->add(jwtMiddleware($pdo, $jwtSecret));

// Get single task by ID
$app->get('/api/tasks/{id}', function (Request $request, Response $response, $args) use ($pdo) {
    $userId = $request->getAttribute('user_id');
    $taskId = $args['id'];
    
    $stmt = $pdo->prepare("
        SELECT id, title, description, status, priority, due_date, created_at, updated_at 
        FROM tasks 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$taskId, $userId]);
    $task = $stmt->fetch();
    
    if (!$task) {
        $response->getBody()->write(json_encode(['error' => 'Task not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    
    $response->getBody()->write(json_encode($task));
    return $response->withHeader('Content-Type', 'application/json');
})->add(jwtMiddleware($pdo, $jwtSecret));

// Update task
$app->put('/api/tasks/{id}', function (Request $request, Response $response, $args) use ($pdo) {
    $userId = $request->getAttribute('user_id');
    $taskId = $args['id'];
    $data = $request->getParsedBody();
    
    // Check if task exists and belongs to user
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    
    if (!$stmt->fetch()) {
        $response->getBody()->write(json_encode(['error' => 'Task not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    
    // Update task
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET title = ?, description = ?, status = ?, priority = ?, due_date = ?, updated_at = NOW() 
        WHERE id = ? AND user_id = ?
    ");
    
    try {
        $stmt->execute([
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['status'] ?? null,
            $data['priority'] ?? null,
            $data['due_date'] ?? null,
            $taskId,
            $userId
        ]);
        
        // Get updated task
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch();
        
        $response->getBody()->write(json_encode($task));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => 'Failed to update task']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
})->add(jwtMiddleware($pdo, $jwtSecret));

// Delete task
$app->delete('/api/tasks/{id}', function (Request $request, Response $response, $args) use ($pdo) {
    $userId = $request->getAttribute('user_id');
    $taskId = $args['id'];
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    
    if ($stmt->rowCount() === 0) {
        $response->getBody()->write(json_encode(['error' => 'Task not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    
    $response->getBody()->write(json_encode(['message' => 'Task deleted successfully']));
    return $response->withHeader('Content-Type', 'application/json');
})->add(jwtMiddleware($pdo, $jwtSecret));

// Update task status
$app->put('/api/tasks/{id}/status', function (Request $request, Response $response, $args) use ($pdo) {
    $userId = $request->getAttribute('user_id');
    $taskId = $args['id'];
    $data = $request->getParsedBody();
    
    if (!isset($data['status'])) {
        $response->getBody()->write(json_encode(['error' => 'Status is required']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
    
    // Check if task exists and belongs to user
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    
    if (!$stmt->fetch()) {
        $response->getBody()->write(json_encode(['error' => 'Task not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    
    // Update task status
    $stmt = $pdo->prepare("UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
    
    try {
        $stmt->execute([$data['status'], $taskId, $userId]);
        
        // Get updated task
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch();
        
        $response->getBody()->write(json_encode($task));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => 'Failed to update task status']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
})->add(jwtMiddleware($pdo, $jwtSecret));

// Run the application
$app->run();