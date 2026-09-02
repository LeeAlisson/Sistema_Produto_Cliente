<div class="page-header">
  <div>
    <h1><i class="bi bi-people"></i> Detalhes do Cliente</h1>
    <p class="page-subtitle"><?= App\View::escape($cliente['c00_nome']) ?></p>
  </div>
  <div class="page-header-actions">
    <a href="<?= App\View::url('clientes.edit', ['codigo' => $cliente['c00_codigo']]) ?>" class="btn-accent">
      <i class="bi bi-pencil"></i> Editar
    </a>
    <a href="<?= App\View::url('clientes.index') ?>" class="btn-ghost">
      <i class="bi bi-arrow-left"></i> Voltar
    </a>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card-modern">
      <div class="card-modern-header">
        <i class="bi bi-info-circle"></i> Informações do Cliente
      </div>
      <div class="card-modern-body">
        <dl class="detail-list">
          <dt>Código</dt>
          <dd><span class="code-tag"><?= App\View::escape($cliente['c00_codigo']) ?></span></dd>
          <dt>Nome</dt>
          <dd><?= App\View::escape($cliente['c00_nome']) ?></dd>
          <dt>Tipo de Pessoa</dt>
          <dd>
            <span class="badge-modern <?= $cliente['c00_pessoa'] === 'J' ? 'indigo' : ($cliente['c00_pessoa'] === 'F' ? 'green' : 'gray') ?>">
              <?= App\View::escape(App\Models\Cliente::TIPOS_PESSOA[$cliente['c00_pessoa']] ?? $cliente['c00_pessoa']) ?>
            </span>
          </dd>
          <?php if ($cliente['c00_pessoa'] === 'F'): ?>
            <dt>CPF</dt>
          <?php elseif ($cliente['c00_pessoa'] === 'J'): ?>
            <dt>CNPJ</dt>
          <?php else: ?>
            <dt>Documento</dt>
          <?php endif; ?>
          <dd><?= App\View::escape($cliente['c00_cnpj'] ?? '—') ?></dd>
          <dt>Estado</dt>
          <dd><?= App\View::escape($cliente['c00_estado']) ?></dd>
          <dt>Data de Nascimento</dt>
          <dd><?= App\View::escape(App\Models\Cliente::formatarDataExibicao($cliente['c00_data_nascimento'])) ?></dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card-modern">
      <div class="card-modern-header">
        <i class="bi bi-box"></i> Produtos Associados
        <span class="count-badge ms-2"><?= count($produtos) ?></span>
      </div>
      <?php if (empty($produtos)): ?>
        <div class="empty-state" style="padding:2rem;">
          <i class="bi bi-box"></i>
          <p>Nenhum produto associado.</p>
        </div>
      <?php else: ?>
        <ul class="assoc-list">
          <?php foreach ($produtos as $p): ?>
            <li class="assoc-item">
              <div>
                <strong><?= App\View::escape($p['p00_descricao']) ?></strong>
                <br>
                <small>
                  <?= App\View::escape($p['p00_codigo']) ?> —
                  R$ <?= number_format((float) $p['p00_preco'], 2, ',', '.') ?>
                </small>
              </div>
              <a href="<?= App\View::url('produtos.show', ['codigo' => $p['p00_codigo']]) ?>" class="btn-icon">
                <i class="bi bi-eye"></i>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
