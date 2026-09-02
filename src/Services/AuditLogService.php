<?php

namespace App\Services;

use App\Auth;
use App\Models\AuditLog;

class AuditLogService
{
  public const ACTION_CREATE = 'CREATE';
  public const ACTION_UPDATE = 'UPDATE';
  public const ACTION_DELETE = 'DELETE';
  public const ACTION_ASSOCIATE = 'ASSOCIATE';
  public const ACTION_DISASSOCIATE = 'DISASSOCIATE';
  public const ACTION_LOGIN = 'LOGIN';
  public const ACTION_LOGOUT = 'LOGOUT';

  public static function log(
    string $action,
    string $entity,
    ?string $entityId = null,
    ?array $details = null
  ): void {
    AuditLog::create([
      'usuario_id' => Auth::userId(),
      'username' => Auth::username() ?? 'system',
      'action' => $action,
      'entity' => $entity,
      'entity_id' => $entityId,
      'details' => $details !== null ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
    ]);
  }
}
