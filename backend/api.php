<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require __DIR__ . '/db.php';

// Get PDO instance
$pdo = require __DIR__ . '/db.php';

// Get the request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Parse the path to get the endpoint
$pathParts = explode('/', trim($path, '/'));
$endpoint = end($pathParts);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            if ($endpoint === 'api.php' || $endpoint === '') {
                echo json_encode(["message" => "TaskMaster API is working!"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Endpoint not found"]);
            }
            break;
            
        case 'POST':
            if ($endpoint === 'register' || (isset($_GET['action']) && $_GET['action'] === 'register')) {
                // Registration logic
                if (!isset($input['username']) || !isset($input['password'])) {
                    http_response_code(400);
                    echo json_encode(["status" => "fail", "message" => "Username and password required"]);
                    break;
                }
                
                $username = $input['username'];
                $password = password_hash($input['password'], PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                try {
                    $stmt->execute([$username, $password]);
                    echo json_encode(["status" => "success", "message" => "User registered successfully"]);
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { // Duplicate entry
                        echo json_encode(["status" => "fail", "message" => "Username already exists"]);
                    } else {
                        echo json_encode(["status" => "fail", "message" => "Registration failed: " . $e->getMessage()]);
                    }
                }
                
            } elseif ($endpoint === 'login' || (isset($_GET['action']) && $_GET['action'] === 'login')) {
                // Login logic
                if (!isset($input['username']) || !isset($input['password'])) {
                    http_response_code(400);
                    echo json_encode(["status" => "fail", "message" => "Username and password required"]);
                    break;
                }
                
                $username = $input['username'];
                $password = $input['password'];
                
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    echo json_encode(["status" => "success", "message" => "Login successful"]);
                } else {
                    echo json_encode(["status" => "fail", "message" => "Invalid credentials"]);
                }
                
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Endpoint not found"]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error: " . $e->getMessage()]);
}
?>