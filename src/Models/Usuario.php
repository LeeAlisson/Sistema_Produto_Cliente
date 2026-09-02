<?php

namespace App\Models;

use App\Database;
use PDO;

class Usuario
{
  public static function findByUsername(string $username): ?array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT * FROM s00_usuario WHERE s00_username = ? AND s00_ativo = 1');
    $stmt->execute([$username]);
    $result = $stmt->fetch();
    return $result ?: null;
  }

  public static function authenticate(string $username, string $password): ?array
  {
    $usuario = self::findByUsername($username);
    if (!$usuario) {
      return null;
    }

    if (!password_verify($password, $usuario['s00_senha'])) {
      return null;
    }

    return $usuario;
  }
}
