<?php

namespace App\Models;

use App\Database;
use PDO;

class Cliente
{
  public const TIPOS_PESSOA = [
    'J' => 'Jurídica',
    'F' => 'Física',
    'O' => 'Outros',
  ];

  public const ESTADOS = [
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AP' => 'Amapá',
    'AM' => 'Amazonas',
    'BA' => 'Bahia',
    'CE' => 'Ceará',
    'DF' => 'Distrito Federal',
    'ES' => 'Espírito Santo',
    'GO' => 'Goiás',
    'MA' => 'Maranhão',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Pará',
    'PB' => 'Paraíba',
    'PR' => 'Paraná',
    'PE' => 'Pernambuco',
    'PI' => 'Piauí',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul',
    'RO' => 'Rondônia',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SP' => 'São Paulo',
    'SE' => 'Sergipe',
    'TO' => 'Tocantins',
  ];

  public static function all(): array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->query('SELECT * FROM c00_cliente ORDER BY c00_nome');
    return $stmt->fetchAll();
  }

  public static function paginate(?string $search, int $page, int $perPage): array
  {
    $pdo = Database::getConnection();
    $where = '';
    $params = [];

    if ($search !== null && $search !== '') {
      $where = 'WHERE c00_codigo LIKE ? OR c00_nome LIKE ?';
      $term = '%' . $search . '%';
      $params = [$term, $term];
    }

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM c00_cliente' . ($where ? ' ' . $where : ''));
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $offset = ($page - 1) * $perPage;
    $sql = 'SELECT * FROM c00_cliente' . ($where ? ' ' . $where : '') . ' ORDER BY c00_nome LIMIT ? OFFSET ?';
    $stmt = $pdo->prepare($sql);

    $bindParams = array_merge($params, [$perPage, $offset]);
    foreach ($bindParams as $i => $value) {
      $stmt->bindValue($i + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();

    return [
      'items' => $stmt->fetchAll(),
      'total' => $total,
    ];
  }

  public static function find(string $codigo): ?array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT * FROM c00_cliente WHERE c00_codigo = ?');
    $stmt->execute([$codigo]);
    $result = $stmt->fetch();
    return $result ?: null;
  }

  public static function create(array $data): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'INSERT INTO c00_cliente (c00_codigo, c00_nome, c00_pessoa, c00_cnpj, c00_estado, c00_data_nascimento)
       VALUES (?, ?, ?, ?, ?, ?)'
    );
    return $stmt->execute([
      $data['c00_codigo'],
      $data['c00_nome'],
      $data['c00_pessoa'],
      $data['c00_cnpj'] ?: null,
      $data['c00_estado'],
      $data['c00_data_nascimento'],
    ]);
  }

  public static function update(string $codigo, array $data): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'UPDATE c00_cliente SET c00_nome = ?, c00_pessoa = ?, c00_cnpj = ?, c00_estado = ?, c00_data_nascimento = ?
       WHERE c00_codigo = ?'
    );
    return $stmt->execute([
      $data['c00_nome'],
      $data['c00_pessoa'],
      $data['c00_cnpj'] ?: null,
      $data['c00_estado'],
      $data['c00_data_nascimento'],
      $codigo,
    ]);
  }

  public static function delete(string $codigo): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('DELETE FROM c00_cliente WHERE c00_codigo = ?');
    return $stmt->execute([$codigo]);
  }

  public static function getProdutosAssociados(string $codigo): array
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'SELECT p.* FROM p00_produto p
       INNER JOIN r00_produto_cliente r ON r.r00_produto_codigo = p.p00_codigo
       WHERE r.r00_cliente_codigo = ?
       ORDER BY p.p00_descricao'
    );
    $stmt->execute([$codigo]);
    return $stmt->fetchAll();
  }

  public static function formatarDataExibicao(string $dataAaaammdd): string
  {
    if (strlen($dataAaaammdd) !== 8) {
      return $dataAaaammdd;
    }
    return substr($dataAaaammdd, 6, 2) . '/' . substr($dataAaaammdd, 4, 2) . '/' . substr($dataAaaammdd, 0, 4);
  }

  public static function dataParaBanco(string $data): string
  {
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data, $m)) {
      return $m[3] . $m[2] . $m[1];
    }
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data, $m)) {
      return $m[1] . $m[2] . $m[3];
    }
    return $data;
  }

  public static function getDocumentoLabel(string $tipoPessoa): string
  {
    return match ($tipoPessoa) {
      'F' => 'CPF',
      'J' => 'CNPJ',
      default => 'CNPJ / CPF',
    };
  }

  public static function isEstadoValido(string $uf): bool
  {
    return isset(self::ESTADOS[strtoupper($uf)]);
  }

  public static function isDataNascimentoValida(string $dataAaaammdd): bool
  {
    if (strlen($dataAaaammdd) !== 8 || !preg_match('/^\d{8}$/', $dataAaaammdd)) {
      return false;
    }

    $ano = (int) substr($dataAaaammdd, 0, 4);
    $mes = (int) substr($dataAaaammdd, 4, 2);
    $dia = (int) substr($dataAaaammdd, 6, 2);

    if (!checkdate($mes, $dia, $ano)) {
      return false;
    }

    return $dataAaaammdd <= date('Ymd');
  }

  public static function temAssociacoes(string $codigo): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
      'SELECT 1 FROM r00_produto_cliente WHERE r00_cliente_codigo = ? LIMIT 1'
    );
    $stmt->execute([$codigo]);
    return (bool) $stmt->fetchColumn();
  }
}
