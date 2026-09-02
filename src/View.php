<?php

namespace App;

class View
{
  public static function render(string $view, array $data = [], string $layout = 'layout'): void
  {
    $data['view'] = $view;
    extract($data);
    require __DIR__ . '/Views/' . $layout . '.php';
  }

  public static function partial(string $partial, array $data = []): void
  {
    extract($data);
    require __DIR__ . '/Views/' . $partial . '.php';
  }

  public static function escape(?string $value): string
  {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
  }

  public static function redirect(string $routeName, array $params = []): void
  {
    $url = str_starts_with($routeName, '/') || str_starts_with($routeName, 'http')
      ? $routeName
      : Url::to($routeName, $params);
    header('Location: ' . $url);
    exit;
  }

  public static function setFlash(string $type, string $message): void
  {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
  }

  public static function getFlash(): ?array
  {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
  }

  public static function csrfField(): string
  {
    $token = Auth::csrfToken();
    return '<input type="hidden" name="csrf_token" value="' . self::escape($token) . '">';
  }

  public static function url(string $name, array $params = []): string
  {
    return Url::to($name, $params);
  }

  public static function asset(string $path): string
  {
    return Url::asset($path);
  }
}
