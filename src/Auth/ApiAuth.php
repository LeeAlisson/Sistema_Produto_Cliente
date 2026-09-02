<?php

namespace App\Auth;

use App\Auth;
use App\Config;
use App\Support\JsonResponse;

class ApiAuth
{
  public static function require(): void
  {
    $apiKey = Config::get('API_KEY');
    if ($apiKey !== null && $apiKey !== '') {
      $header = $_SERVER['HTTP_X_API_KEY'] ?? '';
      if ($header !== '' && hash_equals($apiKey, $header)) {
        return;
      }
    }

    if (Auth::check()) {
      return;
    }

    JsonResponse::error(401, 'Autenticação necessária. Use sessão ou header X-API-Key.');
  }
}
