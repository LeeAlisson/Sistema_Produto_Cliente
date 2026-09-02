<?php

namespace App\Validators;

use App\Models\Cliente;

class ClienteValidator
{
  public function validate(array $data, bool $isCreate): array
  {
    $errors = [];

    if ($isCreate && empty($data['c00_codigo'])) {
      $errors[] = 'Código é obrigatório.';
    }

    if (empty($data['c00_nome'])) {
      $errors[] = 'Nome é obrigatório.';
    }

    if (!in_array($data['c00_pessoa'], ['J', 'F', 'O'], true)) {
      $errors[] = 'Tipo de pessoa inválido.';
    }

    if (empty($data['c00_estado']) || !Cliente::isEstadoValido($data['c00_estado'])) {
      $errors[] = 'Selecione um estado (UF) válido.';
    }

    if (empty($data['c00_data_nascimento']) || strlen($data['c00_data_nascimento']) !== 8) {
      $errors[] = 'Data de nascimento é obrigatória.';
    } elseif (!Cliente::isDataNascimentoValida($data['c00_data_nascimento'])) {
      $errors[] = 'Data de nascimento inválida ou não pode ser futura.';
    }

    if ($data['c00_pessoa'] === 'J' && empty($data['c00_cnpj'])) {
      $errors[] = 'CNPJ é obrigatório para pessoa jurídica.';
    }

    if ($data['c00_pessoa'] === 'F' && empty($data['c00_cnpj'])) {
      $errors[] = 'CPF é obrigatório para pessoa física.';
    }

    return $errors;
  }

  public function normalizeFromRequest(array $post): array
  {
    $documento = trim($post['c00_documento'] ?? '');
    $dataNascimento = trim($post['c00_data_nascimento'] ?? '');

    return [
      'c00_codigo' => trim($post['c00_codigo'] ?? ''),
      'c00_nome' => trim($post['c00_nome'] ?? ''),
      'c00_pessoa' => $post['c00_pessoa'] ?? '',
      'c00_cnpj' => preg_replace('/\D/', '', $documento),
      'c00_estado' => strtoupper(trim($post['c00_estado'] ?? '')),
      'c00_data_nascimento' => Cliente::dataParaBanco($dataNascimento),
    ];
  }
}
