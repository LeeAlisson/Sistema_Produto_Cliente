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
    $errors = $this->validator->validate($this->base([
      'c00_pessoa' => 'F',
      'c00_cnpj' => '',
    ]), true);

    $this->assertNotEmpty($errors);
    $this->assertStringContainsString('CPF', implode(' ', $errors));
  }

  public function testPessoaFisicaRejeitaCpfInvalido(): void
  {
    $errors = $this->validator->validate($this->base([
      'c00_pessoa' => 'F',
      'c00_cnpj' => '11111111111',
    ]), true);

    $this->assertNotEmpty($errors);
    $this->assertStringContainsString('CPF inválido', implode(' ', $errors));
  }

  public function testPessoaFisicaComCpfValido(): void
  {
    $errors = $this->validator->validate($this->base([
      'c00_pessoa' => 'F',
      'c00_cnpj' => '52998224725',
    ]), true);

    $this->assertEmpty($errors);
  }

  public function testPessoaJuridicaExigeCnpj(): void
  {
    $errors = $this->validator->validate($this->base([
      'c00_pessoa' => 'J',
      'c00_nome' => 'Empresa Teste',
      'c00_cnpj' => '',
      'c00_data_nascimento' => '20100101',
    ]), true);

    $this->assertNotEmpty($errors);
    $this->assertStringContainsString('CNPJ', implode(' ', $errors));
  }

  public function testPessoaJuridicaComCnpjValido(): void
  {
    $errors = $this->validator->validate($this->base([
      'c00_codigo' => 'CLI999',
      'c00_nome' => 'Empresa Teste',
      'c00_pessoa' => 'J',
      'c00_cnpj' => '11222333000181',
      'c00_data_nascimento' => '20100101',
    ]), true);

    $this->assertEmpty($errors);
  }

  public function testOutrosSemDocumentoEhValido(): void
  {
    $errors = $this->validator->validate($this->base([
      'c00_pessoa' => 'O',
      'c00_cnpj' => '',
    ]), true);

    $this->assertEmpty($errors);
  }

  public function testOutrosComDocumentoInvalido(): void
  {
    $errors = $this->validator->validate($this->base([
      'c00_pessoa' => 'O',
      'c00_cnpj' => '123',
    ]), true);

    $this->assertNotEmpty($errors);
  }

  public function testNormalizeGravaApenasDigitosNoCnpj(): void
  {
    $data = $this->validator->normalizeFromRequest([
      'c00_codigo' => 'CLI999',
      'c00_nome' => 'João',
      'c00_pessoa' => 'F',
      'c00_documento' => '529.982.247-25',
      'c00_estado' => 'sp',
      'c00_data_nascimento' => '15/03/1990',
    ]);

    $this->assertSame('52998224725', $data['c00_cnpj']);
    $this->assertSame('19900315', $data['c00_data_nascimento']);
    $this->assertSame('SP', $data['c00_estado']);
  }

  private function base(array $overrides): array
  {
    return array_merge([
      'c00_codigo' => 'CLI999',
      'c00_nome' => 'Teste',
      'c00_pessoa' => 'F',
      'c00_cnpj' => '52998224725',
      'c00_estado' => 'SP',
      'c00_data_nascimento' => '19900101',
    ], $overrides);
  }
}
