<?php
declare(strict_types=1);
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;
use App\Helpers\Response as Json;

class AdminMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $papel = $request->getAttribute('user_papel');

        if ($papel !== 'admin') {
            $response = new SlimResponse();
            return Json::erro($response, 'Acesso restrito ao administrador', 403);
        }

        return $handler->handle($request);
    }
}
