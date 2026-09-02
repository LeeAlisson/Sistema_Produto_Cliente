<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema Produto x Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <link href="<?= App\View::asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
  <?php
  $currentPath = App\Url::currentPath();
  $userInitial = strtoupper(substr(App\Auth::username() ?? 'U', 0, 1));
  ?>

  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu">
    <i class="bi bi-list"></i>
  </button>

  <div class="app-wrapper">
    <aside class="sidebar" id="sidebar">
      <a class="sidebar-brand" href="<?= App\View::url('home') ?>">
        <i class="bi bi-box-seam"></i>
        Produto x Cliente
      </a>

      <nav class="sidebar-nav">
        <a class="sidebar-link <?= $currentPath === '/' ? 'active' : '' ?>" href="<?= App\View::url('home') ?>">
          <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a class="sidebar-link <?= str_starts_with($currentPath, '/produtos') ? 'active' : '' ?>" href="<?= App\View::url('produtos.index') ?>">
          <i class="bi bi-box"></i> Produtos
        </a>
        <a class="sidebar-link <?= str_starts_with($currentPath, '/clientes') ? 'active' : '' ?>" href="<?= App\View::url('clientes.index') ?>">
          <i class="bi bi-people"></i> Clientes
        </a>
        <a class="sidebar-link <?= str_starts_with($currentPath, '/associacao') ? 'active' : '' ?>" href="<?= App\View::url('associacao.index') ?>">
          <i class="bi bi-link-45deg"></i> Associações
        </a>
        <a class="sidebar-link <?= str_starts_with($currentPath, '/auditoria') ? 'active' : '' ?>" href="<?= App\View::url('auditoria.index') ?>">
          <i class="bi bi-journal-text"></i> Auditoria
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="sidebar-user-avatar"><?= App\View::escape($userInitial) ?></div>
          <div>
            <div class="sidebar-user-name"><?= App\View::escape(App\Auth::username()) ?></div>
            <div class="sidebar-user-role">Administrador</div>
          </div>
        </div>
        <form method="POST" action="<?= App\View::url('logout') ?>">
          <?= App\View::csrfField() ?>
          <button type="submit" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Sair
          </button>
        </form>
      </div>
    </aside>

    <div class="main-content">
      <main class="page-content">
        <?php
        $flash = App\View::getFlash();
        if ($flash):
          $flashClass = match ($flash['type']) {
            'error' => 'error',
            'warning' => 'warning',
            default => 'success',
          };
        ?>
          <div class="alert-modern <?= $flashClass ?>">
            <i class="bi bi-<?= $flashClass === 'success' ? 'check-circle' : ($flashClass === 'warning' ? 'exclamation-triangle' : 'x-circle') ?>"></i>
            <?= App\View::escape($flash['message']) ?>
          </div>
        <?php endif; ?>

        <?php require __DIR__ . '/' . $view . '.php'; ?>
      </main>

      <footer class="page-footer">
        Sistema de Gerenciamento de Produtos e Clientes
      </footer>
    </div>
  </div>

  <?php require __DIR__ . '/partials/confirm_modal.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
  <script src="<?= App\View::asset('js/app.js') ?>"></script>
  <?php if (!empty($pageScripts)): ?>
    <?php foreach ($pageScripts as $scriptSrc): ?>
      <script src="<?= App\View::escape($scriptSrc) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
