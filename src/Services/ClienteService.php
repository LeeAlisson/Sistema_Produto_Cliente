<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Cliente;
use App\Models\Produto;
use App\Security;
use App\Support\Pagination;
use App\Validators\ClienteValidator;

class ClienteService
{
  private ClienteValidator $validator;

  public function __construct()
  {
    $this->validator = new ClienteValidator();
  }

  public function listPaginated(?string $search, int $page): array
  {
    $perPage = Pagination::perPage();
    $page = Pagination::resolvePage($page);
    $result = Cliente::paginate($search, $page, $perPage);

    foreach ($result['items'] as &$cliente) {
      $cliente['tem_associacoes'] = Cliente::temAssociacoes($cliente['c00_codigo']);
    }
    unset($cliente);

    $result['pagination'] = Pagination::build($result['total'], $page, $perPage);

    return $result;
  }

  public function findOrFail(string $codigo): array
  {
    if (!Security::validateClienteCodigo($codigo)) {
      throw new BusinessException('Código inválido.');
    }

    $cliente = Cliente::find($codigo);
    if (!$cliente) {
      throw new BusinessException('Cliente não encontrado.');
    }

    return $cliente;
  }

  public function create(array $input): void
  {
    $data = $this->validator->normalizeFromRequest($input);
    $errors = $this->validator->validate($data, true);

    if ($errors) {
      throw new BusinessException(implode(' ', $errors));
    }

    if (Cliente::find($data['c00_codigo'])) {
      throw new BusinessException('Código já cadastrado.');
    }

    Cliente::create($data);
    AuditLogService::log(AuditLogService::ACTION_CREATE, 'cliente', $data['c00_codigo'], $data);
  }

  public function update(string $codigo, array $input): void
  {
    $this->findOrFail($codigo);

    $data = $this->validator->normalizeFromRequest(array_merge($input, ['c00_codigo' => $codigo]));
    $errors = $this->validator->validate($data, false);

    if ($errors) {
      throw new BusinessException(implode(' ', $errors));
    }

    Cliente::update($codigo, $data);
    AuditLogService::log(AuditLogService::ACTION_UPDATE, 'cliente', $codigo, $data);
  }

  public function delete(string $codigo): void
  {
    $this->findOrFail($codigo);

    // Mesma regra do produto: vínculo N:N impede exclusão.
    if (Cliente::temAssociacoes($codigo)) {
      throw new BusinessException(
        'Não é possível excluir: este cliente possui associações com produtos.'
      );
    }

    Cliente::delete($codigo);
    AuditLogService::log(AuditLogService::ACTION_DELETE, 'cliente', $codigo);
  }

  public function getProdutosAssociados(string $codigo): array
  {
    $this->findOrFail($codigo);
    $produtos = Cliente::getProdutosAssociados($codigo);

    foreach ($produtos as &$produto) {
      $produto['valor_imposto'] = Produto::calcularValorImposto(
        (float) $produto['p00_preco'],
        (float) $produto['p00_imposto']
      );
    }
    unset($produto);

    return $produtos;
  }

  public function validateForm(array $input, bool $isCreate): array
  {
    $data = $this->validator->normalizeFromRequest($input);
    return $this->validator->validate($data, $isCreate);
  }

  public function normalizeFromRequest(array $input): array
  {
    return $this->validator->normalizeFromRequest($input);
  }

  public function listAll(): array
  {
    return Cliente::all();
  }

  public function listForExport(?string $search): array
  {
    return Cliente::searchAll($search);
  }
}
