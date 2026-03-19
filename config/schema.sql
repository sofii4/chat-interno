SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

CREATE TABLE setores (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(100) NOT NULL,
    descricao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(150) NOT NULL,
    email        VARCHAR(255) NOT NULL UNIQUE,
    senha_hash   VARCHAR(255) NOT NULL,
    setor_id     INT UNSIGNED,
    papel        ENUM('admin','ti','usuario') NOT NULL DEFAULT 'usuario',
    ativo        TINYINT(1) NOT NULL DEFAULT 1,
    criado_em    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (setor_id) REFERENCES setores(id) ON DELETE SET NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE conversas (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo       ENUM('privada','grupo','setor') NOT NULL DEFAULT 'privada',
    nome       VARCHAR(150),
    criado_por INT UNSIGNED NOT NULL,
    criado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criado_por) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE participantes (
    conversa_id INT UNSIGNED NOT NULL,
    usuario_id  INT UNSIGNED NOT NULL,
    entrou_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (conversa_id, usuario_id),
    FOREIGN KEY (conversa_id) REFERENCES conversas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mensagens (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversa_id  INT UNSIGNED NOT NULL,
    usuario_id   INT UNSIGNED NOT NULL,
    conteudo     TEXT NOT NULL,
    arquivo_path VARCHAR(500),
    arquivo_nome VARCHAR(255),
    criado_em    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversa_id) REFERENCES conversas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE,
    INDEX idx_conversa_criado (conversa_id, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chamados (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id     INT UNSIGNED NOT NULL,
    atribuido_a    INT UNSIGNED NULL,
    titulo         VARCHAR(255) NOT NULL,
    descricao_rich LONGTEXT NOT NULL,
    status         ENUM('aberto','em_andamento','resolvido','cancelado') NOT NULL DEFAULT 'aberto',
    prioridade     ENUM('baixa','media','alta','critica') NOT NULL DEFAULT 'media',
    criado_em      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (atribuido_a) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chamado_anexos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chamado_id    INT UNSIGNED NOT NULL,
    arquivo_path  VARCHAR(500) NOT NULL,
    arquivo_nome  VARCHAR(255) NOT NULL,
    mime_type     VARCHAR(100),
    tamanho_bytes INT UNSIGNED,
    criado_em     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chamado_id) REFERENCES chamados(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Setor e usuário admin inicial para teste
INSERT INTO setores (nome) VALUES ('TI'), ('Administrativo'), ('Operacional');
INSERT INTO usuarios (nome, email, senha_hash, setor_id, papel)
VALUES ('Administrador', 'admin@empresa.com',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin');
-- senha do admin acima é: password

SET FOREIGN_KEY_CHECKS = 1;
