<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Get PDO instance from db.php
$pdo = require __DIR__ . '/../db.php';

$app = AppFactory::create();

// Add routing middleware FIRST
$app->addRoutingMiddleware();

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Add CORS headers manually for simplicity
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Handle preflight requests
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// Test route
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(["message" => "TaskMaster API is working!"]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Register Route
$app->post('/register', function (Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    
    if (!isset($data['username']) || !isset($data['password'])) {
        $response->getBody()->write(json_encode(["status" => "fail", "message" => "Username and password required"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    try {
        $stmt->execute([$username, $password]);
        $response->getBody()->write(json_encode(["status" => "success", "message" => "User registered successfully"]));
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            $response->getBody()->write(json_encode(["status" => "fail", "message" => "Username already exists"]));
        } else {
            $response->getBody()->write(json_encode(["status" => "fail", "message" => "Registration failed"]));
        }
    }

    return $response->withHeader('Content-Type', 'application/json');
});

// Login Route
$app->post('/login', function (Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    
    if (!isset($data['username']) || !isset($data['password'])) {
        $response->getBody()->write(json_encode(["status" => "fail", "message" => "Username and password required"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $username = $data['username'];
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $response->getBody()->write(json_encode(["status" => "success", "message" => "Login successful"]));
    } else {
        $response->getBody()->write(json_encode(["status" => "fail", "message" => "Invalid credentials"]));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();