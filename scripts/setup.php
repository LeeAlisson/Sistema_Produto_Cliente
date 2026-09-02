<?php

require_once __DIR__ . '/../src/bootstrap.php';

$config = require __DIR__ . '/../config/database.php';

echo "Setup — Sistema Produto x Cliente\n\n";

try {
    $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $config['host'], $config['port'], $config['charset']);
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "Conexão MySQL OK.\n";
} catch (PDOException $e) {
    echo "Falha na conexão: " . $e->getMessage() . "\n";
    echo "\nVerifique DB_PASSWORD no arquivo .env (não rode cp .env.example .env se o .env já existir).\n";
    exit(1);
}

$dbName = $config['database'];
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$dbName}`");

$schemaFile = __DIR__ . '/../database/schema.sql';
$sql = file_get_contents($schemaFile);
$sql = preg_replace('/^--.*$/m', '', $sql);
$sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
$sql = preg_replace('/USE\s+\w+\s*;/i', '', $sql);

foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    if ($statement === '') {
        continue;
    }
    try {
        $pdo->exec($statement);
    } catch (PDOException $e) {
        if (!str_contains($e->getMessage(), 'Duplicate') && !str_contains($e->getMessage(), 'already exists')) {
            echo "Aviso: " . $e->getMessage() . "\n";
        }
    }
}

$indexes = [
    'CREATE INDEX idx_c00_nome ON c00_cliente (c00_nome)',
    'CREATE INDEX idx_c00_cnpj ON c00_cliente (c00_cnpj)',
    'CREATE INDEX idx_p00_descricao ON p00_produto (p00_descricao)',
    'CREATE INDEX idx_r00_cliente ON r00_produto_cliente (r00_cliente_codigo)',
];

foreach ($indexes as $indexSql) {
    try {
        $pdo->exec($indexSql);
    } catch (PDOException $e) {
        if (!str_contains($e->getMessage(), 'Duplicate') && !str_contains($e->getMessage(), 'already exists')) {
            echo "Aviso: " . $e->getMessage() . "\n";
        }
    }
}

$pdo->exec("UPDATE c00_cliente SET c00_cnpj = '52998224725' WHERE c00_codigo = 'CLI001' AND c00_cnpj IN ('12345678901', '52998224725')");
$pdo->exec("UPDATE c00_cliente SET c00_cnpj = '11222333000181' WHERE c00_codigo = 'CLI002' AND (c00_cnpj = '12345678000199' OR c00_cnpj = '11222333000181')");

$appUrl = \App\Config::url() ?: 'http://localhost:8080';
echo "\nSetup concluído.\n";
echo "URL: {$appUrl}/login\n";
echo "Credenciais padrão: admin / admin123\n";
