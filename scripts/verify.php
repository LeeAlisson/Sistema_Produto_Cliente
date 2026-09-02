<?php

require_once __DIR__ . '/../src/bootstrap.php';

$config = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Conexão OK ({$config['host']}:{$config['port']}/{$config['database']})\n";
} catch (PDOException $e) {
    echo "Falha: " . $e->getMessage() . "\n";
    exit(1);
}

foreach ($pdo->query('SHOW TABLES') as $row) {
    echo "  - {$row[0]}\n";
}

$user = $pdo->query('SELECT s00_username FROM s00_usuario LIMIT 1')->fetch();
echo "Usuário: " . ($user['s00_username'] ?? 'não encontrado') . "\n";
