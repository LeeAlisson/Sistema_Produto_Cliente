<?php

namespace Tests\Integration;

use App\Database;
use App\Exceptions\BusinessException;
use App\Models\Associacao;
use App\Models\Cliente;
use App\Models\Produto;
use App\Services\AssociacaoService;
use App\Services\ClienteService;
use App\Services\ProdutoService;
use PHPUnit\Framework\TestCase;
use Throwable;

class AssociacaoEExclusaoTest extends TestCase
{
  private AssociacaoService $associacao;
  private ProdutoService $produtos;
  private ClienteService $clientes;

  private string $produtoCodigo = 'TSTP01';
  private string $clienteCodigo = 'TSTC01';

  protected function setUp(): void
  {
    try {
      Database::getConnection();
    } catch (Throwable $e) {
      $this->markTestSkipped('MySQL indisponível: ' . $e->getMessage());
    }

    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $this->associacao = new AssociacaoService();
    $this->produtos = new ProdutoService();
    $this->clientes = new ClienteService();
    $this->cleanup();
  }

  protected function tearDown(): void
  {
    try {
      $this->cleanup();
    } catch (Throwable) {
    }
  }

  public function testAssociacaoNnComTransacaoEDuplicata(): void
  {
    $this->criarProdutoECliente();

    $this->associacao->associar($this->produtoCodigo, $this->clienteCodigo);
    $this->assertTrue(Associacao::exists($this->produtoCodigo, $this->clienteCodigo));

    $this->expectException(BusinessException::class);
    $this->expectExceptionMessage('já existe');
    $this->associacao->associar($this->produtoCodigo, $this->clienteCodigo);
  }

  public function testExclusaoBloqueadaQuandoHaVinculo(): void
  {
    $this->criarProdutoECliente();
    $this->associacao->associar($this->produtoCodigo, $this->clienteCodigo);

    try {
      $this->produtos->delete($this->produtoCodigo);
      $this->fail('Produto com vínculo deveria bloquear exclusão.');
    } catch (BusinessException $e) {
      $this->assertStringContainsString('associações', $e->getMessage());
    }

    try {
      $this->clientes->delete($this->clienteCodigo);
      $this->fail('Cliente com vínculo deveria bloquear exclusão.');
    } catch (BusinessException $e) {
      $this->assertStringContainsString('associações', $e->getMessage());
    }

    $this->assertNotNull(Produto::find($this->produtoCodigo));
    $this->assertNotNull(Cliente::find($this->clienteCodigo));
  }

  private function criarProdutoECliente(): void
  {
    Produto::create([
      'p00_codigo' => $this->produtoCodigo,
      'p00_descricao' => 'Produto teste N:N',
      'p00_preco' => 100.00,
      'p00_imposto' => 10.00,
    ]);

    Cliente::create([
      'c00_codigo' => $this->clienteCodigo,
      'c00_nome' => 'Cliente teste N:N',
      'c00_pessoa' => 'F',
      'c00_cnpj' => '39053344705',
      'c00_estado' => 'SP',
      'c00_data_nascimento' => '19900101',
    ]);
  }

  private function cleanup(): void
  {
    Associacao::desassociar($this->produtoCodigo, $this->clienteCodigo);
    Produto::delete($this->produtoCodigo);
    Cliente::delete($this->clienteCodigo);
  }
}
