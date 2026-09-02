<div class="error-page">
  <div class="error-page-icon tone-warning">
    <i class="bi bi-shield-exclamation"></i>
  </div>
  <span class="error-code">403</span>
  <h1>Acesso negado</h1>
  <p class="error-lead"><?= App\View::escape($message ?? 'Você não tem permissão para esta ação.') ?></p>
  <p class="error-hint">Se acredita que isso é um engano, faça login novamente e tente outra vez.</p>

  <div class="error-actions">
    <a href="<?= App\View::url('home') ?>" class="btn-accent">
      <i class="bi bi-house"></i> Ir ao início
    </a>
    <a href="<?= App\View::url('login') ?>" class="btn-ghost">
      <i class="bi bi-box-arrow-in-right"></i> Login
    </a>
  </div>
</div>
