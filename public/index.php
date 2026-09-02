<?php

require_once __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Auth\ApiAuth;
use App\Config;
use App\Router;
use App\Security;
use App\View;

Auth::startSession();
Security::sendHeaders();

$router = new Router();
$registerRoutes = require __DIR__ . '/../routes/web.php';
$registerRoutes($router);

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];
$matched = $router->match($uri, $method);

if ($matched === null) {
  http_response_code(404);
  if (str_starts_with(parse_url($uri, PHP_URL_PATH) ?? '', '/api/')) {
    \App\Support\JsonResponse::error(404, 'Endpoint não encontrado.');
  }
  View::render('errors/404', ['errorTitle' => 'Página não encontrada'], 'layout_error');
  exit;
}

if (str_starts_with($matched['name'], 'preview.') && !Config::isDebug()) {
  http_response_code(404);
  View::render('errors/404', ['errorTitle' => 'Página não encontrada'], 'layout_error');
  exit;
}

$skipAuth = $router->isPublicRoute($matched['name'])
  || (Config::isDebug() && str_starts_with($matched['name'], 'preview.'));

if (!$skipAuth) {
  if ($router->isApiRoute($matched['name'])) {
    ApiAuth::require();
  } else {
    Auth::requireAuth();
  }
}

try {
  $router->dispatch($uri, $method);
} catch (Throwable $e) {
  if ($router->isApiRoute($matched['name'])) {
    \App\Support\JsonResponse::error(500, Config::isDebug() ? $e->getMessage() : 'Erro interno.');
  }
  http_response_code(500);
  View::render('errors/500', [
    'errorTitle' => 'Erro interno',
    'debugMessage' => Config::isDebug() ? $e->getMessage() : null,
  ], 'layout_error');
}
