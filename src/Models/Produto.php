<?php

namespace App\Models;

use App\Database;
use PDO;

class Produto
{
  public static function all(): array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->query('SELECT * FROM p00_produto ORDER BY p00_codigo');
    return $stmt->fetchAll();
  }

  public static function paginate(?string $search, int $page, int $perPage): array
  {
    $pdo = Database::getConnection();
    $where = '';
    $params = [];

    if ($search !== null && $search !== '') {
      $where = 'WHERE p00_codigo LIKE ? OR p00_descricao LIKE ?';
      $term = '%' . $search . '%';
      $params = [$term, $term];
    }

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM p00_produto' . ($where ? ' ' . $where : ''));
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $offset = ($page - 1) * $perPage;
    $sql = 'SELECT * FROM p00_produto' . ($where ? ' ' . $where : '') . ' ORDER BY p00_codigo LIMIT ? OFFSET ?';
    $stmt = $pdo->prepare($sql);

    $bindParams = array_merge($params, [$perPage, $offset]);
    foreach ($bindParams as $i => $value) {
      $stmt->bindValue($i + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();

    return [
      'items' => $stmt->fetchAll(),
      'total' => $total,
    ];
  }

  public static function find(string $codigo): ?array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT * FROM p00_produto WHERE p00_codigo = ?');
    $stmt->execute([$codigo]);
    $result = $stmt->fetch();
    return $result ?: null;
  }

  public static function create(array $data): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'INSERT INTO p00_produto (p00_codigo, p00_descricao, p00_preco, p00_imposto) VALUES (?, ?, ?, ?)'
    );
    return $stmt->execute([
      $data['p00_codigo'],
      $data['p00_descricao'],
      $data['p00_preco'],
      $data['p00_imposto'],
    ]);
  }

  public static function update(string $codigo, array $data): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'UPDATE p00_produto SET p00_descricao = ?, p00_preco = ?, p00_imposto = ? WHERE p00_codigo = ?'
    );
    return $stmt->execute([
      $data['p00_descricao'],
      $data['p00_preco'],
      $data['p00_imposto'],
      $codigo,
    ]);
  }

  public static function delete(string $codigo): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('DELETE FROM p00_produto WHERE p00_codigo = ?');
    return $stmt->execute([$codigo]);
  }

  public static function getClientesAssociados(string $codigo): array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'SELECT c.* FROM c00_cliente c
       INNER JOIN r00_produto_cliente r ON r.r00_cliente_codigo = c.c00_codigo
       WHERE r.r00_produto_codigo = ?
       ORDER BY c.c00_nome'
    );
    $stmt->execute([$codigo]);
    return $stmt->fetchAll();
  }

  public static function calcularValorImposto(float $preco, float $impostoPercentual): float
  {
    return round($preco * ($impostoPercentual / 100), 2);
  }

  public static function temAssociacoes(string $codigo): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'SELECT 1 FROM r00_produto_cliente WHERE r00_produto_codigo = ? LIMIT 1'
    );
    $stmt->execute([$codigo]);
    return (bool) $stmt->fetchColumn();
  }
}
