<?php

namespace Tests\Unit;

use App\Support\Pagination;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
  public function testResolvePage(): void
  {
    $this->assertSame(1, Pagination::resolvePage(null));
    $this->assertSame(1, Pagination::resolvePage(0));
    $this->assertSame(3, Pagination::resolvePage(3));
  }

  public function testBuild(): void
  {
    $meta = Pagination::build(25, 2, 10);

    $this->assertSame(25, $meta['total']);
    $this->assertSame(2, $meta['page']);
    $this->assertSame(10, $meta['per_page']);
    $this->assertSame(3, $meta['pages']);
    $this->assertTrue($meta['has_prev']);
    $this->assertTrue($meta['has_next']);
  }
}
