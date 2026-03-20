<?php
/**
 * Script de Teste — Valida Refatoração
 * Executa: php tests/test-refactoring.php
 */
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

use Dotenv\Dotenv;
use App\Container\ContainerIoC;
use App\Constants\UserRole;
use App\Constants\ValidationRules;
use App\DTOs\CreateUserDTO;
use App\DTOs\CreateMessageDTO;
use App\Validators\UsuarioValidator;
use App\Validators\MensagemValidator;
use App\Exceptions\ValidationException;

// Carrega environment
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "🧪 INICIANDO TESTES DE REFATORAÇÃO\n";
echo str_repeat("=", 50) . "\n\n";

try {
    // Teste 1: Constants
    echo "✓ Teste 1: Constants\n";
    assert(UserRole::ADMIN === 'admin', "UserRole::ADMIN falhou");
    assert(UserRole::isValid('admin'), "UserRole::isValid falhou");
    assert(!UserRole::isValid('invalid'), "UserRole::isValid deveria rejeitar invalid");
    assert(ValidationRules::MIN_PASSWORD_LENGTH === 6, "ValidationRules falhou");
    echo "  ✓ Constants funcionando\n\n";

    // Teste 2: DTOs
    echo "✓ Teste 2: DTOs (Data Transfer Objects)\n";
    $userDto = CreateUserDTO::fromArray([
        'nome' => 'João Silva',
        'email' => 'joao@test.com',
        'senha' => 'senha123',
        'papel' => 'usuario',
        'setor_id' => 5,
    ]);
    assert($userDto->nome === 'João Silva', "DTO nome falhou");
    assert($userDto->email === 'joao@test.com', "DTO email falhou");
    assert($userDto->papel === 'usuario', "DTO papel falhou");
    echo "  ✓ CreateUserDTO funcionando\n";

    $msgDto = CreateMessageDTO::fromArray([
        'conversa_id' => 123,
        'conteudo' => 'Olá mundo',
    ]);
    assert($msgDto->conversaId === 123, "DTO conversaId falhou");
    assert($msgDto->conteudo === 'Olá mundo', "DTO conteudo falhou");
    echo "  ✓ CreateMessageDTO funcionando\n\n";

    // Teste 3: Validators
    echo "✓ Teste 3: Validators\n";
    $usuarioValidator = new UsuarioValidator();

    // Teste validação email inválido
    try {
        $invalidEmailDto = CreateUserDTO::fromArray([
            'nome' => 'Test',
            'email' => 'invalid-email',
            'senha' => 'senha123',
        ]);
        $usuarioValidator->validateCreate($invalidEmailDto);
        throw new Exception("Deveria ter lançado validação para email inválido");
    } catch (ValidationException $e) {
        assert(str_contains($e->getMessage(), 'E-mail'), "Mensagem de email falhou");
        echo "  ✓ Validação de email funcionando\n";
    }

    // Teste validação senha curta
    try {
        $shortPassDto = CreateUserDTO::fromArray([
            'nome' => 'Test',
            'email' => 'test@test.com',
            'senha' => '123',
        ]);
        $usuarioValidator->validateCreate($shortPassDto);
        throw new Exception("Deveria ter lançado validação para senha curta");
    } catch (ValidationException $e) {
        assert(str_contains($e->getMessage(), 'Senha'), "Mensagem de senha falhou");
        echo "  ✓ Validação de senha funcionando\n";
    }

    // Teste validação papel inválido
    try {
        $invalidRoleDto = CreateUserDTO::fromArray([
            'nome' => 'Test',
            'email' => 'test@test.com',
            'senha' => 'senha123',
            'papel' => 'superadmin', // inválido
        ]);
        $usuarioValidator->validateCreate($invalidRoleDto);
        throw new Exception("Deveria ter lançado validação para papel inválido");
    } catch (ValidationException $e) {
        assert(str_contains($e->getMessage(), 'Papel'), "Mensagem de papel falhou");
        echo "  ✓ Validação de papel funcionando\n";
    }

    // Teste validação mensagem vazia
    $msgValidator = new MensagemValidator();
    try {
        $emptyMsgDto = CreateMessageDTO::fromArray([
            'conversa_id' => 0,
            'conteudo' => '',
        ]);
        $msgValidator->validateCreate($emptyMsgDto);
        throw new Exception("Deveria ter lançado validação para mensagem vazia");
    } catch (ValidationException $e) {
        echo "  ✓ Validação de mensagem funcionando\n";
    }

    // Teste mensagem muito longa
    try {
        $longMsgDto = CreateMessageDTO::fromArray([
            'conversa_id' => 1,
            'conteudo' => str_repeat('x', 5001), // Acima do limite
        ]);
        $msgValidator->validateCreate($longMsgDto);
        throw new Exception("Deveria ter lançado validação para mensagem longa");
    } catch (ValidationException $e) {
        assert(str_contains($e->getMessage(), 'Mensagem muito longa'), "Mensagem longa falhou");
        echo "  ✓ Validação de comprimento de mensagem funcionando\n\n";
    }

    // Teste 4: Container IoC
    echo "✓ Teste 4: Container IoC (Dependency Injection)\n";
    
    $adminController = ContainerIoC::get('AdminController');
    assert($adminController !== null, "AdminController não foi criado");
    assert(class_exists(get_class($adminController)), "AdminController class falhou");
    echo "  ✓ AdminController injetado corretamente\n";

    $chatController = ContainerIoC::get('ChatController');
    assert($chatController !== null, "ChatController não foi criado");
    echo "  ✓ ChatController injetado corretamente\n";

    // Verifica se os Repositories foram injetados
    $usuarioRepo = ContainerIoC::get('UsuarioRepository');
    assert($usuarioRepo !== null, "UsuarioRepository não foi criado");
    echo "  ✓ UsuarioRepository injetado\n";

    $conversaRepo = ContainerIoC::get('ConversaRepository');
    assert($conversaRepo !== null, "ConversaRepository não foi criado");
    echo "  ✓ ConversaRepository injetado\n";

    $msgRepo = ContainerIoC::get('MensagemRepository');
    assert($msgRepo !== null, "MensagemRepository não foi criado");
    echo "  ✓ MensagemRepository injetado\n";

    echo "\n";

    // Teste 5: Exceções
    echo "✓ Teste 5: Exceções Customizadas\n";
    try {
        throw new ValidationException('Teste de erro');
    } catch (ValidationException $e) {
        assert($e->getCode() === 400, "Código de ValidationException falhou");
        echo "  ✓ ValidationException funcionando\n";
    }

    try {
        throw new \App\Exceptions\UnauthorizedException('Acesso negado');
    } catch (\App\Exceptions\UnauthorizedException $e) {
        assert($e->getCode() === 403, "Código de UnauthorizedException falhou");
        echo "  ✓ UnauthorizedException funcionando\n\n";
    }

    // Teste 6: Verificação de métodos
    echo "✓ Teste 6: Métodos dos Controllers\n";
    $reflection = new ReflectionClass($adminController);
    $methods = [
        'listarUsuarios',
        'criarUsuario',
        'atualizarUsuario',
        'desativarUsuario',
        'listarSetores',
        'criarSetor',
        'deletarSetor',
    ];
    foreach ($methods as $method) {
        assert($reflection->hasMethod($method), "AdminController não tem método {$method}");
    }
    echo "  ✓ AdminController tem todos os métodos\n";

    $reflection = new ReflectionClass($chatController);
    $methods = [
        'listarConversas',
        'listarMensagens',
        'enviarMensagem',
        'listarUsuarios',
        'criarConversa',
        'editarConversa',
        'deletarConversa',
        'marcarComoLida',
        'listarParticipantes',
        'adicionarParticipante',
        'removerParticipante',
    ];
    foreach ($methods as $method) {
        assert($reflection->hasMethod($method), "ChatController não tem método {$method}");
    }
    echo "  ✓ ChatController tem todos os métodos\n\n";

    // Resumo final
    echo str_repeat("=", 50) . "\n";
    echo "✅ TODOS OS TESTES PASSARAM!\n";
    echo str_repeat("=", 50) . "\n";
    echo "\n📊 Resumo:\n";
    echo "  • Constants funcionando\n";
    echo "  • DTOs estruturados\n";
    echo "  • Validators reutilizáveis\n";
    echo "  • Container IoC com DI\n";
    echo "  • Exceções customizadas\n";
    echo "  • Métodos dos Controllers\n";
    echo "\n🎉 Refatoração bem-sucedida!\n";

} catch (Exception $e) {
    echo "\n❌ ERRO NO TESTE:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
