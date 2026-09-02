<?php

namespace App\Models;

use App\Database;

class Dashboard
{
  public static function getStats(): array
  {
    $pdo = Database::getConnection();

    $totalProdutos = (int) $pdo->query('SELECT COUNT(*) FROM p00_produto')->fetchColumn();
    $totalClientes = (int) $pdo->query('SELECT COUNT(*) FROM c00_cliente')->fetchColumn();
    $totalAssociacoes = (int) $pdo->query('SELECT COUNT(*) FROM r00_produto_cliente')->fetchColumn();

    $produtosSemVinculo = (int) $pdo->query(
      'SELECT COUNT(*) FROM p00_produto p
       WHERE NOT EXISTS (
         SELECT 1 FROM r00_produto_cliente r WHERE r.r00_produto_codigo = p.p00_codigo
       )'
    )->fetchColumn();

    $clientesSemVinculo = (int) $pdo->query(
      'SELECT COUNT(*) FROM c00_cliente c
       WHERE NOT EXISTS (
         SELECT 1 FROM r00_produto_cliente r WHERE r.r00_cliente_codigo = c.c00_codigo
       )'
    )->fetchColumn();

    $financeiro = $pdo->query(
      'SELECT COALESCE(SUM(p00_preco), 0) AS total_preco,
              COALESCE(SUM(p00_preco * p00_imposto / 100), 0) AS total_imposto
       FROM p00_produto'
    )->fetch();

    $porTipoPessoa = $pdo->query(
      'SELECT c00_pessoa AS tipo, COUNT(*) AS total
       FROM c00_cliente GROUP BY c00_pessoa ORDER BY total DESC'
    )->fetchAll();

    $porEstado = $pdo->query(
      'SELECT c00_estado AS uf, COUNT(*) AS total
       FROM c00_cliente GROUP BY c00_estado ORDER BY total DESC LIMIT 5'
    )->fetchAll();

    $produtosMaisAssociados = $pdo->query(
      'SELECT p.p00_codigo, p.p00_descricao, COUNT(r.r00_cliente_codigo) AS total
       FROM p00_produto p
       INNER JOIN r00_produto_cliente r ON r.r00_produto_codigo = p.p00_codigo
       GROUP BY p.p00_codigo, p.p00_descricao
       ORDER BY total DESC, p.p00_descricao
       LIMIT 5'
    )->fetchAll();

    $clientesMaisAssociados = $pdo->query(
      'SELECT c.c00_codigo, c.c00_nome, COUNT(r.r00_produto_codigo) AS total
       FROM c00_cliente c
       INNER JOIN r00_produto_cliente r ON r.r00_cliente_codigo = c.c00_codigo
       GROUP BY c.c00_codigo, c.c00_nome
       ORDER BY total DESC, c.c00_nome
       LIMIT 5'
    )->fetchAll();

    $produtosPorPreco = $pdo->query(
      'SELECT p00_descricao, p00_preco,
              (p00_preco * p00_imposto / 100) AS valor_imposto
       FROM p00_produto
       ORDER BY p00_preco DESC
       LIMIT 6'
    )->fetchAll();

    $mediaVinculosProduto = $totalProdutos > 0
      ? round($totalAssociacoes / $totalProdutos, 1)
      : 0;

    return [
      'total_produtos' => $totalProdutos,
      'total_clientes' => $totalClientes,
      'total_associacoes' => $totalAssociacoes,
      'produtos_sem_vinculo' => $produtosSemVinculo,
      'clientes_sem_vinculo' => $clientesSemVinculo,
      'total_preco' => (float) $financeiro['total_preco'],
      'total_imposto' => (float) $financeiro['total_imposto'],
      'media_vinculos_produto' => $mediaVinculosProduto,
      'por_tipo_pessoa' => $porTipoPessoa,
      'por_estado' => $porEstado,
      'produtos_mais_associados' => $produtosMaisAssociados,
      'clientes_mais_associados' => $clientesMaisAssociados,
      'produtos_por_preco' => $produtosPorPreco,
    ];
  }
}
