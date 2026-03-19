<?php
declare(strict_types=1);
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $request = $request->withAttribute('user_id',    $_SESSION['user_id']);
        $request = $request->withAttribute('user_nome',  $_SESSION['user_nome']);
        $request = $request->withAttribute('user_papel', $_SESSION['user_papel']);

        return $handler->handle($request);
    }
}
