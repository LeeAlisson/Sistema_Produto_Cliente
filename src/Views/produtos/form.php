<div class="page-header">
  <div>
    <h1><i class="bi bi-box"></i> <?= App\View::escape($titulo) ?></h1>
  </div>
  <div class="page-header-actions">
    <a href="<?= App\View::url('produtos.index') ?>" class="btn-ghost">
      <i class="bi bi-arrow-left"></i> Voltar
    </a>
  </div>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert-modern error">
    <i class="bi bi-x-circle"></i>
    <ul class="mb-0 ps-3">
      <?php foreach ($errors as $error): ?>
        <li><?= App\View::escape($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card-modern">
  <div class="card-modern-body">
    <form method="POST" action="<?= $action === 'create' ? App\View::url('produtos.store') : App\View::url('produtos.update', ['codigo' => $produto['p00_codigo']]) ?>"
          class="form-modern"
          data-confirm
          data-confirm-title="<?= $action === 'create' ? 'Cadastrar produto' : 'Salvar alterações' ?>"
          data-confirm-message="<?= $action === 'create' ? 'Confirma o cadastro deste produto?' : 'Confirma as alterações neste produto?' ?>"
          data-confirm-label="Salvar">
      <?= App\View::csrfField() ?>

      <div class="row g-3">
        <div class="col-md-4">
          <label for="p00_codigo" class="form-label">Código <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="p00_codigo" name="p00_codigo"
                 maxlength="15" required
                 value="<?= App\View::escape($produto['p00_codigo'] ?? '') ?>"
                 <?= $action === 'edit' ? 'readonly' : '' ?>>
        </div>
        <div class="col-md-8">
          <label for="p00_descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="p00_descricao" name="p00_descricao"
                 maxlength="45" required
                 value="<?= App\View::escape($produto['p00_descricao'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label for="p00_preco" class="form-label">Preço (R$) <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="p00_preco" name="p00_preco"
                 step="0.01" min="0" required
                 value="<?= App\View::escape($produto['p00_preco'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label for="p00_imposto" class="form-label">Imposto (%) <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="p00_imposto" name="p00_imposto"
                 step="0.01" min="0" required
                 value="<?= App\View::escape($produto['p00_imposto'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Valor do Imposto (calculado)</label>
          <div class="form-preview" id="valor_imposto_preview">R$ 0,00</div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn-accent">
          <i class="bi bi-check-lg"></i> Salvar
        </button>
        <a href="<?= App\View::url('produtos.index') ?>" class="btn-ghost">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const preco = document.getElementById('p00_preco');
    const imposto = document.getElementById('p00_imposto');
    const preview = document.getElementById('valor_imposto_preview');
    if (!preco || !imposto || !preview) return;

    function calcular() {
      const p = parseFloat(preco.value) || 0;
      const i = parseFloat(imposto.value) || 0;
      const valor = p * (i / 100);
      preview.textContent = 'R$ ' + valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    preco.addEventListener('input', calcular);
    imposto.addEventListener('input', calcular);
    calcular();
  });
</script>
