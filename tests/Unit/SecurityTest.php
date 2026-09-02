<?php

namespace Tests\Unit;

use App\Security;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
  public function testValidateCodigos(): void
  {
    $this->assertTrue(Security::validateProdutoCodigo('PROD001'));
    $this->assertFalse(Security::validateProdutoCodigo('PROD-001'));
    $this->assertTrue(Security::validateClienteCodigo('CLI001'));
    $this->assertFalse(Security::validateClienteCodigo('CLI0011'));
  }

  public function testSanitizeCodigo(): void
  {
    $this->assertSame('PROD001', Security::sanitizeCodigo('PROD-001', 15));
    $this->assertSame('CLI001', Security::sanitizeCodigo('CLI@001', 6));
  }
}
