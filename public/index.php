<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

ini_set('session.cookie_httponly', '1');
session_start();

use Slim\Factory\AppFactory;
use App\Container\ContainerIoC;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware($_ENV['APP_ENV'] === 'development', true, true);

// ── Rotas públicas ────────────────────────────
$app->get('/login',  [ContainerIoC::get('AuthController'), 'exibirLogin']);
$app->post('/login', [ContainerIoC::get('AuthController'), 'processarLogin']);
$app->get('/logout', [ContainerIoC::get('AuthController'), 'logout']);

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

// ── Rotas protegidas — API JSON ───────────────
$app->group('/api', function ($group) {
    $chatController = ContainerIoC::get('ChatController');
    $adminController = ContainerIoC::get('AdminController');
    $chamadoController = ContainerIoC::get('ChamadoController');

    $group->get('/conversas',        [$chatController, 'listarConversas']);
    $group->get('/mensagens',        [$chatController, 'listarMensagens']);
    $group->post('/mensagens',       [$chatController, 'enviarMensagem']);
    $group->get('/usuarios/online',  [$chatController, 'listarUsuarios']);

    // Conversas
    $group->post('/conversas',                              [$chatController, 'criarConversa']);
    $group->patch('/conversas/{id}',                        [$chatController, 'editarConversa']);
    $group->delete('/conversas/{id}',                       [$chatController, 'deletarConversa']);
    $group->post('/conversas/{id}/lida',                    [$chatController, 'marcarComoLida']);
    $group->get('/conversas/{id}/participantes',            [$chatController, 'listarParticipantes']);
    $group->post('/conversas/{id}/participantes',           [$chatController, 'adicionarParticipante']);
    $group->delete('/conversas/{id}/participantes/{uid}',   [$chatController, 'removerParticipante']);

    // Chamados
    $group->post('/chamados',              [$chamadoController, 'criar']);
    $group->get('/chamados',               [$chamadoController, 'listar']);
    $group->patch('/chamados/{id}/status', [$chamadoController, 'atualizarStatus']);

    // Admin — usuários
    $group->get('/admin/usuarios',         [$adminController, 'listarUsuarios']);
    $group->post('/admin/usuarios',        [$adminController, 'criarUsuario']);
    $group->patch('/admin/usuarios/{id}',  [$adminController, 'atualizarUsuario']);
    $group->delete('/admin/usuarios/{id}', [$adminController, 'desativarUsuario']);

    // Admin — setores
    $group->get('/admin/setores',          [$adminController, 'listarSetores']);
    $group->post('/admin/setores',         [$adminController, 'criarSetor']);
    $group->delete('/admin/setores/{id}',  [$adminController, 'deletarSetor']);
})->add(new AuthMiddleware());

$app->get('/', function ($request, $response) {
    return $response->withHeader('Location', '/login')->withStatus(302);
});

$app->run();