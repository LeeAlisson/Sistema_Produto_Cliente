<?php

namespace App\Controllers;

use App\Auth;
use App\Exceptions\BusinessException;
use App\Services\AssociacaoService;
use App\View;

class AssociacaoController
{
  private AssociacaoService $service;

  public function __construct()
  {
    $this->service = new AssociacaoService();
  }

  public function index(): void
  {
    View::render('associacao/index', [
      'associacoes' => $this->service->listAll(),
      'produtos' => $this->service->listProdutos(),
      'clientes' => $this->service->listClientes(),
    ]);
  }

  public function store(): void
  {
    Auth::requireCsrf();

    try {
      $this->service->associar($_POST['produto_codigo'] ?? '', $_POST['cliente_codigo'] ?? '');
      View::setFlash('success', 'Associação criada.');
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
    }

    View::redirect('associacao.index');
  }

  public function delete(): void
  {
    Auth::requireCsrf();

    try {
      $this->service->desassociar($_POST['produto_codigo'] ?? '', $_POST['cliente_codigo'] ?? '');
      View::setFlash('success', 'Associação removida.');
    } catch (BusinessException $e) {
      View::setFlash('error', $e->getMessage());
    }

    View::redirect('associacao.index');
  }
}
