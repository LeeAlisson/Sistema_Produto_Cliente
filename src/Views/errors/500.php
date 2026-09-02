<div class="error-page">
  <div class="error-page-icon tone-danger">
    <i class="bi bi-exclamation-octagon"></i>
  </div>
  <span class="error-code">500</span>
  <h1>Erro interno</h1>
  <p class="error-lead">Não foi possível processar sua solicitação no momento.</p>

  <?php if (!empty($debugMessage)): ?>
    <details class="error-debug">
      <summary>Detalhes técnicos</summary>
      <pre><?= App\View::escape($debugMessage) ?></pre>
    </details>
  <?php else: ?>
    <p class="error-hint">
      Tente novamente em instantes. Se o problema continuar, verifique a configuração do ambiente ou contate o administrador.
    </p>
  <?php endif; ?>

  <div class="error-actions">
    <a href="<?= App\View::url('home') ?>" class="btn-accent">
      <i class="bi bi-house"></i> Ir ao início
    </a>
  </div>
</div>
