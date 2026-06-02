<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/catalog.php';

$databaseStatusMessage = '';
$pdo = connect_database($databaseStatusMessage);
$categories = fetch_categories($pdo);
$products = fetch_products($pdo);

$pageId = 'ponudba';
$pageTitle = 'SweetCraft | Ponudba';
$pageDescription = 'Preglejte ponudbo SweetCraft tort, kolačkov in sezonskih sladic.';
require __DIR__ . '/includes/head.php';
?>
<main class="page-main flex-grow-1">
  <section class="page-hero">
    <div class="container">
      <header class="center-copy text-center">
        <h1>Naša ponudba</h1>
        <p class="lead mb-0">Izberite med široko paleto ročno izdelanih sladic.</p>
      </header>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <?php if ($pdo === null): ?>
        <div class="alert alert-warning mb-4" role="status">
          <?= h($databaseStatusMessage) ?>
        </div>
      <?php endif; ?>

      <div
        class="d-flex flex-wrap justify-content-center gap-2 mb-5"
        role="group"
        aria-label="Filtriranje izdelkov"
      >
        <button class="btn filter-btn rounded-pill is-active" type="button" data-filter="vse" aria-pressed="true">Vse</button>
        <?php foreach ($categories as $category): ?>
          <button
            class="btn filter-btn rounded-pill"
            type="button"
            data-filter="<?= h($category['slug']) ?>"
            aria-pressed="false"
          >
            <?= h($category['name']) ?>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-xl-3" data-product-grid>
        <?php foreach ($products as $product): ?>
          <div class="col" data-product-card data-category="<?= h($product['category']) ?>">
            <a class="card-link d-block h-100" href="izdelek.php?id=<?= h($product['id']) ?>">
              <article class="card product-card h-100 border-0 overflow-hidden">
                <div class="card-media product-card-media">
                  <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>" />
                </div>
                <div class="card-body p-4">
                  <p class="eyebrow"><?= h($product['categoryLabel']) ?></p>
                  <h3 class="h4"><?= h($product['name']) ?></h3>
                  <p class="mb-0"><?= h($product['shortDescription']) ?></p>
                </div>
              </article>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="empty-note mb-0" data-empty-state hidden>Ni izdelkov v tej kategoriji.</p>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
