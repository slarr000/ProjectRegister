<?php
namespace ProjectRegister\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Убедимся, что сессия запущена
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Проверяем оба параметра сессии
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) ||
            !isset($_SESSION['username']) || empty($_SESSION['username'])) {
            $response = new \Slim\Psr7\Response();
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        return $handler->handle($request);
    }
}