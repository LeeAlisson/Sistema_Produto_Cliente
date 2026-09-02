<div class="page-header">
  <div>
    <h1><i class="bi bi-journal-text"></i> Auditoria</h1>
    <p class="page-subtitle">Registro de operações no sistema</p>
  </div>
</div>

<div class="card-modern">
  <?php if (empty($logs)): ?>
    <div class="empty-state">
      <i class="bi bi-journal-text"></i>
      <p>Nenhum registro de auditoria.</p>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Data</th>
            <th>Usuário</th>
            <th>Ação</th>
            <th>Entidade</th>
            <th>ID</th>
            <th>Detalhes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($logs as $log): ?>
            <tr>
              <td><small><?= App\View::escape($log['s00_created_at']) ?></small></td>
              <td><?= App\View::escape($log['s00_username']) ?></td>
              <td><span class="badge-modern indigo"><?= App\View::escape($log['s00_action']) ?></span></td>
              <td><?= App\View::escape($log['s00_entity']) ?></td>
              <td><?= App\View::escape($log['s00_entity_id'] ?? '—') ?></td>
              <td><small class="text-muted"><?= App\View::escape($log['s00_details'] ?? '—') ?></small></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
    $routeName = 'auditoria.index';
    require __DIR__ . '/../partials/pagination.php';
    ?>
  <?php endif; ?>
</div>
