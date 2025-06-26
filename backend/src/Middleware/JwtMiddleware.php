<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class JwtMiddleware implements MiddlewareInterface
{
    private $secretKey;
    private $unprotectedRoutes;

    public function __construct($secretKey, $unprotectedRoutes = [])
    {
        $this->secretKey = $secretKey;
        $this->unprotectedRoutes = $unprotectedRoutes;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $path = $request->getUri()->getPath();
        if (in_array($path, $this->unprotectedRoutes)) {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Missing or invalid Authorization header']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $jwt = $matches[1];
        try {
            $decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($this->secretKey, 'HS256'));
            $request = $request->withAttribute('user', $decoded);

            // Check role for specific endpoints
            if ($request->getMethod() === 'PUT' && preg_match('#^/api/products/\d+$#', $path)) {
                if (isset($decoded->role) && $decoded->role !== 'admin') {
                    $response = new \Slim\Psr7\Response();
                    $response->getBody()->write(json_encode(['error' => 'Permission denied. Admins only for editing.']));
                    return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
                }
            }
            if ($request->getMethod() === 'DELETE' && preg_match('#^/api/products/\d+$#', $path)) {
                if (isset($decoded->role) && $decoded->role !== 'admin') {
                    $response = new \Slim\Psr7\Response();
                    $response->getBody()->write(json_encode(['error' => 'Permission denied. Admins only for deleting.']));
                    return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
                }
            }
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token: ' . $e->getMessage()]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}