<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Associacao;
use App\Models\Cliente;
use App\Models\Produto;
use App\Security;

class AssociacaoService
{
  public function listAll(): array
  {
    return Associacao::getAllAssociacoes();
  }

  public function associar(string $produtoCodigo, string $clienteCodigo): void
  {
    $produtoCodigo = Security::sanitizeCodigo($produtoCodigo, 15);
    $clienteCodigo = Security::sanitizeCodigo($clienteCodigo, 6);

    if (
      $produtoCodigo === '' || $clienteCodigo === ''
      || !Security::validateProdutoCodigo($produtoCodigo)
      || !Security::validateClienteCodigo($clienteCodigo)
    ) {
      throw new BusinessException('Produto e cliente inválidos.');
    }

    if (!Produto::find($produtoCodigo) || !Cliente::find($clienteCodigo)) {
      throw new BusinessException('Produto ou cliente não encontrado.');
    }

    if (Associacao::exists($produtoCodigo, $clienteCodigo)) {
      throw new BusinessException('Esta associação já existe.');
    }

    Associacao::associar($produtoCodigo, $clienteCodigo);

    AuditLogService::log(AuditLogService::ACTION_ASSOCIATE, 'associacao', null, [
      'produto_codigo' => $produtoCodigo,
      'cliente_codigo' => $clienteCodigo,
    ]);
  }

  public function desassociar(string $produtoCodigo, string $clienteCodigo): void
  {
    $produtoCodigo = Security::sanitizeCodigo($produtoCodigo, 15);
    $clienteCodigo = Security::sanitizeCodigo($clienteCodigo, 6);

    if (
      $produtoCodigo === '' || $clienteCodigo === ''
      || !Security::validateProdutoCodigo($produtoCodigo)
      || !Security::validateClienteCodigo($clienteCodigo)
    ) {
      throw new BusinessException('Associação inválida.');
    }

    Associacao::desassociar($produtoCodigo, $clienteCodigo);

    AuditLogService::log(AuditLogService::ACTION_DISASSOCIATE, 'associacao', null, [
      'produto_codigo' => $produtoCodigo,
      'cliente_codigo' => $clienteCodigo,
    ]);
  }

  public function listProdutos(): array
  {
    return Produto::all();
  }

  public function listClientes(): array
  {
    return Cliente::all();
  }
}
