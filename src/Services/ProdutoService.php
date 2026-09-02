<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Produto;
use App\Security;
use App\Support\Pagination;
use App\Validators\ProdutoValidator;

class ProdutoService
{
  private ProdutoValidator $validator;

  public function __construct()
  {
    $this->validator = new ProdutoValidator();
  }

  public function listPaginated(?string $search, int $page): array
  {
    $perPage = Pagination::perPage();
    $page = Pagination::resolvePage($page);
    $result = Produto::paginate($search, $page, $perPage);

    foreach ($result['items'] as &$produto) {
      $produto['valor_imposto'] = Produto::calcularValorImposto(
        (float) $produto['p00_preco'],
        (float) $produto['p00_imposto']
      );
      $produto['tem_associacoes'] = Produto::temAssociacoes($produto['p00_codigo']);
    }
    unset($produto);

    $result['pagination'] = Pagination::build($result['total'], $page, $perPage);

    return $result;
  }

  public function findOrFail(string $codigo): array
  {
    if (!Security::validateProdutoCodigo($codigo)) {
      throw new BusinessException('Código inválido.');
    }

    $produto = Produto::find($codigo);
    if (!$produto) {
      throw new BusinessException('Produto não encontrado.');
    }

    $produto['valor_imposto'] = Produto::calcularValorImposto(
      (float) $produto['p00_preco'],
      (float) $produto['p00_imposto']
    );

    return $produto;
  }

  public function create(array $input): void
  {
    $data = $this->validator->normalize($input);
    $errors = $this->validator->validate($data, true);

    if ($errors) {
      throw new BusinessException(implode(' ', $errors));
    }

    if (Produto::find($data['p00_codigo'])) {
      throw new BusinessException('Código já cadastrado.');
    }

    Produto::create($data);
    AuditLogService::log(AuditLogService::ACTION_CREATE, 'produto', $data['p00_codigo'], $data);
  }

  public function update(string $codigo, array $input): void
  {
    $this->findOrFail($codigo);

    $data = $this->validator->normalize(array_merge($input, ['p00_codigo' => $codigo]));
    $errors = $this->validator->validate($data, false);

    if ($errors) {
      throw new BusinessException(implode(' ', $errors));
    }

    Produto::update($codigo, [
      'p00_descricao' => $data['p00_descricao'],
      'p00_preco' => $data['p00_preco'],
      'p00_imposto' => $data['p00_imposto'],
    ]);

    AuditLogService::log(AuditLogService::ACTION_UPDATE, 'produto', $codigo, $data);
  }

  public function delete(string $codigo): void
  {
    $this->findOrFail($codigo);

    // Não apaga produto que ainda tem cliente vinculado.
    if (Produto::temAssociacoes($codigo)) {
      throw new BusinessException(
        'Não é possível excluir: este produto possui associações com clientes.'
      );
    }

    Produto::delete($codigo);
    AuditLogService::log(AuditLogService::ACTION_DELETE, 'produto', $codigo);
  }

  public function getClientesAssociados(string $codigo): array
  {
    $this->findOrFail($codigo);
    return Produto::getClientesAssociados($codigo);
  }

  public function validateForm(array $input, bool $isCreate): array
  {
    $data = $this->validator->normalize($input);
    return $this->validator->validate($data, $isCreate);
  }

  public function normalizeForm(array $input): array
  {
    return $this->validator->normalize($input);
  }

  public function listForExport(?string $search): array
  {
    $items = Produto::searchAll($search);

    foreach ($items as &$produto) {
      $produto['valor_imposto'] = Produto::calcularValorImposto(
        (float) $produto['p00_preco'],
        (float) $produto['p00_imposto']
      );
    }
    unset($produto);

    return $items;
  }
}
