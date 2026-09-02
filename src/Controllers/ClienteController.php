<?php

namespace App\Controllers;

use App\Auth;
use App\Exceptions\BusinessException;
use App\Models\Cliente;
use App\Services\ClienteService;
use App\Support\CsvResponse;
use App\View;

class ClienteController
{
  private ClienteService $service;

  public function __construct()
  {
    $this->service = new ClienteService();
  }

  public function index(): void
  {
    $search = trim($_GET['q'] ?? '');
    $page = (int) ($_GET['page'] ?? 1);
    $result = $this->service->listPaginated($search !== '' ? $search : null, $page);

    View::render('clientes/index', [
      'clientes' => $result['items'],
      'pagination' => $result['pagination'],
      'search' => $search,
    ]);
  }

  public function export(): void
  {
    $search = trim($_GET['q'] ?? '');
    $clientes = $this->service->listForExport($search !== '' ? $search : null);

    $rows = [];
    foreach ($clientes as $c) {
      $rows[] = [
        $c['c00_codigo'],
        $c['c00_nome'],
        Cliente::TIPOS_PESSOA[$c['c00_pessoa']] ?? $c['c00_pessoa'],
        Cliente::formatarDocumento($c['c00_cnpj'] ?? '', $c['c00_pessoa']),
        $c['c00_estado'],
        Cliente::formatarDataExibicao($c['c00_data_nascimento']),
      ];
    }

    CsvResponse::send('clientes.csv', [
      'Código',
      'Nome',
      'Tipo',
      'Documento',
      'UF',
      'Nascimento',
    ], $rows);
  }

  public function create(): void
  {
    View::render('clientes/form', [
      'cliente' => null,
      'action' => 'create',
      'titulo' => 'Novo cliente',
    ]);
  }

  public function store(): void
  {
    Auth::requireCsrf();

    $errors = $this->service->validateForm($_POST, true);
    if ($errors) {
      View::render('clientes/form', [
        'cliente' => $_POST,
        'action' => 'create',
        'titulo' => 'Novo cliente',
        'errors' => $errors,
      ]);
      return;
    }

    try {
      $this->service->create($_POST);
      View::setFlash('success', 'Cliente cadastrado.');
      View::redirect('clientes.index');
    } catch (BusinessException $e) {
      View::render('clientes/form', [
        'cliente' => $_POST,
        'action' => 'create',
        'titulo' => 'Novo cliente',
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  public function show(string $codigo): void
  {
    try {
      $cliente = $this->service->findOrFail($codigo);
      $produtos = $this->service->getProdutosAssociados($codigo);

      View::render('clientes/show', [
        'cliente' => $cliente,
        'produtos' => $produtos,
      ]);
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
      View::redirect('clientes.index');
    }
  }

  public function edit(string $codigo): void
  {
    try {
      $cliente = $this->service->findOrFail($codigo);

      View::render('clientes/form', [
        'cliente' => $cliente,
        'action' => 'edit',
        'titulo' => 'Editar cliente',
      ]);
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
      View::redirect('clientes.index');
    }
  }

  public function update(string $codigo): void
  {
    Auth::requireCsrf();

    $errors = $this->service->validateForm($_POST, false);
    if ($errors) {
      $_POST['c00_codigo'] = $codigo;
      View::render('clientes/form', [
        'cliente' => $_POST,
        'action' => 'edit',
        'titulo' => 'Editar cliente',
        'errors' => $errors,
      ]);
      return;
    }

    try {
      $this->service->update($codigo, $_POST);
      View::setFlash('success', 'Cliente atualizado.');
      View::redirect('clientes.show', ['codigo' => $codigo]);
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
      View::redirect('clientes.edit', ['codigo' => $codigo]);
    }
  }

  public function delete(string $codigo): void
  {
    Auth::requireCsrf();

    try {
      $this->service->delete($codigo);
      View::setFlash('success', 'Cliente excluído.');
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
    }

    View::redirect('clientes.index');
  }
}
