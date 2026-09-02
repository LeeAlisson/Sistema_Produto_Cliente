<?php

namespace App;

class Auth
{
  public static function startSession(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      Security::configureSession();
      session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Strict',
      ]);
      session_start();
    }
  }

  public static function check(): bool
  {
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
  }

  public static function userId(): ?int
  {
    return self::check() ? (int) $_SESSION['user_id'] : null;
  }

  public static function username(): ?string
  {
    return self::check() ? $_SESSION['username'] : null;
  }

  public static function login(int $userId, string $username): void
  {
    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    self::regenerateCsrfToken();
    Security::clearLoginAttempts();
  }

  public static function logout(): void
  {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
      );
    }

    session_destroy();
  }

  public static function requireAuth(): void
  {
    if (!self::check()) {
      View::setFlash('warning', 'Faça login para continuar.');
      View::redirect('login');
    }
  }

  public static function regenerateCsrfToken(): string
  {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
  }

  public static function csrfToken(): string
  {
    if (empty($_SESSION['csrf_token'])) {
      return self::regenerateCsrfToken();
    }
    return $_SESSION['csrf_token'];
  }

  public static function validateCsrf(?string $token): bool
  {
    if ($token === null || empty($_SESSION['csrf_token'])) {
      return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
  }

  public static function requireCsrf(): void
  {
    $token = $_POST['csrf_token'] ?? '';
    if (!self::validateCsrf($token)) {
      http_response_code(403);
      View::render('errors/403', [
        'errorTitle' => 'Acesso negado',
        'message' => 'Token de segurança inválido. Tente novamente.',
      ], 'layout_error');
      exit;
    }
  }
}
