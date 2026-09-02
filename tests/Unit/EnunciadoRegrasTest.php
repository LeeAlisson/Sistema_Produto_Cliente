<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\Produto;
use App\Validators\ClienteValidator;
use PHPUnit\Framework\TestCase;

class EnunciadoRegrasTest extends TestCase
{
  public function testValorDoImpostoEhPrecoVezesPercentual(): void
  {
    $this->assertSame(810.0, Produto::calcularValorImposto(4500.0, 18.0));
    $this->assertSame(87.0, Produto::calcularValorImposto(580.0, 15.0));
  }

  public function testDatePickerParaFormatoAaaammdd(): void
  {
    $this->assertSame('19850315', Cliente::dataParaBanco('15/03/1985'));
    $this->assertSame('15/03/1985', Cliente::formatarDataExibicao('19850315'));
    $this->assertTrue(Cliente::isDataNascimentoValida('19850315'));
    $this->assertFalse(Cliente::isDataNascimentoValida('20900101'));
  }

  public function testRegrasDeDocumentoPorTipoDePessoa(): void
  {
    $validator = new ClienteValidator();

    $pf = $validator->validate([
      'c00_codigo' => 'CLI100',
      'c00_nome' => 'PF',
      'c00_pessoa' => 'F',
      'c00_cnpj' => '52998224725',
      'c00_estado' => 'SP',
      'c00_data_nascimento' => '19900101',
    ], true);
    $this->assertEmpty($pf);

    $pj = $validator->validate([
      'c00_codigo' => 'CLI101',
      'c00_nome' => 'PJ',
      'c00_pessoa' => 'J',
      'c00_cnpj' => '11222333000181',
      'c00_estado' => 'RJ',
      'c00_data_nascimento' => '20100101',
    ], true);
    $this->assertEmpty($pj);

    $outros = $validator->validate([
      'c00_codigo' => 'CLI102',
      'c00_nome' => 'Outros',
      'c00_pessoa' => 'O',
      'c00_cnpj' => '',
      'c00_estado' => 'MG',
      'c00_data_nascimento' => '19920722',
    ], true);
    $this->assertEmpty($outros);
  }
}
