<?php
namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $config = require __DIR__ . '/../../config.php';
        $authHeader = $request->getHeaderLine('Authorization');
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            try {
                $decoded = JWT::decode($matches[1], new Key($config['jwt']['secret'], 'HS256'));
                $request = $request->withAttribute('user', $decoded);
                return $handler->handle($request);
            } catch (\Exception $e) {
                return (new \Slim\Psr7\Response())->withStatus(401);
            }
        }

        return (new \Slim\Psr7\Response())->withStatus(401);
    }
}
