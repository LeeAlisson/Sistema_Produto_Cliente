<div class="page-header">
  <div>
    <h1><i class="bi bi-box"></i> Detalhes do Produto</h1>
    <p class="page-subtitle"><?= App\View::escape($produto['p00_descricao']) ?></p>
  </div>
  <div class="page-header-actions">
    <a href="<?= App\View::url('produtos.edit', ['codigo' => $produto['p00_codigo']]) ?>" class="btn-accent">
      <i class="bi bi-pencil"></i> Editar
    </a>
    <a href="<?= App\View::url('produtos.index') ?>" class="btn-ghost">
      <i class="bi bi-arrow-left"></i> Voltar
    </a>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card-modern">
      <div class="card-modern-header">
        <i class="bi bi-info-circle"></i> Informações do Produto
      </div>
      <div class="card-modern-body">
        <dl class="detail-list">
          <dt>Código</dt>
          <dd><span class="code-tag"><?= App\View::escape($produto['p00_codigo']) ?></span></dd>
          <dt>Descrição</dt>
          <dd><?= App\View::escape($produto['p00_descricao']) ?></dd>
          <dt>Preço</dt>
          <dd>R$ <?= number_format((float) $produto['p00_preco'], 2, ',', '.') ?></dd>
          <dt>Imposto (%)</dt>
          <dd><?= number_format((float) $produto['p00_imposto'], 2, ',', '.') ?>%</dd>
          <dt>Valor do Imposto</dt>
          <dd><span class="detail-highlight">R$ <?= number_format($produto['valor_imposto'], 2, ',', '.') ?></span></dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card-modern">
      <div class="card-modern-header">
        <i class="bi bi-people"></i> Clientes Associados
        <span class="count-badge ms-2"><?= count($clientes) ?></span>
      </div>
      <?php if (empty($clientes)): ?>
        <div class="empty-state" style="padding:2rem;">
          <i class="bi bi-people"></i>
          <p>Nenhum cliente associado.</p>
        </div>
      <?php else: ?>
        <ul class="assoc-list">
          <?php foreach ($clientes as $c): ?>
            <li class="assoc-item">
              <div>
                <strong><?= App\View::escape($c['c00_nome']) ?></strong>
                <br><small><?= App\View::escape($c['c00_codigo']) ?> — <?= App\View::escape($c['c00_estado']) ?></small>
              </div>
              <a href="<?= App\View::url('clientes.show', ['codigo' => $c['c00_codigo']]) ?>" class="btn-icon">
                <i class="bi bi-eye"></i>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
