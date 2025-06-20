<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use Firebase\JWT\JWT;

class AuthController
{
    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'All fields are required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Check if user exists
        if (User::where('email', $data['email'])->exists()) {
            $response->getBody()->write(json_encode(['error' => 'Email already registered']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);

        $response->getBody()->write(json_encode(['message' => 'User registered successfully']));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        if (empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Email and password are required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Find user
        $user = User::where('email', $data['email'])->first();
        if (!$user || !password_verify($data['password'], $user->password)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Generate JWT token
        $payload = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + ($_ENV['JWT_EXPIRE'] * 60)
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        $response->getBody()->write(json_encode([
            'message' => 'Login successful',
            'token' => $token
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}