<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Enhanced CORS headers function
function setCorsHeaders() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    // Allow specific origins
    $allowedOrigins = [
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000'
    ];
    
    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        header('Access-Control-Allow-Origin: http://localhost:5173'); // Default fallback
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json; charset=utf-8');
    
    // Set cache control for preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Max-Age: 86400'); // 24 hours
        header('Content-Length: 0');
    }
}

// CRITICAL: Handle CORS preflight requests BEFORE any other processing
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    setCorsHeaders();
    http_response_code(200);
    exit();
}

// Set CORS headers for all requests
setCorsHeaders();

// Get PDO instance from db.php - create a simple one if it doesn't exist
try {
    $pdo = require __DIR__ . '/../Database.php';
} catch (Exception $e) {
    // Fallback database connection if db.php doesn't exist
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=task_management;charset=utf8mb4", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        setCorsHeaders(); // Make sure CORS headers are set even for errors
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
        exit();
    }
}

$app = AppFactory::create();

// Add routing middleware
$app->addRoutingMiddleware();

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Enhanced CORS Middleware - Apply to all routes
$app->add(function ($request, $handler) {
    $origin = $request->getHeaderLine('Origin');
    
    $allowedOrigins = [
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000'
    ];
    
    $allowOrigin = in_array($origin, $allowedOrigins) ? $origin : 'http://localhost:5173';
    
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Content-Type', 'application/json; charset=utf-8');
});

// Handle preflight requests for all routes
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response
        ->withStatus(200)
        ->withHeader('Access-Control-Max-Age', '86400')
        ->withHeader('Content-Length', '0');
});

// Test route
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        "message" => "TaskMaster API is working!", 
        "timestamp" => date('Y-m-d H:i:s'),
        "cors_test" => "OK"
    ]));
    return $response;
});

// Register Route with enhanced error handling
$app->post('/register', function (Request $request, Response $response) use ($pdo) {
    try {
        // Get the request body
        $body = $request->getBody()->getContents();
        
        // Log the raw body for debugging
        error_log("Raw request body: " . $body);
        
        // Try to parse JSON
        $data = json_decode($body, true);
        
        // If JSON decode failed, try to get parsed body
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            $data = $request->getParsedBody();
        }
        
        // Log the parsed data
        error_log("Parsed request data: " . json_encode($data));
        
        // Validate required fields
        if (!$data || !isset($data['username']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Username and password are required",
                "received_data" => $data // For debugging
            ]));
            return $response->withStatus(400);
        }
        
        $username = trim($data['username']);
        $email = trim($data['email'] ?? '');
        $role = trim($data['role'] ?? 'user');
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Validate input
        if (strlen($username) < 3) {
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Username must be at least 3 characters long"
            ]));
            return $response->withStatus(400);
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Invalid email format"
            ]));
            return $response->withStatus(400);
        }

        // Check if users table exists, create if not
        try {
            $pdo->query("SELECT 1 FROM users LIMIT 1");
        } catch (PDOException $e) {
            // Create users table if it doesn't exist
            $createTable = "
                CREATE TABLE users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(255),
                    role VARCHAR(50) DEFAULT 'user',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            $pdo->exec($createTable);
            error_log("Created users table");
        }

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Username already exists"
            ]));
            return $response->withStatus(400);
        }

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $role]);
        
        $userId = $pdo->lastInsertId();
        
        $response->getBody()->write(json_encode([
            "status" => "success", 
            "message" => "User registered successfully",
            "user_id" => $userId
        ]));
        return $response;
        
    } catch (PDOException $e) {
        error_log("Database error in register: " . $e->getMessage());
        
        if ($e->getCode() == 23000) { // Duplicate entry
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Username already exists"
            ]));
        } else {
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Registration failed: Database error"
            ]));
        }
        return $response->withStatus(400);
    } catch (Exception $e) {
        error_log("General error in register: " . $e->getMessage());
        $response->getBody()->write(json_encode([
            "status" => "fail", 
            "message" => "Server error occurred"
        ]));
        return $response->withStatus(500);
    }
});

// Login Route with enhanced error handling
$app->post('/login', function (Request $request, Response $response) use ($pdo) {
    try {
        // Get the request body
        $body = $request->getBody()->getContents();
        
        // Try to parse JSON
        $data = json_decode($body, true);
        
        // If JSON decode failed, try to get parsed body
        if (json_last_error() !== JSON_ERROR_NONE) {
            $data = $request->getParsedBody();
        }
        
        // Log the incoming data for debugging
        error_log("Login request data: " . json_encode($data));
        
        if (!$data || !isset($data['username']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Username and password are required"
            ]));
            return $response->withStatus(400);
        }
        
        $username = trim($data['username']);
        $password = $data['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $response->getBody()->write(json_encode([
                "status" => "success", 
                "message" => "Login successful",
                "user_id" => $user['id'],
                "user" => [
                    "username" => $user['username'], 
                    "email" => $user['email'], 
                    "role" => $user['role']
                ]
            ]));
        } else {
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "message" => "Invalid credentials"
            ]));
        }
        
        return $response;
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $response->getBody()->write(json_encode([
            "status" => "fail", 
            "message" => "Login failed: Server error"
        ]));
        return $response->withStatus(500);
    }
});

// Add a simple test POST route to verify CORS is working
$app->post('/test', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        "status" => "success", 
        "message" => "POST request successful",
        "method" => "POST",
        "headers" => $request->getHeaders(),
        "data" => $request->getParsedBody()
    ]));
    return $response;
});

try {
    $app->run();
} catch (Exception $e) {
    error_log("App run error: " . $e->getMessage());
    setCorsHeaders(); // Ensure CORS headers are set even for fatal errors
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error occurred']);
}