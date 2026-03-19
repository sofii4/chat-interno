<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

ini_set('session.cookie_httponly', '1');
session_start();

use Slim\Factory\AppFactory;
use App\Controllers\AuthController;
use App\Controllers\ChatController;
use App\Middleware\AuthMiddleware;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware($_ENV['APP_ENV'] === 'development', true, true);

// ── Rotas públicas ────────────────────────────
$app->get('/login',  [AuthController::class, 'exibirLogin']);
$app->post('/login', [AuthController::class, 'processarLogin']);
$app->get('/logout', [AuthController::class, 'logout']);

// ── Rotas protegidas — Frontend ───────────────
$app->get('/chat', function ($request, $response) {
    $userName = $request->getAttribute('user_nome');
    $userId   = $request->getAttribute('user_id');
    ob_start();
    include __DIR__ . '/../templates/chat.php';
    $html = ob_get_clean();
    $response->getBody()->write($html);
    return $response;
})->add(new AuthMiddleware());

// ── Rotas protegidas — API JSON ───────────────
$app->group('/api', function ($group) {
    $group->get('/conversas',        [ChatController::class, 'listarConversas']);
    $group->get('/mensagens',        [ChatController::class, 'listarMensagens']);
    $group->post('/mensagens',       [ChatController::class, 'enviarMensagem']);
    $group->get('/usuarios/online',  [ChatController::class, 'listarUsuarios']);
})->add(new AuthMiddleware());

$app->get('/', function ($request, $response) {
    return $response->withHeader('Location', '/login')->withStatus(302);
});

$app->run();