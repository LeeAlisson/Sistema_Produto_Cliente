<?php

namespace Tests\Unit;

use App\Models\Cliente;
use PHPUnit\Framework\TestCase;

class ClienteTest extends TestCase
{
  public function testIsEstadoValido(): void
  {
    $this->assertTrue(Cliente::isEstadoValido('SP'));
    $this->assertTrue(Cliente::isEstadoValido('sp'));
    $this->assertFalse(Cliente::isEstadoValido('XX'));
  }

  public function testIsDataNascimentoValida(): void
  {
    $this->assertTrue(Cliente::isDataNascimentoValida('19900315'));
    $this->assertFalse(Cliente::isDataNascimentoValida('20900315'));
    $this->assertFalse(Cliente::isDataNascimentoValida('19900230'));
    $this->assertFalse(Cliente::isDataNascimentoValida('invalid'));
  }

  public function testDataParaBanco(): void
  {
    $this->assertSame('19900315', Cliente::dataParaBanco('15/03/1990'));
    $this->assertSame('19900315', Cliente::dataParaBanco('1990-03-15'));
  }

  public function testFormatarDataExibicao(): void
  {
    $this->assertSame('15/03/1990', Cliente::formatarDataExibicao('19900315'));
  }
}
