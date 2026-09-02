<?php if (!empty($pagination) && $pagination['pages'] > 1): ?>
  <nav class="pagination-nav" aria-label="Paginação">
    <div class="pagination-info">
      <?= $pagination['total'] ?> registro(s) — página <?= $pagination['page'] ?> de <?= $pagination['pages'] ?>
    </div>
    <div class="pagination-links">
      <?php
      $query = $_GET;
      if ($pagination['has_prev']):
        $query['page'] = $pagination['page'] - 1;
        $prevUrl = App\View::url($routeName) . '?' . http_build_query($query);
      ?>
        <a href="<?= $prevUrl ?>" class="pagination-btn"><i class="bi bi-chevron-left"></i></a>
      <?php else: ?>
        <span class="pagination-btn disabled"><i class="bi bi-chevron-left"></i></span>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $pagination['pages']; $i++):
        $query['page'] = $i;
        $pageUrl = App\View::url($routeName) . '?' . http_build_query($query);
        $active = $i === $pagination['page'];
      ?>
        <a href="<?= $pageUrl ?>" class="pagination-btn <?= $active ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($pagination['has_next']):
        $query['page'] = $pagination['page'] + 1;
        $nextUrl = App\View::url($routeName) . '?' . http_build_query($query);
      ?>
        <a href="<?= $nextUrl ?>" class="pagination-btn"><i class="bi bi-chevron-right"></i></a>
      <?php else: ?>
        <span class="pagination-btn disabled"><i class="bi bi-chevron-right"></i></span>
      <?php endif; ?>
    </div>
  </nav>
<?php endif; ?>
