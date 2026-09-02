<?php

namespace App;

class Config
{
  private static bool $loaded = false;
  private static array $values = [];

  public static function load(string $root): void
  {
    if (self::$loaded) {
      return;
    }

    $envFile = $root . DIRECTORY_SEPARATOR . '.env';
    if (is_readable($envFile)) {
      $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
      foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
          continue;
        }
        if (!str_contains($line, '=')) {
          continue;
        }
        [$key, $value] = explode('=', $line, 2);
        self::$values[trim($key)] = trim($value, " \t\"'");
      }
    }

    self::$loaded = true;
  }

  public static function get(string $key, mixed $default = null): mixed
  {
    return self::$values[$key] ?? $default;
  }

  public static function int(string $key, int $default): int
  {
    $value = self::get($key);
    return $value !== null && $value !== '' ? (int) $value : $default;
  }

  public static function bool(string $key, bool $default): bool
  {
    $value = self::get($key);
    if ($value === null) {
      return $default;
    }
    return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
  }

  public static function isDebug(): bool
  {
    return self::bool('APP_DEBUG', false);
  }

  public static function url(): string
  {
    return rtrim((string) self::get('APP_URL', ''), '/');
  }
}
