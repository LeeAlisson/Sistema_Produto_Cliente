<div class="login-form-header">
  <h2>Entrar</h2>
  <p>Demonstração do teste técnico — produtos, clientes e associação N:N.</p>
</div>

<?php if (!empty($error)): ?>
  <div class="login-alert error">
    <i class="bi bi-exclamation-circle"></i>
    <?= App\View::escape($error) ?>
  </div>
<?php endif; ?>

<form method="POST" action="<?= App\View::url('login') ?>" autocomplete="off" class="login-form">
  <?= App\View::csrfField() ?>

  <div class="login-field">
    <label for="username">Usuário</label>
    <div class="login-input-wrap">
      <i class="bi bi-person"></i>
      <input type="text" id="username" name="username"
             required autofocus placeholder="Digite seu usuário"
             value="<?= App\View::escape($username ?? '') ?>">
    </div>
  </div>

  <div class="login-field">
    <label for="password">Senha</label>
    <div class="login-input-wrap">
      <i class="bi bi-lock"></i>
      <input type="password" id="password" name="password"
             required placeholder="Digite sua senha">
      <button type="button" class="login-toggle-pw" id="togglePassword" aria-label="Mostrar senha">
        <i class="bi bi-eye"></i>
      </button>
    </div>
  </div>

  <button type="submit" class="login-submit">
    <span>Entrar</span>
    <i class="bi bi-arrow-right"></i>
  </button>

  <p class="login-demo">
    Acesso de avaliação: <strong>admin</strong> / <strong>admin123</strong>
  </p>
</form>

<script>
  document.getElementById('togglePassword')?.addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'bi bi-eye-slash';
    } else {
      input.type = 'password';
      icon.className = 'bi bi-eye';
    }
  });
</script>
