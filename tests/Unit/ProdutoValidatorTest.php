<?php

namespace Tests\Unit;

use App\Validators\ProdutoValidator;
use PHPUnit\Framework\TestCase;

class ProdutoValidatorTest extends TestCase
{
  private ProdutoValidator $validator;

  protected function setUp(): void
  {
    $this->validator = new ProdutoValidator();
  }

  public function testValidacaoCreateSucesso(): void
  {
    $errors = $this->validator->validate([
      'p00_codigo' => 'PROD999',
      'p00_descricao' => 'Produto teste',
      'p00_preco' => '100',
      'p00_imposto' => '10',
    ], true);

    $this->assertEmpty($errors);
  }

  public function testValidacaoCreateFalha(): void
  {
    $errors = $this->validator->validate([
      'p00_descricao' => '',
      'p00_preco' => '-1',
      'p00_imposto' => '',
    ], true);

    $this->assertNotEmpty($errors);
  }
}
