<?php

namespace App\Controllers\Api;

use App\Exceptions\BusinessException;
use App\Services\AssociacaoService;
use App\Support\JsonResponse;

class AssociacaoApiController
{
  private AssociacaoService $service;

  public function __construct()
  {
    $this->service = new AssociacaoService();
  }

  public function index(): void
  {
    JsonResponse::success([
      'associacoes' => $this->service->listAll(),
    ]);
  }

  public function store(): void
  {
    try {
      $this->service->associar($_POST['produto_codigo'] ?? '', $_POST['cliente_codigo'] ?? '');
      JsonResponse::success(['message' => 'Associação criada.'], 201);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }

  public function delete(): void
  {
    try {
      $this->service->desassociar($_POST['produto_codigo'] ?? '', $_POST['cliente_codigo'] ?? '');
      JsonResponse::success(['message' => 'Associação removida.']);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }
}
