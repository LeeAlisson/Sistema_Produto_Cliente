<div class="page-header">
  <div>
    <h1><i class="bi bi-box"></i> Produtos</h1>
    <p class="page-subtitle">Cadastro e gestão de produtos</p>
  </div>
  <div class="page-header-actions">
    <a href="<?= App\View::url('produtos.export') ?><?= !empty($search) ? '?q=' . rawurlencode($search) : '' ?>" class="btn-ghost">
      <i class="bi bi-download"></i> Exportar CSV
    </a>
    <a href="<?= App\View::url('produtos.create') ?>" class="btn-accent">
      <i class="bi bi-plus-lg"></i> Novo produto
    </a>
  </div>
</div>

<form method="GET" action="<?= App\View::url('produtos.index') ?>" class="search-bar">
  <div class="search-input-wrap">
    <i class="bi bi-search"></i>
    <input type="search" name="q" value="<?= App\View::escape($search ?? '') ?>" placeholder="Buscar por código ou descrição..." class="search-input">
  </div>
  <button type="submit" class="btn-ghost">Buscar</button>
  <?php if (!empty($search)): ?>
    <a href="<?= App\View::url('produtos.index') ?>" class="btn-ghost">Limpar</a>
  <?php endif; ?>
</form>

<?php if (empty($produtos)): ?>
  <div class="card-modern">
    <div class="empty-state">
      <i class="bi bi-box"></i>
      <p><?= !empty($search) ? 'Nenhum produto encontrado para esta busca.' : 'Nenhum produto cadastrado.' ?></p>
    </div>
  </div>
<?php else: ?>
  <div class="card-modern">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>Preço</th>
            <th>Imposto</th>
            <th>Valor do imposto</th>
            <th class="text-end">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($produtos as $p): ?>
            <tr>
              <td><span class="code-tag"><?= App\View::escape($p['p00_codigo']) ?></span></td>
              <td><strong><?= App\View::escape($p['p00_descricao']) ?></strong></td>
              <td>R$ <?= number_format((float) $p['p00_preco'], 2, ',', '.') ?></td>
              <td><?= number_format((float) $p['p00_imposto'], 2, ',', '.') ?>%</td>
              <td><span class="detail-highlight" style="font-size:0.9rem;">R$ <?= number_format($p['valor_imposto'], 2, ',', '.') ?></span></td>
              <td class="text-end">
                <a href="<?= App\View::url('produtos.show', ['codigo' => $p['p00_codigo']]) ?>" class="btn-icon" title="Visualizar">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="<?= App\View::url('produtos.edit', ['codigo' => $p['p00_codigo']]) ?>" class="btn-icon" title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="<?= App\View::url('produtos.delete', ['codigo' => $p['p00_codigo']]) ?>"
                      class="d-inline" data-confirm
                      data-confirm-blocked="<?= $p['tem_associacoes'] ? 'true' : 'false' ?>"
                      data-confirm-title="<?= $p['tem_associacoes'] ? 'Exclusão não permitida' : 'Excluir produto' ?>"
                      data-confirm-message="<?= $p['tem_associacoes']
                        ? 'O produto «' . App\View::escape($p['p00_descricao']) . '» possui associações com clientes. Remova os vínculos em Associações antes de excluir.'
                        : 'Confirma a exclusão do produto «' . App\View::escape($p['p00_descricao']) . '»?' ?>"
                      data-confirm-label="Excluir"
                      data-confirm-variant="danger">
                  <?= App\View::csrfField() ?>
                  <button type="submit" class="btn-icon danger" title="Excluir">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
    $routeName = 'produtos.index';
    require __DIR__ . '/../partials/pagination.php';
    ?>
  </div>
<?php endif; ?>
