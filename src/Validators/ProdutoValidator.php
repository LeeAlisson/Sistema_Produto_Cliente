<?php

namespace App\Validators;

class ProdutoValidator
{
  public function validate(array $data, bool $isCreate): array
  {
    $errors = [];

    $codigo = trim((string) ($data['p00_codigo'] ?? ''));
    if ($isCreate && $codigo === '') {
      $errors[] = 'Código é obrigatório.';
    } elseif ($isCreate && strlen($codigo) > 15) {
      $errors[] = 'Código deve ter no máximo 15 caracteres.';
    }

    $descricao = trim((string) ($data['p00_descricao'] ?? ''));
    if ($descricao === '') {
      $errors[] = 'Descrição é obrigatória.';
    } elseif (strlen($descricao) > 45) {
      $errors[] = 'Descrição deve ter no máximo 45 caracteres.';
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
