<?php

require_once __DIR__ . '/../src/bootstrap.php';

use App\Database;
use App\Models\Usuario;

$usuario = Usuario::findByUsername('admin');
if (!$usuario) {
    echo "Usuário admin não encontrado.\n";
    exit(1);
}

if (password_verify('admin123', $usuario['s00_senha'])) {
    echo "Senha admin123 já válida.\n";
    exit(0);
}

$pdo = Database::getConnection();
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$pdo->prepare('UPDATE s00_usuario SET s00_senha = ? WHERE s00_username = ?')->execute([$hash, 'admin']);
echo "Senha admin corrigida para admin123.\n";
