<?php
namespace App;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Database class
class Database {
    private $pdo;

    public function __construct() {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function createUser($username, $password) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return false;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
        return ['id' => $this->pdo->lastInsertId(), 'username' => $username];
    }

    public function getUser($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createTask($username, $title, $description, $status) {
        $user = $this->getUser($username);
        if (!$user) {
            return null;
        }
        $stmt = $this->pdo->prepare("INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user['id'], $title, $description, $status]);
        $taskId = $this->pdo->lastInsertId();
        return $this->getTask($taskId, $username);
    }

    public function getTasks($username) {
        $user = $this->getUser($username);
        if (!$user) {
            return [];
        }
        $stmt = $this->pdo->prepare("SELECT id, title, description, status, created_at FROM tasks WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTask($id, $username) {
        $user = $this->getUser($username);
        if (!$user) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT id, title, description, status, created_at FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user['id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateTask($id, $username, $title, $description, $status) {
        $user = $this->getUser($username);
        if (!$user) {
            return null;
        }
        $updates = [];
        $params = [];
        if ($title !== null) {
            $updates[] = "title = ?";
            $params[] = $title;
        }
        if ($description !== null) {
            $updates[] = "description = ?";
            $params[] = $description;
        }
        if ($status !== null) {
            $updates[] = "status = ?";
            $params[] = $status;
        }
        if (empty($updates)) {
            return $this->getTask($id, $username);
        }
        $params[] = $id;
        $params[] = $user['id'];
        $query = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $this->getTask($id, $username);
    }

    public function updateTaskStatus($id, $username, $status) {
        return $this->updateTask($id, $username, null, null, $status);
    }

    public function deleteTask($id, $username) {
        $user = $this->getUser($username);
        if (!$user) {
            return false;
        }
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user['id']]);
        return $stmt->rowCount() > 0;
    }
}

// JWT Middleware
class AuthMiddleware {
    public function __invoke(Request $request, RequestHandler $handler): Response {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            if (!$authHeader) {
                throw new \Exception('Missing token', 401);
            }
            $token = str_replace('Bearer ', '', $authHeader);
            $key = getenv('JWT_SECRET') ?: 'your-secret-key';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            if ($decoded->iat > time() || ($decoded->nbf ?? 0) > time()) {
                throw new \Exception('Invalid token', 401);
            }
            $request = $request->withAttribute('user', $decoded);
            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {
            $response = new Response();
            return $response->withJson(['error' => 'Token expired'], 401);
        } catch (\Exception $e) {
            $response = new Response();
            return $response->withJson(['error' => $e->getMessage() ?: 'Invalid token'], $e->getCode() ?: 401);
        }
    }
}
?>