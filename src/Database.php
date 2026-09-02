<?php

namespace App;

use PDO;
use PDOException;

class Database
{
  private static ?PDO $connection = null;

  public static function getConnection(): PDO
  {
    if (self::$connection === null) {
      $config = require __DIR__ . '/../config/database.php';

      $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
      );

      try {
        self::$connection = new PDO($dsn, $config['username'], $config['password'], [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        self::$connection->exec("SET time_zone = '-03:00'"); // America/Sao_Paulo
      } catch (PDOException $e) {
        $message = Config::isDebug()
          ? 'Erro de conexão com o banco: ' . $e->getMessage()
          : 'Não foi possível conectar ao banco de dados.';

        throw new PDOException($message, (int) $e->getCode(), $e);
      }
    }

    return self::$connection;
  }

  public static function transaction(callable $callback): mixed
  {
    $pdo = self::getConnection();
    $pdo->beginTransaction();

    try {
      $result = $callback($pdo);
      $pdo->commit();
      return $result;
    } catch (\Throwable $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
      throw $e;
    }
  }
}
