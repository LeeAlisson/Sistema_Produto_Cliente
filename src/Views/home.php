<?php
$s = $stats;

$tipoLabels = [];
$tipoValues = [];
foreach ($s['por_tipo_pessoa'] as $row) {
  $tipoLabels[] = App\Models\Cliente::TIPOS_PESSOA[$row['tipo']] ?? $row['tipo'];
  $tipoValues[] = (int) $row['total'];
}

$ufLabels = [];
$ufValues = [];
foreach ($s['por_estado'] as $row) {
  $ufLabels[] = $row['uf'];
  $ufValues[] = (int) $row['total'];
}

$prodAssocLabels = [];
$prodAssocValues = [];
foreach ($s['produtos_mais_associados'] as $row) {
  $prodAssocLabels[] = $row['p00_descricao'];
  $prodAssocValues[] = (int) $row['total'];
}

$cliAssocLabels = [];
$cliAssocValues = [];
foreach ($s['clientes_mais_associados'] as $row) {
  $cliAssocLabels[] = $row['c00_nome'];
  $cliAssocValues[] = (int) $row['total'];
}

$precoLabels = [];
$precoValues = [];
$impostoValues = [];
foreach ($s['produtos_por_preco'] as $row) {
  $precoLabels[] = $row['p00_descricao'];
  $precoValues[] = (float) $row['p00_preco'];
  $impostoValues[] = (float) $row['valor_imposto'];
}

$produtosComVinculo = $s['total_produtos'] - $s['produtos_sem_vinculo'];
$clientesComVinculo = $s['total_clientes'] - $s['clientes_sem_vinculo'];

$chartData = [
  'overview' => [
    'labels' => ['Produtos', 'Clientes', 'Associações'],
    'values' => [$s['total_produtos'], $s['total_clientes'], $s['total_associacoes']],
  ],
  'cobertura' => [
    'labels' => ['Com vínculo', 'Sem vínculo'],
    'produtos' => [$produtosComVinculo, $s['produtos_sem_vinculo']],
    'clientes' => [$clientesComVinculo, $s['clientes_sem_vinculo']],
  ],
  'tipoPessoa' => [
    'labels' => $tipoLabels,
    'values' => $tipoValues,
  ],
  'estados' => [
    'labels' => $ufLabels,
    'values' => $ufValues,
  ],
  'produtosAssociados' => [
    'labels' => $prodAssocLabels,
    'values' => $prodAssocValues,
  ],
  'clientesAssociados' => [
    'labels' => $cliAssocLabels,
    'values' => $cliAssocValues,
  ],
  'financeiro' => [
    'labels' => $precoLabels,
    'precos' => $precoValues,
    'impostos' => $impostoValues,
  ],
  'resumo' => [
    'total_preco' => $s['total_preco'],
    'total_imposto' => $s['total_imposto'],
    'media_vinculos' => $s['media_vinculos_produto'],
  ],
];
?>

<div class="dashboard-page">
<div class="dashboard-header">
  <div>
    <div class="hero-title">Dashboard</div>
    <p class="hero-subtitle">Visão geral e estatísticas do sistema</p>
  </div>
  <div class="dashboard-header-meta">
    <span class="meta-pill"><i class="bi bi-box"></i> <?= $s['total_produtos'] ?> produtos</span>
    <span class="meta-pill"><i class="bi bi-people"></i> <?= $s['total_clientes'] ?> clientes</span>
    <span class="meta-pill accent"><i class="bi bi-link-45deg"></i> <?= $s['total_associacoes'] ?> vínculos</span>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card indigo">
    <div class="stat-icon"><i class="bi bi-box"></i></div>
    <div class="stat-body">
      <span class="stat-value"><?= $s['total_produtos'] ?></span>
      <span class="stat-label">Produtos</span>
    </div>
    <a href="<?= App\View::url('produtos.index') ?>" class="stat-link">Ver todos <i class="bi bi-arrow-right"></i></a>
  </div>

  <div class="stat-card green">
    <div class="stat-icon"><i class="bi bi-people"></i></div>
    <div class="stat-body">
      <span class="stat-value"><?= $s['total_clientes'] ?></span>
      <span class="stat-label">Clientes</span>
    </div>
    <a href="<?= App\View::url('clientes.index') ?>" class="stat-link">Ver todos <i class="bi bi-arrow-right"></i></a>
  </div>

  <div class="stat-card amber">
    <div class="stat-icon"><i class="bi bi-link-45deg"></i></div>
    <div class="stat-body">
      <span class="stat-value"><?= $s['total_associacoes'] ?></span>
      <span class="stat-label">Associações</span>
    </div>
    <a href="<?= App\View::url('associacao.index') ?>" class="stat-link">Gerenciar <i class="bi bi-arrow-right"></i></a>
  </div>

  <div class="stat-card slate">
    <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
    <div class="stat-body">
      <span class="stat-value">R$ <?= number_format($s['total_preco'], 2, ',', '.') ?></span>
      <span class="stat-label">Valor total em produtos</span>
    </div>
    <span class="stat-hint">Imposto est.: R$ <?= number_format($s['total_imposto'], 2, ',', '.') ?></span>
  </div>
