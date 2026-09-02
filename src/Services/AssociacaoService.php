<?php

namespace App\Services;

use App\Database;
use App\Exceptions\BusinessException;
use App\Models\Associacao;
use App\Models\Cliente;
use App\Models\Produto;
use App\Security;
use PDOException;

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

    try {
      // Transação cobre o INSERT e o log. Duplicata na PK vira erro de negócio.
      Database::transaction(function () use ($produtoCodigo, $clienteCodigo): void {
        if (Associacao::exists($produtoCodigo, $clienteCodigo)) {
          throw new BusinessException('Esta associação já existe.');
        }

        Associacao::associar($produtoCodigo, $clienteCodigo);

        AuditLogService::log(AuditLogService::ACTION_ASSOCIATE, 'associacao', null, [
          'produto_codigo' => $produtoCodigo,
          'cliente_codigo' => $clienteCodigo,
        ]);
      });
    } catch (PDOException $e) {
      if ($this->isDuplicateKey($e)) {
        throw new BusinessException('Esta associação já existe.');
      }
      throw $e;
    }
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

    Database::transaction(function () use ($produtoCodigo, $clienteCodigo): void {
      Associacao::desassociar($produtoCodigo, $clienteCodigo);

      AuditLogService::log(AuditLogService::ACTION_DISASSOCIATE, 'associacao', null, [
        'produto_codigo' => $produtoCodigo,
        'cliente_codigo' => $clienteCodigo,
      ]);
    });
  }

  public function listProdutos(): array
  {
    return Produto::all();
  }

  public function listClientes(): array
  {
    return Cliente::all();
  }

  private function isDuplicateKey(PDOException $e): bool
  {
    return $e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate');
  }
}
