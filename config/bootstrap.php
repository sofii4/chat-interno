<?php
declare(strict_types=1);

function bootstrapDefaultData(): void
{
    $pdo = getDbConnection();

    seedDefaultSectors($pdo);
    seedDefaultAdminUser($pdo);
}

function seedDefaultSectors(PDO $pdo): void
{
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO setores (nome, descricao) VALUES
            ('TI', 'Setor de tecnologia da informação'),
            ('Administrativo', 'Setor administrativo'),
            ('Operacional', 'Setor operacional')"
    );
    $stmt->execute();
}

function seedDefaultAdminUser(PDO $pdo): void
{
    $nome = trim((string) ($_ENV['ADMIN_NAME'] ?? 'Admin'));
    $email = trim((string) ($_ENV['ADMIN_EMAIL'] ?? 'admin@empresa.com'));
    $senha = (string) ($_ENV['ADMIN_PASSWORD'] ?? 'password');

    if ($nome === '') {
        $nome = 'Admin';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email = 'admin@empresa.com';
    }

    $check = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($check->fetchColumn()) {
        return;
    }

    $setorId = null;
    $stmtSetor = $pdo->prepare('SELECT id FROM setores WHERE nome = ? LIMIT 1');
    $stmtSetor->execute(['TI']);
    $setorId = $stmtSetor->fetchColumn();
    $setorId = $setorId !== false ? (int) $setorId : null;

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nome, email, senha_hash, setor_id, papel)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $nome,
        $email,
        password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]),
        $setorId,
        'admin',
    ]);
}