<?php
// Suppress all PHP warnings and notices to prevent JSON corruption
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Start output buffering to catch any unexpected output
ob_start();

require __DIR__ . '/Database.php';

// Get PDO instance
$pdo = require __DIR__ . '/Database.php';

// Get the request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$pathParts = explode('/', trim($path, '/'));

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Helper function to get authorization header
function getAuthorizationHeader() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        return $headers['Authorization'];
    }
    if (isset($headers['authorization'])) {
        return $headers['authorization'];
    }
    return null;
}

// Helper function to verify JWT token (simplified version)
function verifyToken() {
    $authHeader = getAuthorizationHeader();
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return false;
    }
    
    $token = $matches[1];
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }
    
    return (int)$parts[0]; // Return user ID
}

// Helper function to create tables if they don't exist
function createTablesIfNotExist($pdo) {
    try {
        // Check if users table exists and what columns it has
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Add missing columns if they don't exist
        if (!in_array('email', $columns)) {
            $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255) DEFAULT NULL");
        }
        if (!in_array('role', $columns)) {
            $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user'");
        }
        if (!in_array('created_at', $columns)) {
            $pdo->exec("ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        }
        if (!in_array('updated_at', $columns)) {
            $pdo->exec("ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }
    } catch (PDOException $e) {
        // If table doesn't exist, create it
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) DEFAULT NULL,
                role VARCHAR(50) DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }
    
    // Workspaces table (added to match your schema)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS workspaces (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            user_id INT NOT NULL,
            color VARCHAR(7) DEFAULT '#667eea',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // Projects table - updated to match your schema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            workspace_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            color VARCHAR(7) DEFAULT '#667eea',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE
        )
    ");
    
    // Tasks table - updated to match your exact schema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            status ENUM('todo', 'in_progress', 'done') DEFAULT 'todo',
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            due_date DATE,
            user_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
        )
    ");
    
    // Comments table - matches your schema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            task_id INT NOT NULL,
            user_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
}

// Create tables
createTablesIfNotExist($pdo);

// Clear any output that might have been generated
ob_clean();

try {
    // Route handling based on the URL pattern
    if (in_array('api', $pathParts)) {
        $apiIndex = array_search('api', $pathParts);
        $endpoint = $pathParts[$apiIndex + 1] ?? '';
        $resource = $pathParts[$apiIndex + 2] ?? '';
        $resourceId = $pathParts[$apiIndex + 3] ?? '';
        $action = $pathParts[$apiIndex + 4] ?? '';
        
        switch ($method) {
            case 'GET':
                handleApiGetRequest($pdo, $endpoint, $resource, $resourceId);
                break;
            case 'POST':
                handleApiPostRequest($pdo, $endpoint, $resource, $input);
                break;
            case 'PUT':
                handleApiPutRequest($pdo, $endpoint, $resource, $resourceId, $action, $input);
                break;
            case 'DELETE':
                handleApiDeleteRequest($pdo, $endpoint, $resource, $resourceId);
                break;
            default:
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"]);
                break;
        }
    } else {
        // Fallback to original routing for backward compatibility
        switch ($method) {
            case 'GET':
                handleGetRequest($pdo, $pathParts);
                break;
            case 'POST':
                handlePostRequest($pdo, $pathParts, $input);
                break;
            case 'PUT':
                handlePutRequest($pdo, $pathParts, $input);
                break;
            case 'PATCH':
                handlePatchRequest($pdo, $pathParts, $input);
                break;
            case 'DELETE':
                handleDeleteRequest($pdo, $pathParts);
                break;
            default:
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"]);
                break;
        }
    }
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(["error" => "Internal server error"]);
}

