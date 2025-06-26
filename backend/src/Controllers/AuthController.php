<?php
namespace App\Controllers;

use App\db;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private $db;
    private $secretKey;

    public function __construct(db $db, $secretKey)
    {
        $this->db = $db;
        $this->secretKey = $secretKey;
    }

public function register($data)
{
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    $now = date('Y-m-d H:i:s');

    if (!$username || !$password) {
        return ['error' => 'Username and password are required.'];
    }

    // Check if user exists
    $stmt = $this->db->getPDO()->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return ['error' => 'Username already exists.'];
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $this->db->getPDO()->prepare(
        "INSERT INTO users (username, password, created_at, updated_at) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$username, $hashedPassword, $now, $now]);

    return ['message' => 'User registered successfully.'];
}

public function login($data)
{
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    if (!$username || !$password) {
        return ['error' => 'Username and password are required.'];
    }

    $stmt = $this->db->getPDO()->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    // Verify hashed password
    if (!$user || !password_verify($password, $user['password'])) {
        return ['error' => 'Invalid username or password.'];
    }

    // Generate JWT
    $payload = [
        'sub' => $user['id'],
        'username' => $user['username'],
        'iat' => time(),
        'exp' => time() + 3600 // 1 hour
    ];
    $token = JWT::encode($payload, $this->secretKey, 'HS256');

    return [
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username']
        ]
    ];
}

}