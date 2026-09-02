-- Referência PostgreSQL do enunciado. O app em Docker usa MySQL (schema.sql).

CREATE TABLE IF NOT EXISTS c00_cliente (
    c00_codigo CHAR(6) NOT NULL,
    c00_nome VARCHAR(60) NOT NULL,
    c00_pessoa VARCHAR(1) NOT NULL,
    c00_cnpj VARCHAR(14),
    c00_estado CHAR(2) NOT NULL,
    c00_data_nascimento CHAR(8) NOT NULL,
    PRIMARY KEY (c00_codigo),
    CONSTRAINT chk_c00_pessoa CHECK (c00_pessoa IN ('J', 'F', 'O'))
);

CREATE INDEX IF NOT EXISTS idx_c00_nome ON c00_cliente (c00_nome);
CREATE INDEX IF NOT EXISTS idx_c00_cnpj ON c00_cliente (c00_cnpj);

CREATE TABLE IF NOT EXISTS p00_produto (
    p00_codigo CHAR(15) NOT NULL,
    p00_descricao VARCHAR(45) NOT NULL,
    p00_preco NUMERIC(10, 2) NOT NULL,
    p00_imposto NUMERIC(10, 2) NOT NULL,
    PRIMARY KEY (p00_codigo)
);

CREATE INDEX IF NOT EXISTS idx_p00_descricao ON p00_produto (p00_descricao);

CREATE TABLE IF NOT EXISTS r00_produto_cliente (
    r00_produto_codigo CHAR(15) NOT NULL,
    r00_cliente_codigo CHAR(6) NOT NULL,
    PRIMARY KEY (r00_produto_codigo, r00_cliente_codigo),
    CONSTRAINT fk_r00_produto
        FOREIGN KEY (r00_produto_codigo) REFERENCES p00_produto (p00_codigo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_r00_cliente
        FOREIGN KEY (r00_cliente_codigo) REFERENCES c00_cliente (c00_codigo)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_r00_cliente ON r00_produto_cliente (r00_cliente_codigo);
