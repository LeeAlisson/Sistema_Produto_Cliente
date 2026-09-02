<?php

namespace App\Models;

use App\Database;

class AuditLog
{
  public static function create(array $data): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'INSERT INTO s00_audit_log (s00_usuario_id, s00_username, s00_action, s00_entity, s00_entity_id, s00_details)
       VALUES (?, ?, ?, ?, ?, ?)'
    );

    return $stmt->execute([
      $data['usuario_id'],
      $data['username'],
      $data['action'],
      $data['entity'],
      $data['entity_id'],
      $data['details'],
    ]);
  }

  public static function paginate(int $page, int $perPage): array
  {
    $pdo = Database::getConnection();
    $offset = ($page - 1) * $perPage;

    $total = (int) $pdo->query('SELECT COUNT(*) FROM s00_audit_log')->fetchColumn();

    $stmt = $pdo->prepare(
      'SELECT * FROM s00_audit_log ORDER BY s00_created_at DESC, s00_id DESC LIMIT ? OFFSET ?'
    );
    $stmt->bindValue(1, $perPage, \PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return [
      'items' => $stmt->fetchAll(),
      'total' => $total,
    ];
  }
}
