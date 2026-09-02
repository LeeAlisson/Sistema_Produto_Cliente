<?php

namespace App\Validators;

use App\Models\Cliente;
use App\Support\Documento;

class ClienteValidator
{
  public function validate(array $data, bool $isCreate): array
  {
    $errors = [];
    $documento = Documento::onlyDigits((string) ($data['c00_cnpj'] ?? ''));

    if ($isCreate && empty($data['c00_codigo'])) {
      $errors[] = 'Código é obrigatório.';
    } elseif ($isCreate && strlen((string) $data['c00_codigo']) > 6) {
      $errors[] = 'Código deve ter no máximo 6 caracteres.';
    }

    $nome = trim((string) ($data['c00_nome'] ?? ''));
    if ($nome === '') {
      $errors[] = 'Nome é obrigatório.';
    } elseif (strlen($nome) > 60) {
      $errors[] = 'Nome deve ter no máximo 60 caracteres.';
    }

    if (!in_array($data['c00_pessoa'] ?? '', ['J', 'F', 'O'], true)) {
      $errors[] = 'Tipo de pessoa inválido.';
    }

    if (empty($data['c00_estado']) || !Cliente::isEstadoValido((string) $data['c00_estado'])) {
      $errors[] = 'Selecione um estado (UF) válido.';
    }

    if (empty($data['c00_data_nascimento']) || strlen((string) $data['c00_data_nascimento']) !== 8) {
      $errors[] = 'Data de nascimento é obrigatória.';
    } elseif (!Cliente::isDataNascimentoValida((string) $data['c00_data_nascimento'])) {
      $errors[] = 'Data de nascimento inválida ou não pode ser futura.';
    }

    $tipo = $data['c00_pessoa'] ?? '';

    // Enunciado: CPF e CNPJ usam a mesma coluna (c00_cnpj).
    if ($tipo === 'J') {
      if ($documento === '') {
        $errors[] = 'CNPJ é obrigatório para pessoa jurídica.';
      } elseif (!Documento::isValidCnpj($documento)) {
        $errors[] = 'CNPJ inválido.';
      }
    }

    if ($tipo === 'F') {
      if ($documento === '') {
        $errors[] = 'CPF é obrigatório para pessoa física.';
      } elseif (!Documento::isValidCpf($documento)) {
        $errors[] = 'CPF inválido.';
      }
    }

    if ($tipo === 'O' && $documento !== '' && !Documento::isValidCpfOrCnpj($documento)) {
      $errors[] = 'Documento inválido. Informe um CPF ou CNPJ válido, ou deixe em branco.';
    }

    return $errors;
  }

  public function normalizeFromRequest(array $post): array
  {
    $documento = Documento::onlyDigits(trim((string) ($post['c00_documento'] ?? $post['c00_cnpj'] ?? '')));
    $dataNascimento = trim((string) ($post['c00_data_nascimento'] ?? ''));

    return [
      'c00_codigo' => trim((string) ($post['c00_codigo'] ?? '')),
      'c00_nome' => trim((string) ($post['c00_nome'] ?? '')),
      'c00_pessoa' => $post['c00_pessoa'] ?? '',
      'c00_cnpj' => $documento, // persistido sem máscara
      'c00_estado' => strtoupper(trim((string) ($post['c00_estado'] ?? ''))),
      'c00_data_nascimento' => Cliente::dataParaBanco($dataNascimento),
    ];
  }
}