</div>

<div class="row g-4 dashboard-charts">
  <div class="col-lg-4">
    <div class="card-modern chart-card">
      <div class="card-modern-header"><i class="bi bi-pie-chart"></i> Visão geral</div>
      <div class="chart-wrap chart-wrap-doughnut">
        <canvas id="chartOverview"></canvas>
      </div>
      <div class="chart-legend-row" id="legendOverview"></div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-modern chart-card">
      <div class="card-modern-header"><i class="bi bi-diagram-3"></i> Cobertura de vínculos</div>
      <div class="chart-wrap">
        <canvas id="chartCobertura"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-modern chart-card">
      <div class="card-modern-header"><i class="bi bi-person-badge"></i> Clientes por tipo</div>
      <div class="chart-wrap chart-wrap-doughnut">
        <canvas id="chartTipoPessoa"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 dashboard-charts">
  <div class="col-lg-8">
    <div class="card-modern chart-card">
      <div class="card-modern-header"><i class="bi bi-bar-chart-line"></i> Valores por produto</div>
      <div class="chart-wrap chart-wrap-lg">
        <canvas id="chartFinanceiro"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-modern chart-card">
      <div class="card-modern-header"><i class="bi bi-geo-alt"></i> Clientes por UF</div>
      <div class="chart-wrap chart-wrap-lg">
        <canvas id="chartEstados"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 dashboard-charts">
  <div class="col-lg-6">
    <div class="card-modern chart-card">
      <div class="card-modern-header"><i class="bi bi-trophy"></i> Produtos mais associados</div>
      <?php if (empty($s['produtos_mais_associados'])): ?>
        <div class="chart-empty"><i class="bi bi-link-45deg"></i><p>Nenhuma associação ainda.</p></div>
      <?php else: ?>
        <div class="chart-wrap">
          <canvas id="chartProdutosAssoc"></canvas>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card-modern chart-card">
      <div class="card-modern-header"><i class="bi bi-star"></i> Clientes mais associados</div>
      <?php if (empty($s['clientes_mais_associados'])): ?>
        <div class="chart-empty"><i class="bi bi-link-45deg"></i><p>Nenhuma associação ainda.</p></div>
      <?php else: ?>
        <div class="chart-wrap">
          <canvas id="chartClientesAssoc"></canvas>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="row g-4 dashboard-kpi-section">
  <div class="col-md-4">
    <div class="card-modern kpi-card">
      <div class="kpi-icon warning"><i class="bi bi-box-seam"></i></div>
      <div>
        <span class="kpi-value <?= $s['produtos_sem_vinculo'] > 0 ? 'warning' : '' ?>"><?= $s['produtos_sem_vinculo'] ?></span>
        <span class="kpi-label">Produtos sem cliente</span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-modern kpi-card">
      <div class="kpi-icon warning"><i class="bi bi-person-x"></i></div>
      <div>
        <span class="kpi-value <?= $s['clientes_sem_vinculo'] > 0 ? 'warning' : '' ?>"><?= $s['clientes_sem_vinculo'] ?></span>
        <span class="kpi-label">Clientes sem produto</span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-modern kpi-card">
      <div class="kpi-icon indigo"><i class="bi bi-graph-up"></i></div>
      <div>
        <span class="kpi-value"><?= $s['media_vinculos_produto'] ?></span>
        <span class="kpi-label">Média de vínculos / produto</span>
      </div>
    </div>
  </div>
</div>

</div>

<script type="application/json" id="dashboardChartData"><?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
