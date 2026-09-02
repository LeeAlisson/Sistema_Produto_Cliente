<?php

/**
 * Troca a senha de login (tabela s00_usuario).
 * Uso: php scripts/change-password.php admin nova_senha
 */

require_once __DIR__ . '/../src/bootstrap.php';

use App\Database;

if ($argc < 3) {
    echo "Uso: php scripts/change-password.php <usuario> <nova_senha>\n";
    exit(1);
}

$username = $argv[1];
$newPassword = $argv[2];

if (strlen($newPassword) < 6) {
    echo "A senha deve ter no mínimo 6 caracteres.\n";
    exit(1);
}

$pdo = Database::getConnection();
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE s00_usuario SET s00_senha = ? WHERE s00_username = ?');
$stmt->execute([$hash, $username]);

if ($stmt->rowCount() === 0) {
    echo "Usuário '{$username}' não encontrado.\n";
    exit(1);
}

echo "Senha atualizada para '{$username}'.\n";
