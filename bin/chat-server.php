<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

date_default_timezone_set('America/Sao_Paulo');

// Carrega o .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Services\ChatServer;

$port = 8080;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    $port
);

echo "WebSocket rodando na porta {$port}...\n";
echo "Pressione Ctrl+C para parar.\n\n";

$server->run();
