<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');
        
        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token not provided']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        try {
            $token = str_replace('Bearer ', '', $token);
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->user_id);
            return $handler->handle($request);
        } catch (ExpiredException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token expired']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (SignatureInvalidException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Authentication failed']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}