<?php

use App\Controllers\Api\AssociacaoApiController;
use App\Controllers\Api\ClienteApiController;
use App\Controllers\Api\ProdutoApiController;
use App\Controllers\AssociacaoController;
use App\Controllers\AuditController;
use App\Controllers\AuthController;
use App\Controllers\ClienteController;
use App\Controllers\HomeController;
use App\Controllers\ProdutoController;
use App\Config;
use App\View;
use App\Router;

$authController = new AuthController();
$homeController = new HomeController();
$produtoController = new ProdutoController();
$clienteController = new ClienteController();
$associacaoController = new AssociacaoController();
$auditController = new AuditController();

$produtoApi = new ProdutoApiController();
$clienteApi = new ClienteApiController();
$associacaoApi = new AssociacaoApiController();

return function (Router $router) use (
  $authController,
  $homeController,
  $produtoController,
  $clienteController,
  $associacaoController,
  $auditController,
  $produtoApi,
  $clienteApi,
  $associacaoApi
): void {
  $router->get('/login', 'login', fn() => $authController->showLogin());
  $router->post('/login', 'login.post', fn() => $authController->login());

  $router->get('/', 'home', fn() => $homeController->index());
  $router->post('/logout', 'logout', fn() => $authController->logout());

  $router->get('/produtos', 'produtos.index', fn() => $produtoController->index());
  $router->get('/produtos/export', 'produtos.export', fn() => $produtoController->export());
  $router->get('/produtos/create', 'produtos.create', fn() => $produtoController->create());
  $router->post('/produtos', 'produtos.store', fn() => $produtoController->store());
  $router->get('/produtos/{codigo}', 'produtos.show', fn($p) => $produtoController->show($p['codigo']));
  $router->get('/produtos/{codigo}/edit', 'produtos.edit', fn($p) => $produtoController->edit($p['codigo']));
  $router->post('/produtos/{codigo}/edit', 'produtos.update', fn($p) => $produtoController->update($p['codigo']));
  $router->post('/produtos/{codigo}/delete', 'produtos.delete', fn($p) => $produtoController->delete($p['codigo']));

  $router->get('/clientes', 'clientes.index', fn() => $clienteController->index());
  $router->get('/clientes/export', 'clientes.export', fn() => $clienteController->export());
  $router->get('/clientes/create', 'clientes.create', fn() => $clienteController->create());
  $router->post('/clientes', 'clientes.store', fn() => $clienteController->store());
  $router->get('/clientes/{codigo}', 'clientes.show', fn($p) => $clienteController->show($p['codigo']));
  $router->get('/clientes/{codigo}/edit', 'clientes.edit', fn($p) => $clienteController->edit($p['codigo']));
  $router->post('/clientes/{codigo}/edit', 'clientes.update', fn($p) => $clienteController->update($p['codigo']));
  $router->post('/clientes/{codigo}/delete', 'clientes.delete', fn($p) => $clienteController->delete($p['codigo']));

  $router->get('/associacao', 'associacao.index', fn() => $associacaoController->index());
  $router->post('/associacao', 'associacao.store', fn() => $associacaoController->store());
  $router->post('/associacao/delete', 'associacao.delete', fn() => $associacaoController->delete());

  $router->get('/auditoria', 'auditoria.index', fn() => $auditController->index());

  $router->get('/api/produtos', 'api.produtos.index', fn() => $produtoApi->index());
  $router->get('/api/produtos/{codigo}', 'api.produtos.show', fn($p) => $produtoApi->show($p['codigo']));
  $router->post('/api/produtos', 'api.produtos.store', fn() => $produtoApi->store());
  $router->post('/api/produtos/{codigo}/edit', 'api.produtos.update', fn($p) => $produtoApi->update($p['codigo']));
  $router->post('/api/produtos/{codigo}/delete', 'api.produtos.delete', fn($p) => $produtoApi->delete($p['codigo']));

  $router->get('/api/clientes', 'api.clientes.index', fn() => $clienteApi->index());
  $router->get('/api/clientes/{codigo}', 'api.clientes.show', fn($p) => $clienteApi->show($p['codigo']));
  $router->post('/api/clientes', 'api.clientes.store', fn() => $clienteApi->store());
  $router->post('/api/clientes/{codigo}/edit', 'api.clientes.update', fn($p) => $clienteApi->update($p['codigo']));
  $router->post('/api/clientes/{codigo}/delete', 'api.clientes.delete', fn($p) => $clienteApi->delete($p['codigo']));

  $router->get('/api/associacoes', 'api.associacoes.index', fn() => $associacaoApi->index());
  $router->post('/api/associacoes', 'api.associacoes.store', fn() => $associacaoApi->store());
  $router->post('/api/associacoes/delete', 'api.associacoes.delete', fn() => $associacaoApi->delete());

  if (Config::isDebug()) {
    $router->get('/_preview/error-500', 'preview.error500', function () {
      http_response_code(500);
      View::render('errors/500', [
        'errorTitle' => 'Erro interno',
        'debugMessage' => 'SQLSTATE[HY000] [1045] Access denied for user \'root\'@\'localhost\' (using password: YES)',
      ], 'layout_error');
    });
  }
};
