<?php

namespace App\Validators;

class ProdutoValidator
{
  public function validate(array $data, bool $isCreate): array
  {
    $errors = [];

    if ($isCreate && empty(trim($data['p00_codigo'] ?? ''))) {
      $errors[] = 'Código é obrigatório.';
    }

    if (empty(trim($data['p00_descricao'] ?? ''))) {
      $errors[] = 'Descrição é obrigatória.';
    }

    if (!isset($data['p00_preco']) || $data['p00_preco'] === '') {
      $errors[] = 'Preço é obrigatório.';
    } elseif ((float) $data['p00_preco'] < 0) {
      $errors[] = 'Preço deve ser positivo.';
    }

    if (!isset($data['p00_imposto']) || $data['p00_imposto'] === '') {
      $errors[] = 'Imposto (%) é obrigatório.';
    } elseif ((float) $data['p00_imposto'] < 0) {
      $errors[] = 'Imposto deve ser positivo.';
    }

    return $errors;
  }

  public function normalize(array $data): array
  {
    return [
      'p00_codigo' => trim($data['p00_codigo'] ?? ''),
      'p00_descricao' => trim($data['p00_descricao'] ?? ''),
      'p00_preco' => (float) ($data['p00_preco'] ?? 0),
      'p00_imposto' => (float) ($data['p00_imposto'] ?? 0),
    ];
  }
}
