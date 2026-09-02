<?php

namespace Tests\Unit;

use App\Validators\ClienteValidator;
use PHPUnit\Framework\TestCase;

class ClienteValidatorTest extends TestCase
{
  private ClienteValidator $validator;

  protected function setUp(): void
  {
    $this->validator = new ClienteValidator();
  }

  public function testPessoaFisicaExigeCpf(): void
  {
    $data = [
      'c00_codigo' => 'CLI999',
      'c00_nome' => 'Teste',
      'c00_pessoa' => 'F',
      'c00_cnpj' => '',
      'c00_estado' => 'SP',
      'c00_data_nascimento' => '19900101',
    ];

    $errors = $this->validator->validate($data, true);
    $this->assertNotEmpty($errors);
  }

  public function testPessoaJuridicaValida(): void
  {
    $data = [
      'c00_codigo' => 'CLI999',
      'c00_nome' => 'Empresa Teste',
      'c00_pessoa' => 'J',
      'c00_cnpj' => '12345678000199',
      'c00_estado' => 'SP',
      'c00_data_nascimento' => '20100101',
    ];

    $errors = $this->validator->validate($data, true);
    $this->assertEmpty($errors);
  }
}
