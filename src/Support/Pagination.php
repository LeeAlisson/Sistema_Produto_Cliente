<?php

namespace App\Support;

class Pagination
{
  public static function resolvePage(?int $page): int
  {
    return max(1, $page ?? 1);
  }

  public static function perPage(): int
  {
    return max(1, \App\Config::int('PAGINATION_PER_PAGE', 10));
  }

  public static function build(int $total, int $page, int $perPage): array
  {
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $pages);

    return [
      'total' => $total,
      'page' => $page,
      'per_page' => $perPage,
      'pages' => $pages,
      'has_prev' => $page > 1,
      'has_next' => $page < $pages,
    ];
  }
}
