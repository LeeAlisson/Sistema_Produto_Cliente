<div class="page-header">
  <div>
    <h1><i class="bi bi-people"></i> Clientes</h1>
    <p class="page-subtitle">Cadastro e gestão de clientes</p>
  </div>
  <div class="page-header-actions">
    <a href="<?= App\View::url('clientes.export') ?><?= !empty($search) ? '?q=' . rawurlencode($search) : '' ?>" class="btn-ghost">
      <i class="bi bi-download"></i> Exportar CSV
    </a>
    <a href="<?= App\View::url('clientes.create') ?>" class="btn-accent">
      <i class="bi bi-plus-lg"></i> Novo cliente
    </a>
  </div>
</div>

<form method="GET" action="<?= App\View::url('clientes.index') ?>" class="search-bar">
  <div class="search-input-wrap">
    <i class="bi bi-search"></i>
    <input type="search" name="q" value="<?= App\View::escape($search ?? '') ?>" placeholder="Buscar por código ou nome..." class="search-input">
  </div>
  <button type="submit" class="btn-ghost">Buscar</button>
  <?php if (!empty($search)): ?>
    <a href="<?= App\View::url('clientes.index') ?>" class="btn-ghost">Limpar</a>
  <?php endif; ?>
</form>

<?php if (empty($clientes)): ?>
  <div class="card-modern">
    <div class="empty-state">
      <i class="bi bi-people"></i>
      <p><?= !empty($search) ? 'Nenhum cliente encontrado para esta busca.' : 'Nenhum cliente cadastrado.' ?></p>
    </div>
  </div>
<?php else: ?>
  <div class="card-modern">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Documento</th>
            <th>UF</th>
            <th>Nascimento</th>
            <th class="text-end">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clientes as $c): ?>
            <tr>
              <td><span class="code-tag"><?= App\View::escape($c['c00_codigo']) ?></span></td>
              <td><strong><?= App\View::escape($c['c00_nome']) ?></strong></td>
              <td>
                <span class="badge-modern <?= $c['c00_pessoa'] === 'J' ? 'indigo' : ($c['c00_pessoa'] === 'F' ? 'green' : 'gray') ?>">
                  <?= App\View::escape(App\Models\Cliente::TIPOS_PESSOA[$c['c00_pessoa']] ?? $c['c00_pessoa']) ?>
                </span>
              </td>
              <td><?= App\View::escape(App\Models\Cliente::formatarDocumento($c['c00_cnpj'] ?? '', $c['c00_pessoa'])) ?></td>
              <td><?= App\View::escape($c['c00_estado']) ?></td>
              <td><?= App\View::escape(App\Models\Cliente::formatarDataExibicao($c['c00_data_nascimento'])) ?></td>
              <td class="text-end">
                <a href="<?= App\View::url('clientes.show', ['codigo' => $c['c00_codigo']]) ?>" class="btn-icon" title="Visualizar">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="<?= App\View::url('clientes.edit', ['codigo' => $c['c00_codigo']]) ?>" class="btn-icon" title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="<?= App\View::url('clientes.delete', ['codigo' => $c['c00_codigo']]) ?>"
                      class="d-inline" data-confirm
                      data-confirm-blocked="<?= $c['tem_associacoes'] ? 'true' : 'false' ?>"
                      data-confirm-title="<?= $c['tem_associacoes'] ? 'Exclusão não permitida' : 'Excluir cliente' ?>"
                      data-confirm-message="<?= $c['tem_associacoes']
                        ? 'O cliente «' . App\View::escape($c['c00_nome']) . '» possui associações com produtos. Remova os vínculos em Associações antes de excluir.'
                        : 'Confirma a exclusão do cliente «' . App\View::escape($c['c00_nome']) . '»?' ?>"
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
    $routeName = 'clientes.index';
    require __DIR__ . '/../partials/pagination.php';
    ?>
  </div>
<?php endif; ?>
