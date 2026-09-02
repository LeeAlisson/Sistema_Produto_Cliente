<div class="page-header">
  <div>
    <h1><i class="bi bi-people"></i> <?= App\View::escape($titulo) ?></h1>
  </div>
  <div class="page-header-actions">
    <a href="<?= App\View::url('clientes.index') ?>" class="btn-ghost">
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

<?php
$tipoPessoa = $cliente['c00_pessoa'] ?? 'F';
$documentoBruto = $cliente['c00_documento'] ?? ($cliente['c00_cnpj'] ?? '');
$documento = $documentoBruto !== ''
  ? App\Support\Documento::format((string) $documentoBruto, $tipoPessoa)
  : '';
$estadoSelecionado = strtoupper($cliente['c00_estado'] ?? '');
$dataExibicao = '';
if (!empty($cliente['c00_data_nascimento'])) {
  if (strpos($cliente['c00_data_nascimento'], '/') !== false) {
    $dataExibicao = $cliente['c00_data_nascimento'];
  } else {
    $dataExibicao = App\Models\Cliente::formatarDataExibicao($cliente['c00_data_nascimento']);
  }
}
?>

<div class="card-modern">
  <div class="card-modern-body">
    <form method="POST" action="<?= $action === 'create' ? App\View::url('clientes.store') : App\View::url('clientes.update', ['codigo' => $cliente['c00_codigo']]) ?>"
          id="form-cliente" class="form-modern"
          data-confirm
          data-confirm-title="<?= $action === 'create' ? 'Cadastrar cliente' : 'Salvar alterações' ?>"
          data-confirm-message="<?= $action === 'create' ? 'Confirma o cadastro deste cliente?' : 'Confirma as alterações neste cliente?' ?>"
          data-confirm-label="Salvar">
      <?= App\View::csrfField() ?>

      <div class="row g-3">
        <div class="col-md-3">
          <label for="c00_codigo" class="form-label">Código <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="c00_codigo" name="c00_codigo"
                 maxlength="6" required
                 value="<?= App\View::escape($cliente['c00_codigo'] ?? '') ?>"
                 <?= $action === 'edit' ? 'readonly' : '' ?>>
        </div>
        <div class="col-md-9">
          <label for="c00_nome" class="form-label">Nome <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="c00_nome" name="c00_nome"
                 maxlength="60" required
                 value="<?= App\View::escape($cliente['c00_nome'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label for="c00_pessoa" class="form-label">Tipo de Pessoa <span class="text-danger">*</span></label>
          <select class="form-select" id="c00_pessoa" name="c00_pessoa" required>
            <?php foreach (App\Models\Cliente::TIPOS_PESSOA as $key => $label): ?>
              <option value="<?= $key ?>" <?= $tipoPessoa === $key ? 'selected' : '' ?>><?= App\View::escape($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4" id="campo-documento">
          <label for="c00_documento" class="form-label">
            <span id="label-documento"><?= App\View::escape(App\Models\Cliente::getDocumentoLabel($tipoPessoa)) ?></span>
            <span class="text-danger" id="doc-required">*</span>
          </label>
          <input type="text" class="form-control" id="c00_documento" name="c00_documento"
                 maxlength="18" inputmode="numeric" autocomplete="off"
                 data-documento-mask
                 value="<?= App\View::escape($documento) ?>">
        </div>
        <div class="col-md-3">
          <label for="c00_estado" class="form-label">UF <span class="text-danger">*</span></label>
          <select class="form-select" id="c00_estado" name="c00_estado" required>
            <option value="">Selecione...</option>
            <?php foreach (App\Models\Cliente::ESTADOS as $uf => $nome): ?>
              <option value="<?= $uf ?>" <?= $estadoSelecionado === $uf ? 'selected' : '' ?>>
                <?= App\View::escape($uf) ?> — <?= App\View::escape($nome) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label for="c00_data_nascimento" class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
          <input type="text" class="form-control datepicker" id="c00_data_nascimento" name="c00_data_nascimento"
                 placeholder="DD/MM/AAAA" required
                 value="<?= App\View::escape($dataExibicao) ?>">
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn-accent">
          <i class="bi bi-check-lg"></i> Salvar
        </button>
        <a href="<?= App\View::url('clientes.index') ?>" class="btn-ghost">Cancelar</a>
      </div>
    </form>
  </div>
</div>
