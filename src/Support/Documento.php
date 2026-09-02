<?php

namespace App\Support;

/** CPF/CNPJ: máscara na tela, só dígitos no banco, dígito verificador da Receita. */
class Documento
{
  public static function onlyDigits(string $value): string
  {
    return preg_replace('/\D+/', '', $value) ?? '';
  }

  public static function isValidCpf(string $cpf): bool
  {
    $cpf = self::onlyDigits($cpf);

    // 11 dígitos iguais passam no módulo 11, mas a Receita rejeita.
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
      return false;
    }

    for ($position = 9; $position < 11; $position++) {
      $sum = 0;
      for ($i = 0; $i < $position; $i++) {
        $sum += (int) $cpf[$i] * (($position + 1) - $i);
      }
      $digit = ((10 * $sum) % 11) % 10;
      if ((int) $cpf[$position] !== $digit) {
        return false;
      }
    }

    return true;
  }

  public static function isValidCnpj(string $cnpj): bool
  {
    $cnpj = self::onlyDigits($cnpj);

    if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
      return false;
    }

    $weights = [
      [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
      [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
    ];

    for ($check = 0; $check < 2; $check++) {
      $sum = 0;
      $limit = 12 + $check;
      foreach ($weights[$check] as $i => $weight) {
        $sum += (int) $cnpj[$i] * $weight;
      }
      $digit = $sum % 11;
      $digit = $digit < 2 ? 0 : 11 - $digit;
      if ((int) $cnpj[$limit] !== $digit) {
        return false;
      }
    }

    return true;
  }

  public static function isValidCpfOrCnpj(string $documento): bool
  {
    $digits = self::onlyDigits($documento);

    return match (strlen($digits)) {
      11 => self::isValidCpf($digits),
      14 => self::isValidCnpj($digits),
      default => false,
    };
  }

  public static function format(string $documento, ?string $tipoPessoa = null): string
  {
    $digits = self::onlyDigits($documento);
    if ($digits === '') {
      return '';
    }

    $tipo = $tipoPessoa ?? (strlen($digits) === 14 ? 'J' : (strlen($digits) === 11 ? 'F' : 'O'));

    if ($tipo === 'J' && strlen($digits) === 14) {
      return sprintf(
        '%s.%s.%s/%s-%s',
        substr($digits, 0, 2),
        substr($digits, 2, 3),
        substr($digits, 5, 3),
        substr($digits, 8, 4),
        substr($digits, 12, 2)
      );
    }

    if ($tipo === 'F' && strlen($digits) === 11) {
      return sprintf(
        '%s.%s.%s-%s',
        substr($digits, 0, 3),
        substr($digits, 3, 3),
        substr($digits, 6, 3),
        substr($digits, 9, 2)
      );
    }

    if (strlen($digits) === 14) {
      return self::format($digits, 'J');
    }

    if (strlen($digits) === 11) {
      return self::format($digits, 'F');
    }

    return $digits;
  }
}
