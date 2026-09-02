<?php

namespace Tests\Unit;

use App\Models\Produto;
use PHPUnit\Framework\TestCase;

class ProdutoTest extends TestCase
{
  public function testCalcularValorImposto(): void
  {
    $this->assertSame(810.0, Produto::calcularValorImposto(4500.0, 18.0));
    $this->assertSame(42.0, Produto::calcularValorImposto(350.0, 12.0));
    $this->assertSame(0.0, Produto::calcularValorImposto(0.0, 18.0));
  }
}
