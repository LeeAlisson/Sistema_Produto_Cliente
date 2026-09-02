<?php

namespace Tests\Unit;

use App\Support\Documento;
use PHPUnit\Framework\TestCase;

class DocumentoTest extends TestCase
{
  public function testCpfValidoComMascara(): void
  {
    $this->assertTrue(Documento::isValidCpf('529.982.247-25'));
    $this->assertTrue(Documento::isValidCpf('52998224725'));
  }

  public function testCpfInvalido(): void
  {
    $this->assertFalse(Documento::isValidCpf('111.111.111-11'));
    $this->assertFalse(Documento::isValidCpf('12345678901'));
    $this->assertFalse(Documento::isValidCpf('123'));
  }

  public function testCnpjValidoComMascara(): void
  {
    $this->assertTrue(Documento::isValidCnpj('11.222.333/0001-81'));
    $this->assertTrue(Documento::isValidCnpj('11222333000181'));
  }

  public function testCnpjInvalido(): void
  {
    $this->assertFalse(Documento::isValidCnpj('12.345.678/0001-99'));
    $this->assertFalse(Documento::isValidCnpj('00000000000000'));
  }

  public function testOnlyDigitsEFormatacao(): void
  {
    $this->assertSame('52998224725', Documento::onlyDigits('529.982.247-25'));
    $this->assertSame('529.982.247-25', Documento::format('52998224725', 'F'));
    $this->assertSame('11.222.333/0001-81', Documento::format('11222333000181', 'J'));
  }
}
