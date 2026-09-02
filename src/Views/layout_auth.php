<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Produto x Cliente</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= App\View::asset('css/style.css') ?>" rel="stylesheet">
</head>
<body class="login-page">
  <div class="login-bg">
    <div class="login-bg-orb login-bg-orb-1"></div>
    <div class="login-bg-orb login-bg-orb-2"></div>
    <div class="login-bg-grid"></div>
  </div>

  <div class="login-container">
    <div class="login-card">
      <div class="login-card-brand">
        <div class="login-logo">
          <i class="bi bi-box-seam"></i>
        </div>
        <h1>Produto × Cliente</h1>
        <p>Cadastro de produtos e clientes, com vínculo N:N, imposto virtual e regras de CPF/CNPJ.</p>

        <div class="login-features">
          <div class="login-feature">
            <span class="login-feature-icon"><i class="bi bi-shield-check"></i></span>
            <div>
              <strong>Sessão segura</strong>
              <span>Autenticação server-side</span>
            </div>
          </div>
          <div class="login-feature">
            <span class="login-feature-icon"><i class="bi bi-box"></i></span>
            <div>
              <strong>Cadastro completo</strong>
              <span>Produtos e clientes</span>
            </div>
          </div>
          <div class="login-feature">
            <span class="login-feature-icon"><i class="bi bi-link-45deg"></i></span>
            <div>
              <strong>Associações</strong>
              <span>Vínculos produto x cliente</span>
            </div>
          </div>
        </div>
      </div>

      <div class="login-card-form">
        <?php require __DIR__ . '/' . $view . '.php'; ?>
      </div>
    </div>
  </div>
</body>
</html>
