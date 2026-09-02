<?php

namespace App\Controllers\Api;

use App\Exceptions\BusinessException;
use App\Services\ClienteService;
use App\Support\JsonResponse;

class ClienteApiController
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

    JsonResponse::success($result);
  }

  public function show(string $codigo): void
  {
    try {
      $cliente = $this->service->findOrFail($codigo);
      $produtos = $this->service->getProdutosAssociados($codigo);

      JsonResponse::success([
        'cliente' => $cliente,
        'produtos' => $produtos,
      ]);
    } catch (BusinessException $e) {
      JsonResponse::error(404, $e->getMessage());
    }
  }

  public function store(): void
  {
    try {
      $this->service->create($_POST);
      JsonResponse::success(['message' => 'Cliente criado.'], 201);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }

  public function update(string $codigo): void
  {
    try {
      $this->service->update($codigo, $_POST);
      JsonResponse::success(['message' => 'Cliente atualizado.']);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }

  public function delete(string $codigo): void
  {
    try {
      $this->service->delete($codigo);
      JsonResponse::success(['message' => 'Cliente excluído.']);
    } catch (BusinessException $e) {
      JsonResponse::error(422, $e->getMessage());
    }
  }
}