// New API handlers following the /api/{endpoint}/{resource} pattern
function handleApiGetRequest($pdo, $endpoint, $resource, $resourceId) {
    switch ($endpoint) {
        case 'auth':
            http_response_code(405);
            echo json_encode(["error" => "GET not allowed for auth endpoints"]);
            break;
        case 'tasks':
            if ($resourceId) {
                getTask($pdo, verifyToken(), $resourceId);
            } else {
                getTasks($pdo, verifyToken());
            }
            break;
        case 'projects':
            if ($resourceId) {
                getProject($pdo, verifyToken(), $resourceId);
            } else {
                getProjects($pdo, verifyToken());
            }
            break;
        case 'workspaces':
            if ($resourceId) {
                getWorkspace($pdo, verifyToken(), $resourceId);
            } else {
                getWorkspaces($pdo, verifyToken());
            }
            break;
        case 'comments':
            if ($resource === 'task' && $resourceId) {
                getTaskComments($pdo, verifyToken(), $resourceId);
            } else {
                getComments($pdo, verifyToken());
            }
            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "Endpoint not found"]);
    }
}

function handleApiPostRequest($pdo, $endpoint, $resource, $input) {
    switch ($endpoint) {
        case 'auth':
            if ($resource === 'register') {
                registerUser($pdo, $input);
            } elseif ($resource === 'login') {
                loginUser($pdo, $input);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Auth endpoint not found"]);
            }
            break;
        case 'tasks':
            createTask($pdo, verifyToken(), $input);
            break;
        case 'projects':
            createProject($pdo, verifyToken(), $input);
            break;
        case 'workspaces':
            createWorkspace($pdo, verifyToken(), $input);
            break;
        case 'comments':
            createComment($pdo, verifyToken(), $input);
            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "Endpoint not found"]);
    }
}

function handleApiPutRequest($pdo, $endpoint, $resource, $resourceId, $action, $input) {
    $userId = verifyToken();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    switch ($endpoint) {
        case 'tasks':
            if ($action === 'status') {
                updateTaskStatus($pdo, $userId, $resource, $input);
            } else {
                updateTask($pdo, $userId, $resource, $input);
            }
            break;
        case 'projects':
            updateProject($pdo, $userId, $resource, $input);
            break;
        case 'workspaces':
            updateWorkspace($pdo, $userId, $resource, $input);
            break;
        case 'comments':
            updateComment($pdo, $userId, $resource, $input);
            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "Endpoint not found"]);
    }
}

function handleApiDeleteRequest($pdo, $endpoint, $resource, $resourceId) {
    $userId = verifyToken();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    switch ($endpoint) {
        case 'tasks':
            deleteTask($pdo, $userId, $resource);
            break;
        case 'projects':
            deleteProject($pdo, $userId, $resource);
            break;
        case 'workspaces':
            deleteWorkspace($pdo, $userId, $resource);
            break;
        case 'comments':
            deleteComment($pdo, $userId, $resource);
            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "Endpoint not found"]);
    }
}

// Original handlers for backward compatibility
function handleGetRequest($pdo, $pathParts) {
    $endpoint = end($pathParts);
    
    if ($endpoint === 'api.php' || $endpoint === '') {
        echo json_encode(["message" => "TaskMaster API is working!", "version" => "3.0"]);
        return;
    }
    
    $userId = verifyToken();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    switch ($endpoint) {
        case 'tasks':
            getTasks($pdo, $userId);
            break;
        case 'projects':
            getProjects($pdo, $userId);
            break;
        case 'workspaces':
            getWorkspaces($pdo, $userId);
            break;
        case 'comments':
            getComments($pdo, $userId);
            break;
        default:
            if (count($pathParts) >= 2) {
                $resourceType = $pathParts[count($pathParts) - 2];
                $resourceId = (int)$endpoint;
                
                switch ($resourceType) {
                    case 'tasks':
                        getTask($pdo, $userId, $resourceId);
                        break;
                    case 'projects':
                        getProject($pdo, $userId, $resourceId);
                        break;
                    case 'workspaces':
                        getWorkspace($pdo, $userId, $resourceId);
                        break;
                    default:
                        http_response_code(404);
                        echo json_encode(["error" => "Endpoint not found"]);
                }
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Endpoint not found"]);
            }
    }
}

