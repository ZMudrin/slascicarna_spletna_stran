<?php

declare(strict_types=1);

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
      <div
        class="d-flex flex-wrap justify-content-center gap-2 mb-5"
        role="group"
        aria-label="Filtriranje izdelkov"
      >
        <button class="btn filter-btn rounded-pill is-active" type="button" data-filter="vse" aria-pressed="true">Vse</button>
        <button class="btn filter-btn rounded-pill" type="button" data-filter="torte" aria-pressed="false">Torte</button>
        <button class="btn filter-btn rounded-pill" type="button" data-filter="kolacki" aria-pressed="false">Kolački</button>
        <button class="btn filter-btn rounded-pill" type="button" data-filter="sezonsko" aria-pressed="false">Sezonsko</button>
      </div>

      <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-xl-3" data-product-grid></div>
      <p class="empty-note mb-0" data-empty-state hidden>Ni izdelkov v tej kategoriji.</p>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
