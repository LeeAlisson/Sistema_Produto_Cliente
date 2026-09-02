<?php

use App\Config;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

Config::load($root);

if (Config::isDebug()) {
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
} else {
  error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
  ini_set('display_errors', '0');
}

date_default_timezone_set('America/Sao_Paulo');