function handlePostRequest($pdo, $pathParts, $input) {
    $endpoint = end($pathParts);
    
    // Handle authentication endpoints
    if ($endpoint === 'register' || (isset($_GET['action']) && $_GET['action'] === 'register')) {
        registerUser($pdo, $input);
        return;
    }
    
    if ($endpoint === 'login' || (isset($_GET['action']) && $_GET['action'] === 'login')) {
        loginUser($pdo, $input);
        return;
    }
    
    // All other endpoints require authentication
    $userId = verifyToken();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    switch ($endpoint) {
        case 'tasks':
            createTask($pdo, $userId, $input);
            break;
        case 'projects':
            createProject($pdo, $userId, $input);
            break;
        case 'workspaces':
            createWorkspace($pdo, $userId, $input);
            break;
        case 'comments':
            createComment($pdo, $userId, $input);
            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "Endpoint not found"]);
    }
}

function handlePutRequest($pdo, $pathParts, $input) {
    $userId = verifyToken();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (count($pathParts) >= 2) {
        $resourceType = $pathParts[count($pathParts) - 2];
        $resourceId = (int)end($pathParts);
        
        switch ($resourceType) {
            case 'tasks':
                updateTask($pdo, $userId, $resourceId, $input);
                break;
            case 'projects':
                updateProject($pdo, $userId, $resourceId, $input);
                break;
            case 'workspaces':
                updateWorkspace($pdo, $userId, $resourceId, $input);
                break;
            default:
                http_response_code(404);
                echo json_encode(["error" => "Endpoint not found"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Resource ID required"]);
    }
}

function handlePatchRequest($pdo, $pathParts, $input) {
    $userId = verifyToken();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (count($pathParts) >= 3 && $pathParts[count($pathParts) - 3] === 'tasks' && end($pathParts) === 'status') {
        $taskId = (int)$pathParts[count($pathParts) - 2];
        updateTaskStatus($pdo, $userId, $taskId, $input);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
    }
}

function handleDeleteRequest($pdo, $pathParts) {
    $userId = verifyToken();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (count($pathParts) >= 2) {
        $resourceType = $pathParts[count($pathParts) - 2];
        $resourceId = (int)end($pathParts);
        
        switch ($resourceType) {
            case 'tasks':
                deleteTask($pdo, $userId, $resourceId);
                break;
            case 'projects':
                deleteProject($pdo, $userId, $resourceId);
                break;
            case 'workspaces':
                deleteWorkspace($pdo, $userId, $resourceId);
                break;
            case 'comments':
                deleteComment($pdo, $userId, $resourceId);
                break;
            default:
                http_response_code(404);
                echo json_encode(["error" => "Endpoint not found"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Resource ID required"]);
    }
}

// Authentication functions
function registerUser($pdo, $input) {
    if (!isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(["status" => "fail", "message" => "Username and password required"]);
        return;
    }
    
    $username = trim($input['username']);
    $password = password_hash($input['password'], PASSWORD_DEFAULT);
    $email = isset($input['email']) ? trim($input['email']) : null;
    $role = isset($input['role']) ? trim($input['role']) : 'user';
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $password, $email, $role]);
        $userId = $pdo->lastInsertId();
        
        // Create a default workspace for the new user
        $defaultWorkspace = $pdo->prepare("INSERT INTO workspaces (name, description, user_id) VALUES (?, ?, ?)");
        $defaultWorkspace->execute(['My Workspace', 'Default workspace', $userId]);
        
        echo json_encode(["status" => "success", "message" => "User registered successfully"]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(["status" => "fail", "message" => "Username already exists"]);
        } else {
            echo json_encode(["status" => "fail", "message" => "Registration failed"]);
        }
    }
}

function loginUser($pdo, $input) {
    if (!isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(["status" => "fail", "message" => "Username and password required"]);
        return;
    }
    
    $username = trim($input['username']);
    $password = $input['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $token = $user['id'] . '.' . time() . '.' . hash('sha256', $user['id'] . $user['username'] . time());
        
        $userData = [
            "id" => (int)$user['id'],
            "username" => $user['username'],
            "email" => $user['email'] ?? null,
            "role" => $user['role'] ?? 'user'
        ];
        
        echo json_encode([
            "status" => "success", 
            "message" => "Login successful",
            "token" => $token,
            "user" => $userData
        ]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Invalid credentials"]);
    }
}

// Workspace functions
function getWorkspaces($pdo, $userId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT w.*,
               COUNT(DISTINCT p.id) as projects_count,
               COUNT(DISTINCT t.id) as tasks_count
        FROM workspaces w 
        LEFT JOIN projects p ON w.id = p.workspace_id 
        LEFT JOIN tasks t ON p.id = t.project_id 
        WHERE w.user_id = ? 
        GROUP BY w.id
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$userId]);
    $workspaces = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($workspaces);
}

function getWorkspace($pdo, $userId, $workspaceId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT w.*,
               COUNT(DISTINCT p.id) as projects_count,
               COUNT(DISTINCT t.id) as tasks_count
        FROM workspaces w 
        LEFT JOIN projects p ON w.id = p.workspace_id 
        LEFT JOIN tasks t ON p.id = t.project_id 
        WHERE w.id = ? AND w.user_id = ?
        GROUP BY w.id
    ");
    $stmt->execute([$workspaceId, $userId]);
    $workspace = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($workspace) {
        echo json_encode($workspace);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Workspace not found"]);
    }
}

function createWorkspace($pdo, $userId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (!isset($input['name']) || empty(trim($input['name']))) {
        http_response_code(400);
        echo json_encode(["error" => "Workspace name is required"]);
        return;
    }
    
    $stmt = $pdo->prepare("INSERT INTO workspaces (name, description, color, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        trim($input['name']),
        isset($input['description']) ? trim($input['description']) : '',
        isset($input['color']) ? $input['color'] : '#667eea',
        $userId
    ]);
    
    $workspaceId = $pdo->lastInsertId();
    getWorkspace($pdo, $userId, $workspaceId);
}

function updateWorkspace($pdo, $userId, $workspaceId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT id FROM workspaces WHERE id = ? AND user_id = ?");
    $stmt->execute([$workspaceId, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Workspace not found"]);
        return;
    }
    
    $fields = [];
    $values = [];
    
    if (isset($input['name']) && !empty(trim($input['name']))) {
        $fields[] = "name = ?";
        $values[] = trim($input['name']);
    }
    if (isset($input['description'])) {
        $fields[] = "description = ?";
        $values[] = trim($input['description']);
    }
    if (isset($input['color'])) {
        $fields[] = "color = ?";
        $values[] = $input['color'];
    }
    
    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(["error" => "No fields to update"]);
        return;
    }
    
    $values[] = $workspaceId;
    $values[] = $userId;
    
    $sql = "UPDATE workspaces SET " . implode(", ", $fields) . " WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    
    getWorkspace($pdo, $userId, $workspaceId);
}

function deleteWorkspace($pdo, $userId, $workspaceId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM workspaces WHERE id = ? AND user_id = ?");
    $stmt->execute([$workspaceId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Workspace deleted"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Workspace not found"]);
    }
}

