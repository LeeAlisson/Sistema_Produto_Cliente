<?php

namespace App;

class Router
{
  private array $routes = [];
  private string $currentName = '';

  public function get(string $path, string $name, callable $handler): void
  {
    $this->add('GET', $path, $name, $handler);
  }

  public function post(string $path, string $name, callable $handler): void
  {
    $this->add('POST', $path, $name, $handler);
  }

  private function add(string $method, string $path, string $name, callable $handler): void
  {
    $params = [];
    $pattern = preg_replace_callback('/\{([a-zA-Z_]+)\}/', function ($m) use (&$params) {
      $params[] = $m[1];
      return '([^/]+)';
    }, $path);

    $this->routes[] = [
      'method' => $method,
      'path' => $path,
      'name' => $name,
      'pattern' => '#^' . $pattern . '$#',
      'params' => $params,
      'handler' => $handler,
    ];
  }

  private function normalizePath(string $uri): string
  {
    $path = parse_url($uri, PHP_URL_PATH) ?? '/';
    $base = Url::base();

    if ($base !== '' && str_starts_with($path, $base)) {
      $path = substr($path, strlen($base)) ?: '/';
    }

    $path = '/' . trim($path, '/');
    if ($path !== '/') {
      $path = rtrim($path, '/');
    }

    return $path;
  }

  public function match(string $uri, string $method): ?array
  {
    $path = $this->normalizePath($uri);

    foreach ($this->routes as $route) {
      if ($route['method'] !== $method) {
        continue;
      }

      if (!preg_match($route['pattern'], $path, $matches)) {
        continue;
      }

      $params = [];
      foreach ($route['params'] as $i => $paramName) {
        $params[$paramName] = rawurldecode($matches[$i + 1]);
      }

      return [
        'name' => $route['name'],
        'handler' => $route['handler'],
        'params' => $params,
      ];
    }

    return null;
  }

  public function dispatch(string $uri, string $method): bool
  {
    $matched = $this->match($uri, $method);

    if ($matched === null) {
      return false;
    }

    $this->currentName = $matched['name'];
    $matched['handler']($matched['params']);
    return true;
  }

  public function currentName(): string
  {
    return $this->currentName;
  }

  public function isPublicRoute(string $name): bool
  {
    return in_array($name, ['login', 'login.post'], true);
  }

  public function isApiRoute(string $name): bool
  {
    return str_starts_with($name, 'api.');
  }
}
