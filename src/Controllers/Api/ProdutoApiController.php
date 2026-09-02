<?php

namespace App\Controllers\Api;

use App\Exceptions\BusinessException;
use App\Services\ProdutoService;
use App\Support\JsonResponse;

class ProdutoApiController
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

    JsonResponse::success($result);
  }

  public function show(string $codigo): void
  {
    try {
      $produto = $this->service->findOrFail($codigo);
      $clientes = $this->service->getClientesAssociados($codigo);

      JsonResponse::success([
        'produto' => $produto,
        'clientes' => $clientes,
      ]);
    } catch (BusinessException $e) {
      JsonResponse::error(404, $e->getMessage());
    }
  }

  public function store(): void
  {
    try {
      $this->service->create($_POST);
      JsonResponse::success(['message' => 'Produto criado.'], 201);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }

  public function update(string $codigo): void
  {
    try {
      $this->service->update($codigo, $_POST);
      JsonResponse::success(['message' => 'Produto atualizado.']);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }

  public function delete(string $codigo): void
  {
    try {
      $this->service->delete($codigo);
      JsonResponse::success(['message' => 'Produto excluído.']);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }
}
