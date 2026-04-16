<?php
declare(strict_types=1);

function bootstrapDefaultData(): void
{
    $pdo = getDbConnection();

    $lockName = 'bootstrap_default_data';
    $lockStmt = $pdo->prepare('SELECT GET_LOCK(?, 10)');
    $lockStmt->execute([$lockName]);
    $lockAcquired = (int) $lockStmt->fetchColumn() === 1;

    try {
        ensureUniqueSectorNames($pdo);
        seedDefaultSectors($pdo);
        seedDefaultAdminUser($pdo);
    } finally {
        if ($lockAcquired) {
            $unlockStmt = $pdo->prepare('SELECT RELEASE_LOCK(?)');
            $unlockStmt->execute([$lockName]);
        }
    }
}

function ensureUniqueSectorNames(PDO $pdo): void
{
    static $alreadyChecked = false;
    if ($alreadyChecked) {
        return;
    }

    $alreadyChecked = true;

    deduplicateSectors($pdo);

    $stmtIndex = $pdo->query(
        "SELECT COUNT(*)
         FROM information_schema.statistics
         WHERE table_schema = DATABASE()
           AND table_name = 'setores'
           AND index_name = 'uniq_setores_nome'"
    );
    $hasUniqueIndex = (int) $stmtIndex->fetchColumn() > 0;

    if (!$hasUniqueIndex) {
        try {
            $pdo->exec('ALTER TABLE setores ADD UNIQUE KEY uniq_setores_nome (nome)');
        } catch (\PDOException $e) {
            // Se houve corrida de inicializacao ou nomes com espacos/case variantes,
            // tenta deduplicar mais uma vez e reaplicar a constraint.
            deduplicateSectors($pdo);
            try {
                $pdo->exec('ALTER TABLE setores ADD UNIQUE KEY uniq_setores_nome (nome)');
            } catch (\PDOException $finalError) {
                error_log('Nao foi possivel criar uniq_setores_nome: ' . $finalError->getMessage());
            }
        }
    }
}

function deduplicateSectors(PDO $pdo): void
{
    $pdo->exec('UPDATE setores SET nome = TRIM(nome)');

    $rows = $pdo->query('SELECT id, nome FROM setores ORDER BY id ASC')->fetchAll();
    if (!$rows) {
        return;
    }

    $canonicalToKeepId = [];
    $duplicateIdToKeepId = [];

    foreach ($rows as $row) {
        $id = (int) ($row['id'] ?? 0);
        $name = trim((string) ($row['nome'] ?? ''));
        if ($id <= 0 || $name === '') {
            continue;
        }

        $canonical = mb_strtolower($name, 'UTF-8');
        if (!isset($canonicalToKeepId[$canonical])) {
            $canonicalToKeepId[$canonical] = $id;
            continue;
        }

        $duplicateIdToKeepId[$id] = $canonicalToKeepId[$canonical];
    }

    foreach ($duplicateIdToKeepId as $duplicateId => $keepId) {
        $stmtUpdateUsers = $pdo->prepare('UPDATE usuarios SET setor_id = ? WHERE setor_id = ?');
        $stmtUpdateUsers->execute([$keepId, $duplicateId]);

        $stmtDeleteDup = $pdo->prepare('DELETE FROM setores WHERE id = ?');
        $stmtDeleteDup->execute([$duplicateId]);
    }
}

function seedDefaultSectors(PDO $pdo): void
{
    $defaultSectors = [
        ['TI', 'Setor de tecnologia da informacao'],
        ['Administrativo', 'Setor administrativo'],
        ['Engenharia', 'Setor de engenharia'],
        ['Financeiro', 'Setor financeiro'],
        ['Operacional', 'Setor operacional'],
        ['Vendas', 'Setor comercial e vendas'],
    ];

    $stmt = $pdo->prepare(
        'INSERT INTO setores (nome, descricao)
         SELECT ?, ?
         WHERE NOT EXISTS (
             SELECT 1 FROM setores WHERE nome = ? LIMIT 1
         )'
    );

    foreach ($defaultSectors as [$name, $description]) {
        $stmt->execute([$name, $description, $name]);
    }
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