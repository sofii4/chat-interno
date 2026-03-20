# Documentação da Refatoração

## 📁 Estrutura de Pastas Nova

```
app/
├── Constants/           # Constantes da aplicação
│   ├── UserRole.php    # Papéis de usuário (admin, ti, usuario)
│   └── ValidationRules.php  # Regras de validação
├── DTOs/               # Data Transfer Objects
│   ├── CreateUserDTO.php
│   ├── UpdateUserDTO.php
│   └── CreateMessageDTO.php
├── Exceptions/         # Exceções customizadas
│   ├── ValidationException.php
│   └── UnauthorizedException.php
├── Validators/         # Validadores reutilizáveis
│   ├── UsuarioValidator.php
│   └── MensagemValidator.php
├── Repositories/       # Padrão Repository (acesso aos dados)
│   ├── UsuarioRepository.php
│   ├── SetorRepository.php
│   ├── ConversaRepository.php
│   └── MensagemRepository.php
├── Container/          # Injeção de Dependências
│   └── ContainerIoC.php
├── Controllers/        # Controllers (refatorados)
├── Middleware/         # Middleware (não mudou)
├── Models/             # Models (ainda vazios)
├── Services/           # Services (ainda não refatorado)
└── Helpers/            # Helpers
```

## 🔄 Padrões Implementados

### 1. Constants
Centraliza valores hardcoded para evitar duplicação:

```php
use App\Constants\UserRole;
use App\Constants\ValidationRules;

UserRole::ADMIN  // 'admin'
UserRole::isValid('admin')  // true

ValidationRules::MIN_PASSWORD_LENGTH  // 6
ValidationRules::MAX_MESSAGE_LENGTH   // 5000
```

### 2. DTOs (Data Transfer Objects)
Estrutura de dados tipada para requisições:

```php
$dto = CreateUserDTO::fromArray([
    'nome' => 'João',
    'email' => 'joao@test.com',
    'senha' => '123456',
]);

// Acesso com type hints
echo $dto->nome;  // 'João'
echo $dto->email;  // 'joao@test.com
```

### 3. Validators
Lógica de validação reutilizável:

```php
$validator = new UsuarioValidator();
try {
    $validator->validateCreate($dto);
} catch (ValidationException $e) {
    // Trata erro
}
```

### 4. Repositories
Centraliza todo acesso a dados (SQL):

```php
$usuarioRepo = new UsuarioRepository($pdo);

// Métodos disponíveis:
$usuarios = $usuarioRepo->listarTodos();
$usuario = $usuarioRepo->buscarPorId(5);
$usuarioRepo->criar($dto);
$usuarioRepo->atualizar(5, $updateDto);
$usuarioRepo->desativar(5);
```

### 5. Container IoC
Gerencia injeção de dependências:

```php
$controller = ContainerIoC::get('AdminController');
// Retorna AdminController com todos os Repositories injetados

// Ou acesse diretamente um repositório:
$repo = ContainerIoC::get('UsuarioRepository');
```

### 6. Exceções Customizadas
Exceções específicas para tratamento diferenciado:

```php
try {
    $validator->validateCreate($dto);
} catch (ValidationException $e) {
    // Erro de validação (400)
    return Json::erro($response, $e->getMessage(), 400);
} catch (UnauthorizedException $e) {
    // Acesso negado (403)
    return Json::erro($response, $e->getMessage(), 403);
}
```

## 🎯 Benefícios da Refatoração

| Benefício | Antes | Depois |
|-----------|-------|--------|
| **Testabilidade** | Difícil (acoplado) | Fácil (injeção) |
| **Reutilização** | Não (código duplicado) | Sim (validators, repos) |
| **Manutenção** | Difícil (mudanças em N lugares) | Fácil (centralizado) |
| **Legibilidade** | Média (SQL misturado) | Alta (separado) |
| **Escalabilidade** | Baixa (monolítico) | Alta (modular) |

## 🧪 Rodando os Testes

```bash
php tests/test-refactoring.php
```

Testa:
- ✓ Constants funcionando
- ✓ DTOs estruturados
- ✓ Validators reutilizáveis
- ✓ Container IoC com DI
- ✓ Exceções customizadas
- ✓ Métodos dos Controllers

## 📝 Próximos Passos Sugeridos

1. **Completar Models** - Implementar classes Usuario, Mensagem, etc com métodos úteis
2. **Refatorar ChatServer** (WebSocket) - Aplicar mesmo padrão
3. **Refatorar ChamadoController** - Usar Repositories e Validators
4. **Adicionar Testes Unitários** - Com PHPUnit
5. **Adicionar Logging** - Usar PSR-3 compatible logger
6. **Adicionar Cache** - Para queries repetidas

## 🔗 Referências

- **SOLID Principles**: https://en.wikipedia.org/wiki/SOLID
- **Repository Pattern**: https://martinfowler.com/eaaCatalog/repository.html
- **DTO (Transfer Objects)**: https://martinfowler.com/bliki/DataTransferObject.html
- **Dependency Injection**: https://martinfowler.com/articles/injection.html
