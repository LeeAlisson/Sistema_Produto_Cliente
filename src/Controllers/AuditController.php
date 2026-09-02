<?php

namespace App\Controllers;

use App\Models\AuditLog;
use App\Support\Pagination;
use App\View;

class AuditController
{
  public function index(): void
  {
    $page = Pagination::resolvePage((int) ($_GET['page'] ?? 1));
    $perPage = Pagination::perPage();
    $result = AuditLog::paginate($page, $perPage);

    View::render('auditoria/index', [
      'logs' => $result['items'],
      'pagination' => Pagination::build($result['total'], $page, $perPage),
    ]);
  }
}