// Project functions
function getProjects($pdo, $userId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT p.*, w.name as workspace_name,
               COUNT(t.id) as tasks_count,
               COALESCE(ROUND(
                   (COUNT(CASE WHEN t.status = 'done' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(t.id), 0)), 0
               ), 0) as progress
        FROM projects p 
        JOIN workspaces w ON p.workspace_id = w.id
        LEFT JOIN tasks t ON p.id = t.project_id 
        WHERE w.user_id = ? 
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$userId]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($projects);
}

function getProject($pdo, $userId, $projectId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT p.*, w.name as workspace_name,
               COUNT(t.id) as tasks_count,
               COALESCE(ROUND(
                   (COUNT(CASE WHEN t.status = 'done' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(t.id), 0)), 0
               ), 0) as progress
        FROM projects p 
        JOIN workspaces w ON p.workspace_id = w.id
        LEFT JOIN tasks t ON p.id = t.project_id 
        WHERE p.id = ? AND w.user_id = ?
        GROUP BY p.id
    ");
    $stmt->execute([$projectId, $userId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($project) {
        echo json_encode($project);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Project not found"]);
    }
}

function createProject($pdo, $userId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (!isset($input['name']) || empty(trim($input['name']))) {
        http_response_code(400);
        echo json_encode(["error" => "Project name is required"]);
        return;
    }
    
    if (!isset($input['workspace_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Workspace ID is required"]);
        return;
    }
    
    // Verify workspace belongs to user
    $stmt = $pdo->prepare("SELECT id FROM workspaces WHERE id = ? AND user_id = ?");
    $stmt->execute([$input['workspace_id'], $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Workspace not found"]);
        return;
    }
    
    $stmt = $pdo->prepare("INSERT INTO projects (workspace_id, name, description, color) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        (int)$input['workspace_id'],
        trim($input['name']),
        isset($input['description']) ? trim($input['description']) : '',
        isset($input['color']) ? $input['color'] : '#667eea'
    ]);
    
    $projectId = $pdo->lastInsertId();
    getProject($pdo, $userId, $projectId);
}

function updateProject($pdo, $userId, $projectId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    // Check if project belongs to user's workspace
    $stmt = $pdo->prepare("
        SELECT p.id FROM projects p 
        JOIN workspaces w ON p.workspace_id = w.id 
        WHERE p.id = ? AND w.user_id = ?
    ");
    $stmt->execute([$projectId, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Project not found"]);
        return;
    }
    
    $fields = [];
    $values = [];
    
    if (isset($input['name']) && !empty(trim($input['name']))) {
        $fields[] = "name = ?";
        $values[] = trim($input['name']);
    }
    if (isset($input['description'])) {
        $fields[] = "description = ?";
        $values[] = trim($input['description']);
    }
    if (isset($input['color'])) {
        $fields[] = "color = ?";
        $values[] = $input['color'];
    }
    
    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(["error" => "No fields to update"]);
        return;
    }
    
    $values[] = $projectId;
    
    $sql = "UPDATE projects SET " . implode(", ", $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    
    getProject($pdo, $userId, $projectId);
}

function deleteProject($pdo, $userId, $projectId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    // Check if project belongs to user's workspace
    $stmt = $pdo->prepare("
        SELECT p.id FROM projects p 
        JOIN workspaces w ON p.workspace_id = w.id 
        WHERE p.id = ? AND w.user_id = ?
    ");
    $stmt->execute([$projectId, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Project not found"]);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$projectId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Project deleted"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Project not found"]);
    }
}

// Task functions
function getTasks($pdo, $userId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT t.*, 
               COALESCE(p.name, '') as project_name,
               COALESCE(p.color, '#667eea') as project_color,
               w.name as workspace_name,
               (SELECT COUNT(*) FROM comments c WHERE c.task_id = t.id) as comments_count
        FROM tasks t 
        LEFT JOIN projects p ON t.project_id = p.id 
        LEFT JOIN workspaces w ON p.workspace_id = w.id
        WHERE t.user_id = ? 
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tasks);
}

function getTask($pdo, $userId, $taskId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT t.*, 
               COALESCE(p.name, '') as project_name,
               COALESCE(p.color, '#667eea') as project_color,
               w.name as workspace_name,
               (SELECT COUNT(*) FROM comments c WHERE c.task_id = t.id) as comments_count
        FROM tasks t 
        LEFT JOIN projects p ON t.project_id = p.id 
        LEFT JOIN workspaces w ON p.workspace_id = w.id
        WHERE t.id = ? AND t.user_id = ?
    ");
    $stmt->execute([$taskId, $userId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($task) {
        echo json_encode($task);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
    }
}

function createTask($pdo, $userId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (!isset($input['title']) || empty(trim($input['title']))) {
        http_response_code(400);
        echo json_encode(["error" => "Title is required"]);
        return;
    }
    
    // If project_id is provided, verify it belongs to user
    $projectId = null;
    if (!empty($input['project_id'])) {
        $stmt = $pdo->prepare("
            SELECT p.id FROM projects p 
            JOIN workspaces w ON p.workspace_id = w.id 
            WHERE p.id = ? AND w.user_id = ?
        ");
        $stmt->execute([$input['project_id'], $userId]);
        if ($stmt->fetch()) {
            $projectId = (int)$input['project_id'];
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO tasks (project_id, title, content, status, priority, due_date, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $projectId,
        trim($input['title']),
        isset($input['content']) ? trim($input['content']) : '',
        isset($input['status']) ? $input['status'] : 'todo',
        isset($input['priority']) ? $input['priority'] : 'medium',
        isset($input['due_date']) && !empty($input['due_date']) ? $input['due_date'] : null,
        $userId
    ]);
    
    $taskId = $pdo->lastInsertId();
    getTask($pdo, $userId, $taskId);
}

function updateTask($pdo, $userId, $taskId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
        return;
    }
    
    $fields = [];
    $values = [];
    
    if (isset($input['title']) && !empty(trim($input['title']))) {
        $fields[] = "title = ?";
        $values[] = trim($input['title']);
    }
    if (isset($input['content'])) {
        $fields[] = "content = ?";
        $values[] = trim($input['content']);
    }
    if (isset($input['status'])) {
        $fields[] = "status = ?";
        $values[] = $input['status'];
    }
    if (isset($input['priority'])) {
        $fields[] = "priority = ?";
        $values[] = $input['priority'];
    }
    if (isset($input['due_date'])) {
        $fields[] = "due_date = ?";
        $values[] = !empty($input['due_date']) ? $input['due_date'] : null;
    }
    if (isset($input['project_id'])) {
        // Verify project belongs to user if provided
        $projectId = null;
        if (!empty($input['project_id'])) {
            $stmt = $pdo->prepare("
                SELECT p.id FROM projects p 
                JOIN workspaces w ON p.workspace_id = w.id 
                WHERE p.id = ? AND w.user_id = ?
            ");
            $stmt->execute([$input['project_id'], $userId]);
            if ($stmt->fetch()) {
                $projectId = (int)$input['project_id'];
            }
        }
        $fields[] = "project_id = ?";
        $values[] = $projectId;
    }
    
    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(["error" => "No fields to update"]);
        return;
    }
    
    $values[] = $taskId;
    $values[] = $userId;
    
    $sql = "UPDATE tasks SET " . implode(", ", $fields) . " WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    
    getTask($pdo, $userId, $taskId);
}

function updateTaskStatus($pdo, $userId, $taskId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (!isset($input['status'])) {
        http_response_code(400);
        echo json_encode(["error" => "Status is required"]);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$input['status'], $taskId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Task status updated"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
    }
}

function deleteTask($pdo, $userId, $taskId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Task deleted"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
    }
}

// Comment functions
function getComments($pdo, $userId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT c.*, t.title as task_title, u.username
        FROM comments c 
        JOIN tasks t ON c.task_id = t.id 
        JOIN users u ON c.user_id = u.id
        WHERE t.user_id = ? OR c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$userId, $userId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($comments);
}

function getTaskComments($pdo, $userId, $taskId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    // Verify task belongs to user
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT c.*, u.username
        FROM comments c 
        JOIN users u ON c.user_id = u.id
        WHERE c.task_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$taskId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($comments);
}

function createComment($pdo, $userId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (!isset($input['content']) || !isset($input['task_id']) || empty(trim($input['content']))) {
        http_response_code(400);
        echo json_encode(["error" => "Content and task_id are required"]);
        return;
    }
    
    // Verify task exists and belongs to user
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$input['task_id'], $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
        return;
    }
    
    $stmt = $pdo->prepare("INSERT INTO comments (task_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([
        (int)$input['task_id'],
        $userId,
        trim($input['content'])
    ]);
    
    $commentId = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("
        SELECT c.*, t.title as task_title, u.username
        FROM comments c 
        JOIN tasks t ON c.task_id = t.id 
        JOIN users u ON c.user_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$commentId]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

function updateComment($pdo, $userId, $commentId, $input) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    if (!isset($input['content']) || empty(trim($input['content']))) {
        http_response_code(400);
        echo json_encode(["error" => "Content is required"]);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([trim($input['content']), $commentId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("
            SELECT c.*, t.title as task_title, u.username
            FROM comments c 
            JOIN tasks t ON c.task_id = t.id 
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$commentId]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Comment not found"]);
    }
}

function deleteComment($pdo, $userId, $commentId) {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->execute([$commentId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Comment deleted"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Comment not found"]);
    }
}

// End output buffering and flush
ob_end_flush();
?>