CREATE DATABASE IF NOT EXISTS produto_cliente
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE produto_cliente;

-- c00_cnpj guarda CPF ou CNPJ (só dígitos). PF = 11, PJ = 14.
-- c00_data_nascimento: AAAAMMDD, sem separador.
CREATE TABLE IF NOT EXISTS c00_cliente (
    c00_codigo CHAR(6) NOT NULL,
    c00_nome VARCHAR(60) NOT NULL,
    c00_pessoa VARCHAR(1) NOT NULL,
    c00_cnpj VARCHAR(14) NULL,
    c00_estado CHAR(2) NOT NULL,
    c00_data_nascimento CHAR(8) NOT NULL,
    PRIMARY KEY (c00_codigo),
    INDEX idx_c00_nome (c00_nome),
    INDEX idx_c00_cnpj (c00_cnpj),
    CONSTRAINT chk_c00_pessoa CHECK (c00_pessoa IN ('J', 'F', 'O'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- p00_imposto é percentual. O valor em reais é calculado na aplicação (preço × %).
CREATE TABLE IF NOT EXISTS p00_produto (
    p00_codigo CHAR(15) NOT NULL,
    p00_descricao VARCHAR(45) NOT NULL,
    p00_preco DECIMAL(10, 2) NOT NULL,
    p00_imposto DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (p00_codigo),
    INDEX idx_p00_descricao (p00_descricao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- N:N. PK composta evita o mesmo par duas vezes.
CREATE TABLE IF NOT EXISTS r00_produto_cliente (
    r00_produto_codigo CHAR(15) NOT NULL,
    r00_cliente_codigo CHAR(6) NOT NULL,
    PRIMARY KEY (r00_produto_codigo, r00_cliente_codigo),
    CONSTRAINT fk_r00_produto
        FOREIGN KEY (r00_produto_codigo) REFERENCES p00_produto (p00_codigo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_r00_cliente
        FOREIGN KEY (r00_cliente_codigo) REFERENCES c00_cliente (c00_codigo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_r00_cliente (r00_cliente_codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS s00_usuario (
    s00_id INT AUTO_INCREMENT NOT NULL,
    s00_username VARCHAR(50) NOT NULL,
    s00_senha VARCHAR(255) NOT NULL,
    s00_nome VARCHAR(100) NOT NULL,
    s00_ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (s00_id),
    UNIQUE KEY uk_s00_username (s00_username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS s00_audit_log (
    s00_id BIGINT AUTO_INCREMENT NOT NULL,
    s00_usuario_id INT NULL,
    s00_username VARCHAR(50) NOT NULL,
    s00_action VARCHAR(20) NOT NULL,
    s00_entity VARCHAR(30) NOT NULL,
    s00_entity_id VARCHAR(50) NULL,
    s00_details TEXT NULL,
    s00_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (s00_id),
    INDEX idx_audit_created (s00_created_at),
    INDEX idx_audit_entity (s00_entity, s00_entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO s00_usuario (s00_username, s00_senha, s00_nome) VALUES
    ('admin', '$2y$10$G/7V9BHEuVfWxw57qpjMvO.wUr6jVBq.QePaThxc.6rejx52lQxR6', 'Administrador');

INSERT INTO p00_produto (p00_codigo, p00_descricao, p00_preco, p00_imposto) VALUES
    ('PROD001', 'Notebook Dell Inspiron', 4500.00, 18.00),
    ('PROD002', 'Mouse Logitech MX', 350.00, 12.00),
    ('PROD003', 'Teclado Mecânico RGB', 580.00, 15.00);

INSERT INTO c00_cliente (c00_codigo, c00_nome, c00_pessoa, c00_cnpj, c00_estado, c00_data_nascimento) VALUES
    ('CLI001', 'João Silva Santos', 'F', '52998224725', 'SP', '19850315'),
    ('CLI002', 'Tech Solutions Ltda', 'J', '11222333000181', 'RJ', '20100101'),
    ('CLI003', 'Maria Oliveira', 'F', '98765432100', 'MG', '19920722');

INSERT INTO r00_produto_cliente (r00_produto_codigo, r00_cliente_codigo) VALUES
    ('PROD001', 'CLI001'),
    ('PROD001', 'CLI002'),
    ('PROD002', 'CLI001'),
    ('PROD003', 'CLI003');
