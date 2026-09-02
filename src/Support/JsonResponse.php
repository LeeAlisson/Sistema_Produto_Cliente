<?php

namespace App\Support;

class JsonResponse
{
  public static function send(int $status, array $payload): void
  {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
  }

  public static function success(array $data = [], int $status = 200): void
  {
    self::send($status, ['success' => true, 'data' => $data]);
  }

  public static function error(int $status, string $message, array $errors = []): void
  {
    self::send($status, [
      'success' => false,
      'message' => $message,
      'errors' => $errors,
    ]);
  }
}
