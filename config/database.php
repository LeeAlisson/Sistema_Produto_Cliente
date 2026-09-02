<?php

use App\Config;

return [
  'host' => Config::get('DB_HOST', 'localhost'),
  'port' => Config::get('DB_PORT', '3306'),
  'database' => Config::get('DB_DATABASE', 'produto_cliente'),
  'username' => Config::get('DB_USERNAME', 'root'),
  'password' => Config::get('DB_PASSWORD', ''),
  'charset' => Config::get('DB_CHARSET', 'utf8mb4'),
];
