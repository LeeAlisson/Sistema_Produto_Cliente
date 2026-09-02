<?php

namespace App\Models;

use App\Database;
use PDO;

class Associacao
{
  public static function associar(string $produtoCodigo, string $clienteCodigo): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'INSERT INTO r00_produto_cliente (r00_produto_codigo, r00_cliente_codigo) VALUES (?, ?)'
    ); // PK impede duplicata; o service trata 23000
    return $stmt->execute([$produtoCodigo, $clienteCodigo]);
  }

  public static function desassociar(string $produtoCodigo, string $clienteCodigo): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'DELETE FROM r00_produto_cliente WHERE r00_produto_codigo = ? AND r00_cliente_codigo = ?'
    );
    return $stmt->execute([$produtoCodigo, $clienteCodigo]);
  }

  public static function getAllAssociacoes(): array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->query(
      'SELECT r.r00_produto_codigo, r.r00_cliente_codigo,
              p.p00_descricao, c.c00_nome
       FROM r00_produto_cliente r
       INNER JOIN p00_produto p ON p.p00_codigo = r.r00_produto_codigo
       INNER JOIN c00_cliente c ON c.c00_codigo = r.r00_cliente_codigo
       ORDER BY p.p00_descricao, c.c00_nome'
    );
    return $stmt->fetchAll();
  }

  public static function exists(string $produtoCodigo, string $clienteCodigo): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'SELECT 1 FROM r00_produto_cliente WHERE r00_produto_codigo = ? AND r00_cliente_codigo = ?'
    );
    $stmt->execute([$produtoCodigo, $clienteCodigo]);
    return (bool) $stmt->fetch();
  }
}
