<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/catalog.php';

$databaseStatusMessage = '';
$pdo = connect_database($databaseStatusMessage);
$productId = isset($_GET['id']) ? (int) $_GET['id'] : 1;
$product = fetch_product_by_id($pdo, $productId);
$relatedProducts = $product !== null ? fetch_related_products($pdo, $product['categoryId'], $product['id']) : [];

$pageId = 'izdelek';
$pageTitle = $product !== null ? 'SweetCraft | ' . $product['name'] : 'SweetCraft | Izdelek';
$pageDescription = $product !== null
    ? $product['shortDescription']
    : 'Podrobnosti izbranega izdelka SweetCraft in možnost hitrega naročila.';
require __DIR__ . '/includes/head.php';
?>
<main class="page-main flex-grow-1">
  <section class="page-section">
    <div class="container">
      <?php if ($pdo === null): ?>
        <div class="alert alert-warning mb-4" role="status">
          <?= h($databaseStatusMessage) ?>
        </div>
      <?php endif; ?>

      <?php if ($product === null): ?>
        <div class="row justify-content-center">
          <div class="col-lg-8 col-xl-6">
            <div class="empty-panel">
              <h1>Izdelek ni bil najden</h1>
              <p>Izbrani izdelek ne obstaja več ali pa je povezava napačna.</p>
              <a class="btn btn-primary" href="ponudba.php">Nazaj na ponudbo</a>
            </div>
          </div>
        </div>
      <?php else: ?>
        <nav aria-label="Drobtinice">
          <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="domov.php">Domov</a></li>
            <li class="breadcrumb-item"><a href="ponudba.php">Ponudba</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= h($product['name']) ?></li>
          </ol>
        </nav>

        <article class="row g-4 g-xl-5 align-items-start">
          <div class="col-lg-6">
            <figure class="detail-media mb-0">
              <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>" />
            </figure>
          </div>

          <div class="col-lg-6">
            <div class="detail-copy">
              <div class="rating-row mb-3">
                <span class="stars">★★★★★</span>
                <span>(127 ocen)</span>
              </div>
              <p class="eyebrow mb-2"><?= h($product['categoryLabel']) ?></p>
              <h1><?= h($product['name']) ?></h1>
              <p class="lead mb-4"><?= h($product['description']) ?></p>

              <section class="info-card mb-4">
                <h2 class="h3 mb-3">Informacije</h2>
                <dl class="row g-3 mb-0 info-list">
                  <div class="col-sm-4">
                    <dt>Okus</dt>
                    <dd><?= h($product['flavor']) ?></dd>
                  </div>
                  <div class="col-sm-4">
                    <dt>Velikost</dt>
                    <dd><?= h($product['size']) ?></dd>
                  </div>
                  <div class="col-sm-4">
                    <dt>Priložnost</dt>
                    <dd><?= h($product['occasion']) ?></dd>
                  </div>
                </dl>
              </section>

              <a class="btn btn-primary w-100 mb-3" href="narocilo.php?product=<?= h($product['id']) ?>">Naroči ta izdelek</a>

              <aside class="notice">
                <p class="mb-0"><strong>Pomembno:</strong> Vse sladice so narejene po naročilu iz svežih sestavin. Prosimo, naročite vsaj <strong>3 dni vnaprej</strong> za najboljšo kakovost.</p>
              </aside>
            </div>
          </div>
        </article>

        <?php if ($relatedProducts !== []): ?>
          <section class="page-section px-0 pb-0">
            <header class="center-copy text-center mb-4">
              <h2 class="section-title mb-3">Podobni izdelki</h2>
            </header>
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-xl-3">
              <?php foreach ($relatedProducts as $relatedProduct): ?>
                <div class="col">
                  <a class="card-link d-block h-100" href="izdelek.php?id=<?= h($relatedProduct['id']) ?>">
                    <article class="card product-card h-100 border-0 overflow-hidden">
                      <div class="card-media product-card-media">
                        <img src="<?= h($relatedProduct['image']) ?>" alt="<?= h($relatedProduct['name']) ?>" />
                      </div>
                      <div class="card-body p-4">
                        <h3 class="h4"><?= h($relatedProduct['name']) ?></h3>
                        <p class="mb-0"><?= h($relatedProduct['shortDescription']) ?></p>
                      </div>
                    </article>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
