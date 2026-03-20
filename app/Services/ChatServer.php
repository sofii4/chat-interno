<?php
declare(strict_types=1);
namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class ChatServer implements MessageComponentInterface
{
    private SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
        echo "Servidor de chat iniciado!\n";
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
        $conn->userId     = null;
        $conn->userName   = null;
        $conn->conversaId = null;
        echo "Nova conexão: #{$conn->resourceId} | Total: {$this->clients->count()}\n";
    }

    public function onMessage(ConnectionInterface $from, $msgRaw): void
    {
        $data = json_decode($msgRaw, true);
        if (!$data || !isset($data['type'])) return;

        switch ($data['type']) {

            // 1. Autenticação: frontend envia user_id + user_nome ao conectar
            case 'auth':
                $from->userId     = (int) ($data['user_id'] ?? 0);
                $from->userName   = $data['user_nome'] ?? 'Anônimo';
                $from->conversaId = (int) ($data['conversa_id'] ?? 0);
                echo "Usuário autenticado: {$from->userName} (#{$from->userId})\n";

                // Confirma autenticação para o cliente
                $from->send(json_encode([
                    'type'   => 'auth_ok',
                    'userId' => $from->userId,
                ]));
                break;

            // 2. Entrar numa conversa (mudar de sala)
            case 'join':
                $from->conversaId = (int) ($data['conversa_id'] ?? 0);
                echo "Usuário {$from->userName} entrou na conversa #{$from->conversaId}\n";
                break;

            // 3. Enviar mensagem
            case 'send_message':
                if (!$from->userId) return;

                $conversaId = (int) ($data['conversa_id'] ?? 0);
                $conteudo   = trim($data['conteudo'] ?? '');

                if (!$conversaId || !$conteudo) return;
                if (mb_strlen($conteudo) > 5000) return;

                // Persiste no banco
                try {
                    $pdo  = getDbConnection();

                    // Segurança: verifica se é participante
                    $check = $pdo->prepare(
                        'SELECT 1 FROM participantes WHERE conversa_id = ? AND usuario_id = ?'
                    );
                    $check->execute([$conversaId, $from->userId]);
                    if (!$check->fetch()) return;

                    $stmt = $pdo->prepare(
                        'INSERT INTO mensagens (conversa_id, usuario_id, conteudo) VALUES (?, ?, ?)'
                    );
                    $stmt->execute([$conversaId, $from->userId, $conteudo]);
                    $msgId = (int) $pdo->lastInsertId();

                    $payload = json_encode([
                        'type'    => 'new_message',
                        'message' => [
                            'id'           => $msgId,
                            'conteudo'     => $conteudo,
                            'criado_em'    => date('Y-m-d H:i:s'),
                            'usuario_id'   => $from->userId,
                            'usuario_nome' => $from->userName,
                            'conversa_id'  => $conversaId,
                        ]
                    ], JSON_UNESCAPED_UNICODE);

                    // Envia para todos na mesma conversa
                    foreach ($this->clients as $client) {
                        if ($client->conversaId === $conversaId) {
                            $client->send($payload);
                        }
                    }

                    echo "Mensagem de {$from->userName} na conversa #{$conversaId}: {$conteudo}\n";

                } catch (\Exception $e) {
                    error_log('ChatServer erro: ' . $e->getMessage());
                }
                break;

            // 4. Usuário digitando (feedback visual)
            case 'typing':
                if (!$from->userId) return;
                $conversaId = (int) ($data['conversa_id'] ?? 0);
                $payload    = json_encode([
                    'type'       => 'typing',
                    'user_nome'  => $from->userName,
                    'conversa_id'=> $conversaId,
                ]);
                foreach ($this->clients as $client) {
                    if ($client !== $from && $client->conversaId === $conversaId) {
                        $client->send($payload);
                    }
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
        echo "Conexão fechada: #{$conn->resourceId} ({$conn->userName}) | Total: {$this->clients->count()}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        error_log("WebSocket erro: " . $e->getMessage());
        $conn->close();
    }
}
