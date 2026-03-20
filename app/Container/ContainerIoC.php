<?php
declare(strict_types=1);
namespace App\Container;

use PDO;
use App\Repositories\UsuarioRepository;
use App\Repositories\SetorRepository;
use App\Repositories\ConversaRepository;
use App\Repositories\MensagemRepository;
use App\Validators\UsuarioValidator;
use App\Validators\MensagemValidator;
use App\Controllers\AdminController;
use App\Controllers\ChatController;
use App\Controllers\AuthController;
use App\Controllers\ChamadoController;

/**
 * Container IoC simples para Dependency Injection
 * Gerencia a criação de instâncias automaticamente
 */
class ContainerIoC
{
    private static array $singletons = [];

    /**
     * Registra um singleton no container
     */
    public static function set(string $name, mixed $instance): void
    {
        self::$singletons[$name] = $instance;
    }

    /**
     * Obtém uma instância do container
     */
    public static function get(string $name): mixed
    {
        if (isset(self::$singletons[$name])) {
            return self::$singletons[$name];
        }

        // Factory automática para classes conhecidas
        $instance = match ($name) {
            'pdo' => self::createPDO(),
            'UsuarioRepository' => new UsuarioRepository(self::get('pdo')),
            'SetorRepository' => new SetorRepository(self::get('pdo')),
            'ConversaRepository' => new ConversaRepository(self::get('pdo')),
            'MensagemRepository' => new MensagemRepository(self::get('pdo')),
            'UsuarioValidator' => new UsuarioValidator(),
            'MensagemValidator' => new MensagemValidator(),
            'AdminController' => new AdminController(
                self::get('UsuarioRepository'),
                self::get('SetorRepository'),
                self::get('UsuarioValidator'),
            ),
            'ChatController' => new ChatController(
                self::get('ConversaRepository'),
                self::get('MensagemRepository'),
                self::get('UsuarioRepository'),
                self::get('MensagemValidator'),
            ),
            'AuthController' => new AuthController(),
            'ChamadoController' => new ChamadoController(),
            default => throw new \RuntimeException("Serviço '{$name}' não encontrado no container"),
        };

        // Armazena singleton
        self::$singletons[$name] = $instance;
        return $instance;
    }

    /**
     * Cria e retorna a instância PDO
     */
    private static function createPDO(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_NAME']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
    }
}
