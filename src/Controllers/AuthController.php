<?php

namespace App\Controllers;

use App\Auth;
use App\Security;
use App\Services\AuditLogService;
use App\View;

class AuthController
{
  public function showLogin(): void
  {
    if (Auth::check()) {
      View::redirect('home');
    }

    View::render('auth/login', [], 'layout_auth');
  }

  public function login(): void
  {
    Auth::requireCsrf();

    if (Security::isLoginLocked()) {
      View::render('auth/login', [
        'error' => 'Muitas tentativas. Aguarde ' . Security::loginLockoutMinutes() . ' minuto(s).',
      ], 'layout_auth');
      return;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
      View::render('auth/login', [
        'error' => 'Usuário e senha são obrigatórios.',
        'username' => $username,
      ], 'layout_auth');
      return;
    }

    if (strlen($username) > 50) {
      Security::recordFailedLogin();
      View::render('auth/login', [
        'error' => 'Usuário ou senha inválidos.',
      ], 'layout_auth');
      return;
    }

    $usuario = \App\Models\Usuario::authenticate($username, $password);

    if (!$usuario) {
      Security::recordFailedLogin();
      View::render('auth/login', [
        'error' => 'Usuário ou senha inválidos.',
        'username' => $username,
      ], 'layout_auth');
      return;
    }

    Auth::login((int) $usuario['s00_id'], $usuario['s00_username']);
    AuditLogService::log(AuditLogService::ACTION_LOGIN, 'usuario', (string) $usuario['s00_id']);
    View::setFlash('success', 'Login realizado.');
    View::redirect('home');
  }

  public function logout(): void
  {
    Auth::requireCsrf();
    $userId = Auth::userId();
    if ($userId) {
      AuditLogService::log(AuditLogService::ACTION_LOGOUT, 'usuario', (string) $userId);
    }
    Auth::logout();
    Auth::startSession();
    View::setFlash('success', 'Logout realizado.');
    View::redirect('login');
  }
}
