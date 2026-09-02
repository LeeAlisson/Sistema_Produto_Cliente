<?php

namespace App;

class Url
{
  private static array $map = [
    'home' => '/',
    'login' => '/login',
    'logout' => '/logout',
    'produtos.index' => '/produtos',
    'produtos.create' => '/produtos/create',
    'produtos.store' => '/produtos',
    'produtos.show' => '/produtos/{codigo}',
    'produtos.edit' => '/produtos/{codigo}/edit',
    'produtos.update' => '/produtos/{codigo}/edit',
    'produtos.delete' => '/produtos/{codigo}/delete',
    'clientes.index' => '/clientes',
    'clientes.create' => '/clientes/create',
    'clientes.store' => '/clientes',
    'clientes.show' => '/clientes/{codigo}',
    'clientes.edit' => '/clientes/{codigo}/edit',
    'clientes.update' => '/clientes/{codigo}/edit',
    'clientes.delete' => '/clientes/{codigo}/delete',
    'associacao.index' => '/associacao',
    'associacao.store' => '/associacao',
    'associacao.delete' => '/associacao/delete',
    'auditoria.index' => '/auditoria',
  ];

  public static function base(): string
  {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($script));
    if ($dir === '/' || $dir === '.') {
      return '';
    }
    return rtrim($dir, '/');
  }

  public static function to(string $name, array $params = []): string
  {
    $path = self::$map[$name] ?? '/';

    foreach ($params as $key => $value) {
      $path = str_replace('{' . $key . '}', rawurlencode((string) $value), $path);
    }

    return self::base() . $path;
  }

  public static function currentPath(): string
  {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = self::base();

    if ($base !== '' && str_starts_with($path, $base)) {
      $path = substr($path, strlen($base)) ?: '/';
    }

    $path = '/' . trim($path, '/');
    return $path === '/' ? '/' : rtrim($path, '/');
  }

  public static function asset(string $path): string
  {
    return self::base() . '/' . ltrim($path, '/');
  }
}
