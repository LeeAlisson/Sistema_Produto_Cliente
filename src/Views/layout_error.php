<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= App\View::escape($errorTitle ?? 'Erro') ?> — Produto x Cliente</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= App\View::asset('css/style.css') ?>" rel="stylesheet">
</head>
<body class="error-layout">
  <div class="error-bg">
    <div class="login-bg-orb login-bg-orb-1"></div>
    <div class="login-bg-orb login-bg-orb-2"></div>
    <div class="login-bg-grid"></div>
  </div>

  <div class="error-container">
    <a href="<?= App\View::url('home') ?>" class="error-brand">
      <i class="bi bi-box-seam"></i>
      Produto x Cliente
    </a>

    <div class="error-card">
      <?php require __DIR__ . '/' . $view . '.php'; ?>
    </div>

    <p class="error-footer">Sistema de Gerenciamento de Produtos e Clientes</p>
  </div>
</body>
</html>
