<?php

namespace App;

class Security
{
  public static function sendHeaders(): void
  {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-XSS-Protection: 1; mode=block');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header(
      "Content-Security-Policy: default-src 'self'; " .
      "script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; " .
      "style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com 'unsafe-inline'; " .
      "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com; " .
      "img-src 'self' data:; " .
      "connect-src 'self'; " .
      "frame-ancestors 'self'; " .
      "base-uri 'self'; " .
      "form-action 'self'"
    );
  }

  public static function configureSession(): void
  {
    $sessionName = Config::get('SESSION_NAME', 'produto_cliente_session');
    if ($sessionName) {
      session_name($sessionName);
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
      ini_set('session.cookie_secure', '1');
    }
  }

  public static function isLoginLocked(): bool
  {
    $until = $_SESSION['login_lockout_until'] ?? 0;
    return $until > time();
  }

  public static function loginLockoutMinutes(): int
  {
    $until = $_SESSION['login_lockout_until'] ?? 0;
    return max(1, (int) ceil(($until - time()) / 60));
  }

  public static function recordFailedLogin(): void
  {
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $maxAttempts = Config::int('LOGIN_MAX_ATTEMPTS', 5);

    if ($_SESSION['login_attempts'] >= $maxAttempts) {
      $_SESSION['login_lockout_until'] = time() + Config::int('LOGIN_LOCKOUT_SECONDS', 900);
    }
  }

  public static function clearLoginAttempts(): void
  {
    unset($_SESSION['login_attempts'], $_SESSION['login_lockout_until']);
  }

  public static function validateProdutoCodigo(string $codigo): bool
  {
    return (bool) preg_match('/^[A-Za-z0-9]{1,15}$/', $codigo);
  }

  public static function validateClienteCodigo(string $codigo): bool
  {
    return (bool) preg_match('/^[A-Za-z0-9]{1,6}$/', $codigo);
  }

  public static function sanitizeCodigo(string $codigo, int $maxLength): string
  {
    return substr(preg_replace('/[^A-Za-z0-9]/', '', $codigo), 0, $maxLength);
  }
}
