<div class="page-header">
  <div>
    <h1><i class="bi bi-link-45deg"></i> Associações</h1>
    <p class="page-subtitle">Relacionamento entre produtos e clientes</p>
  </div>
  <div class="page-header-actions">
  <?php if (!empty($produtos) && !empty($clientes)): ?>
    <button type="button" class="btn-accent" data-form-modal-open="associacaoModal">
      <i class="bi bi-plus-lg"></i> Nova Associação
    </button>
  <?php else: ?>
    <span class="text-muted" style="font-size:0.85rem;">
      Cadastre produtos e clientes para associar
    </span>
  <?php endif; ?>
  </div>
</div>

<div class="card-modern associacao-vinculos">
  <?php if (empty($associacoes)): ?>
    <div class="empty-state">
      <i class="bi bi-link-45deg"></i>
      <p>Nenhuma associação cadastrada. Clique em "Nova Associação" para vincular um produto a um cliente.</p>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table-modern table-associacao">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Cliente</th>
            <th class="text-end">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($associacoes as $a): ?>
            <tr>
              <td>
                <div class="vinculo-cell">
                  <span class="vinculo-icon produto"><i class="bi bi-box"></i></span>
                  <div>
                    <strong><?= App\View::escape($a['p00_descricao']) ?></strong>
                    <span class="code-tag"><?= App\View::escape($a['r00_produto_codigo']) ?></span>
                  </div>
                </div>
              </td>
              <td>
                <div class="vinculo-cell">
                  <span class="vinculo-icon cliente"><i class="bi bi-person"></i></span>
                  <div>
                    <strong><?= App\View::escape($a['c00_nome']) ?></strong>
                    <span class="code-tag"><?= App\View::escape($a['r00_cliente_codigo']) ?></span>
                  </div>
                </div>
              </td>
              <td class="text-end">
                <div class="action-group">
                  <a href="<?= App\View::url('produtos.show', ['codigo' => $a['r00_produto_codigo']]) ?>" class="btn-action" title="Ver produto">
                    <i class="bi bi-box"></i>
                  </a>
                  <a href="<?= App\View::url('clientes.show', ['codigo' => $a['r00_cliente_codigo']]) ?>" class="btn-action" title="Ver cliente">
                    <i class="bi bi-person"></i>
                  </a>
                  <form method="POST" action="<?= App\View::url('associacao.delete') ?>"
                        data-confirm
                        data-confirm-title="Remover associação"
                        data-confirm-message="Confirma a remoção do vínculo entre «<?= App\View::escape($a['p00_descricao']) ?>» e «<?= App\View::escape($a['c00_nome']) ?>»?"
                        data-confirm-label="Remover"
                        data-confirm-variant="danger">
                    <?= App\View::csrfField() ?>
                    <input type="hidden" name="produto_codigo" value="<?= App\View::escape($a['r00_produto_codigo']) ?>">
                    <input type="hidden" name="cliente_codigo" value="<?= App\View::escape($a['r00_cliente_codigo']) ?>">
                    <button type="submit" class="btn-action-danger" title="Remover associação">
                      <i class="bi bi-trash3"></i>
                      <span>Remover</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php if (!empty($produtos) && !empty($clientes)): ?>
<div id="associacaoModal" class="app-modal" hidden aria-hidden="true">
  <div class="app-modal-backdrop" data-form-modal-close></div>
  <div class="app-modal-dialog form-modal" role="dialog" aria-modal="true" aria-labelledby="associacaoModalTitle">
    <div class="form-modal-header">
      <div class="form-modal-title-wrap">
        <span class="app-modal-icon primary form-modal-icon"><i class="bi bi-link-45deg"></i></span>
        <div>
          <h3 id="associacaoModalTitle" class="app-modal-title">Nova Associação</h3>
          <p class="form-modal-subtitle">Selecione o produto e o cliente para criar o vínculo</p>
        </div>
      </div>
      <button type="button" class="btn-icon form-modal-close" data-form-modal-close aria-label="Fechar">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <form method="POST" action="<?= App\View::url('associacao.store') ?>" class="form-modern"
          data-confirm
          data-confirm-title="Criar associação"
          data-confirm-message="Confirma a associação entre o produto e o cliente selecionados?"
          data-confirm-label="Associar">
      <?= App\View::csrfField() ?>
      <div class="mb-3">
        <label for="produto_codigo" class="form-label">Produto <span class="text-danger">*</span></label>
        <select class="form-select" id="produto_codigo" name="produto_codigo" required>
          <option value="">Selecione um produto...</option>
          <?php foreach ($produtos as $p): ?>
            <option value="<?= App\View::escape($p['p00_codigo']) ?>">
              <?= App\View::escape($p['p00_codigo']) ?> — <?= App\View::escape($p['p00_descricao']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-4">
        <label for="cliente_codigo" class="form-label">Cliente <span class="text-danger">*</span></label>
        <select class="form-select" id="cliente_codigo" name="cliente_codigo" required>
          <option value="">Selecione um cliente...</option>
          <?php foreach ($clientes as $c): ?>
            <option value="<?= App\View::escape($c['c00_codigo']) ?>">
              <?= App\View::escape($c['c00_codigo']) ?> — <?= App\View::escape($c['c00_nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-modal-actions">
        <button type="button" class="btn-ghost" data-form-modal-close>Cancelar</button>
        <button type="submit" class="btn-accent">
          <i class="bi bi-link"></i> Associar
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>
