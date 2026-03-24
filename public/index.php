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
use App\Controllers\ChamadoController;
use App\Controllers\AdminController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware($_ENV['APP_ENV'] === 'development', true, true);

// ── Rotas públicas ────────────────────────────
$app->get('/login',  [AuthController::class, 'exibirLogin']);
$app->post('/login', [AuthController::class, 'processarLogin']);
$app->get('/logout', [AuthController::class, 'logout']);

// ── Rotas protegidas — Frontend ───────────────
$app->get('/admin', function ($request, $response) {
    $userName = $request->getAttribute('user_nome');
    ob_start();
    include __DIR__ . '/../templates/admin.php';
    $html = ob_get_clean();
    $response->getBody()->write($html);
    return $response;
})->add(new AdminMiddleware())->add(new AuthMiddleware());

$app->get('/chat', function ($request, $response) {
    $userName  = $request->getAttribute('user_nome');
    $userId    = $request->getAttribute('user_id');
    $userPapel = $request->getAttribute('user_papel');
    ob_start();
    include __DIR__ . '/../templates/chat.php';
    $html = ob_get_clean();
    $response->getBody()->write($html);
    return $response;
})->add(new AuthMiddleware());

$app->get('/dashboard-ti', function ($request, $response) {
    $userName  = $request->getAttribute('user_nome');
    $userPapel = $request->getAttribute('user_papel');
    
    // Se não for TI ou Admin, redireciona pro chat
    if (!in_array($userPapel, ['ti', 'admin'])) {
        return $response->withHeader('Location', '/chat')->withStatus(302);
    }

    ob_start();
    include __DIR__ . '/../templates/dashboard_ti.php';
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

    // Conversas
    $group->post('/conversas',                              [ChatController::class, 'criarConversa']);
    $group->patch('/conversas/{id}',                        [ChatController::class, 'editarConversa']);
    $group->delete('/conversas/{id}',                       [ChatController::class, 'deletarConversa']);
    $group->post('/conversas/{id}/lida',                    [ChatController::class, 'marcarComoLida']);
    $group->get('/conversas/{id}/participantes',            [ChatController::class, 'listarParticipantes']);
    $group->post('/conversas/{id}/participantes',           [ChatController::class, 'adicionarParticipante']);
    $group->delete('/conversas/{id}/participantes/{uid}',   [ChatController::class, 'removerParticipante']);

    // Chamados
    $group->post('/chamados',              [ChamadoController::class, 'criar']);
    $group->get('/chamados',               [ChamadoController::class, 'listar']);
    $group->patch('/chamados/{id}/status', [ChamadoController::class, 'atualizarStatus']);
    $group->patch('/chamados/{id}/classificar', [ChamadoController::class, 'classificar']);
    $group->patch('/chamados/{id}/finalizar', [ChamadoController::class, 'finalizar']);

    // Admin — usuários
    $group->get('/admin/usuarios',         [AdminController::class, 'listarUsuarios']);
    $group->post('/admin/usuarios',        [AdminController::class, 'criarUsuario']);
    $group->patch('/admin/usuarios/{id}',  [AdminController::class, 'atualizarUsuario']);
    $group->delete('/admin/usuarios/{id}', [AdminController::class, 'desativarUsuario']);

    // Admin — setores
    $group->get('/admin/setores',          [AdminController::class, 'listarSetores']);
    $group->post('/admin/setores',         [AdminController::class, 'criarSetor']);
    $group->delete('/admin/setores/{id}',  [AdminController::class, 'deletarSetor']);
})->add(new AuthMiddleware());

$app->get('/', function ($request, $response) {
    return $response->withHeader('Location', '/login')->withStatus(302);
});

$app->run();