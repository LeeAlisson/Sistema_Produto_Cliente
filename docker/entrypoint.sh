#!/bin/sh
set -e
cd /var/www/html

git config --global --add safe.directory /var/www/html 2>/dev/null || true

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

echo "Aguardando MySQL em ${DB_HOST:-db}:${DB_PORT:-3306}..."
i=0
until php -r '
require "/var/www/html/src/bootstrap.php";
$c = require "/var/www/html/config/database.php";
try {
    new PDO(
        sprintf("mysql:host=%s;port=%s;charset=%s", $c["host"], $c["port"], $c["charset"]),
        $c["username"],
        $c["password"]
    );
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
'; do
  i=$((i + 1))
  if [ "$i" -gt 40 ]; then
    echo "Timeout aguardando MySQL."
    exit 1
  fi
  sleep 2
done

# CREATE IF NOT EXISTS / índices. Não zera dados já gravados.
php scripts/setup.php
exec php -S 0.0.0.0:8080 -t public public/router.php
