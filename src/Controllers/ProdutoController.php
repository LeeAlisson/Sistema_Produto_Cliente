<?php

namespace App\Controllers;

use App\Auth;
use App\Exceptions\BusinessException;
use App\Services\ProdutoService;
use App\Support\CsvResponse;
use App\View;

class ProdutoController
{
  private ProdutoService $service;

  public function __construct()
  {
    $this->service = new ProdutoService();
  }

  public function index(): void
  {
    $search = trim($_GET['q'] ?? '');
    $page = (int) ($_GET['page'] ?? 1);
    $result = $this->service->listPaginated($search !== '' ? $search : null, $page);

    View::render('produtos/index', [
      'produtos' => $result['items'],
      'pagination' => $result['pagination'],
      'search' => $search,
    ]);
  }

  public function export(): void
  {
    $search = trim($_GET['q'] ?? '');
    $produtos = $this->service->listForExport($search !== '' ? $search : null);

    $rows = [];
    foreach ($produtos as $p) {
      $rows[] = [
        $p['p00_codigo'],
        $p['p00_descricao'],
        number_format((float) $p['p00_preco'], 2, ',', '.'),
        number_format((float) $p['p00_imposto'], 2, ',', '.') . '%',
        number_format((float) $p['valor_imposto'], 2, ',', '.'),
      ];
    }

    CsvResponse::send('produtos.csv', [
      'Código',
      'Descrição',
      'Preço',
      'Imposto (%)',
      'Valor do Imposto',
    ], $rows);
  }

  public function create(): void
  {
    View::render('produtos/form', [
      'produto' => null,
      'action' => 'create',
      'titulo' => 'Novo produto',
    ]);
  }

  public function store(): void
  {
    Auth::requireCsrf();

    $errors = $this->service->validateForm($_POST, true);
    if ($errors) {
      View::render('produtos/form', [
        'produto' => $_POST,
        'action' => 'create',
        'titulo' => 'Novo produto',
        'errors' => $errors,
      ]);
      return;
    }

    try {
      $this->service->create($_POST);
      View::setFlash('success', 'Produto cadastrado.');
      View::redirect('produtos.index');
    } catch (BusinessException $e) {
      View::render('produtos/form', [
        'produto' => $_POST,
        'action' => 'create',
        'titulo' => 'Novo produto',
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  public function show(string $codigo): void
  {
    try {
      $produto = $this->service->findOrFail($codigo);
      $clientes = $this->service->getClientesAssociados($codigo);

      View::render('produtos/show', [
        'produto' => $produto,
        'clientes' => $clientes,
      ]);
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
      View::redirect('produtos.index');
    }
  }

  public function edit(string $codigo): void
  {
    try {
      $produto = $this->service->findOrFail($codigo);

      View::render('produtos/form', [
        'produto' => $produto,
        'action' => 'edit',
        'titulo' => 'Editar produto',
      ]);
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
      View::redirect('produtos.index');
    }
  }

  public function update(string $codigo): void
  {
    Auth::requireCsrf();

    $errors = $this->service->validateForm($_POST, false);
    if ($errors) {
      $_POST['p00_codigo'] = $codigo;
      View::render('produtos/form', [
        'produto' => $_POST,
        'action' => 'edit',
        'titulo' => 'Editar produto',
        'errors' => $errors,
      ]);
      return;
    }

    try {
      $this->service->update($codigo, $_POST);
      View::setFlash('success', 'Produto atualizado.');
      View::redirect('produtos.show', ['codigo' => $codigo]);
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
      View::redirect('produtos.edit', ['codigo' => $codigo]);
    }
  }

  public function delete(string $codigo): void
  {
    Auth::requireCsrf();

    try {
      $this->service->delete($codigo);
      View::setFlash('success', 'Produto excluído.');
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
    }

    View::redirect('produtos.index');
  }
}
