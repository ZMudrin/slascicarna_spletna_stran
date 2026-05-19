<?php

declare(strict_types=1);

$pageId = 'domov';
$pageTitle = 'SweetCraft | Domov';
$pageDescription = 'SweetCraft je butična slaščičarna za ročno izdelane torte, kolačke in sladice po meri.';
require __DIR__ . '/includes/head.php';
?>
<main class="page-main flex-grow-1">
  <section class="hero-section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="hero-copy">
            <p class="hero-badge mb-3">Ročno izdelano z ljubeznijo</p>
            <h1>Ročno izdelane sladice po meri</h1>
            <p class="lead mb-4">
              Ustvarjamo unikatne torte za posebne trenutke. Vsaka sladica
              je izdelana iz najboljših sestavin in z največjo skrbnostjo.
            </p>
            <a class="btn btn-primary hero-cta-btn" href="narocilo.php">Naroči zdaj →</a>
          </div>
        </div>

        <div class="col-lg-6">
          <figure class="hero-media mb-0">
            <img src="slike/cake-with-rose.jpg" alt="Elegantna torta" />
          </figure>
        </div>
      </div>
    </div>
  </section>

  <section class="page-section pt-0">
    <div class="container">
      <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-xl-3">
        <div class="col">
          <article class="card feature-card h-100 border-0 text-center">
            <div class="card-body p-4 p-xl-5">
              <div class="feature-icon">♥</div>
              <h2 class="h4">Ročno izdelano</h2>
              <p class="mb-0">
                Vsaka sladica je ustvarjena z ljubeznijo in skrbnostjo.
              </p>
            </div>
          </article>
        </div>

        <div class="col">
          <article class="card feature-card h-100 border-0 text-center">
            <div class="card-body p-4 p-xl-5">
              <div class="feature-icon is-accent">★</div>
              <h2 class="h4">Kakovostne sestavine</h2>
              <p class="mb-0">
                Uporabljamo samo najboljše in sveže sestavine.
              </p>
            </div>
          </article>
        </div>

        <div class="col">
          <article class="card feature-card h-100 border-0 text-center">
            <div class="card-body p-4 p-xl-5">
              <div class="feature-icon">⏱</div>
              <h2 class="h4">Po naročilu</h2>
              <p class="mb-0">Sveže pripravljeno za vaš posebni dan.</p>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>

  <section class="page-section section-band">
    <div class="container">
      <article class="center-copy text-center">
        <h2 class="section-title">O SweetCraft</h2>
        <p>
          SweetCraft je butična slaščičarna iz Maribora, kjer tradicija
          sreča sodobnost. Specializirani smo za ročno izdelane torte po
          meri, ki so ustvarjene posebej za vas. Vsaka sladica pripoveduje
          svojo zgodbo in je izdelana z največjo skrbnostjo ter ljubeznijo
          do detajlov.
        </p>
        <p class="mb-0">
          Naše poslanstvo je ustvarjati nepozabne trenutke z vsakim
          grizljajem.
        </p>
      </article>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <header class="center-copy text-center mb-4">
        <h2 class="section-title mb-3">Naša ponudba</h2>
      </header>
      <div
        class="row g-4 row-cols-1 row-cols-md-2 row-cols-xl-3"
        data-home-categories
      ></div>
    </div>
  </section>

  <section class="page-section section-soft">
    <div class="container">
      <header class="center-copy text-center mb-4">
        <h2 class="section-title mb-3">Izpostavljeni izdelki</h2>
      </header>
      <div
        class="row g-4 row-cols-1 row-cols-md-2 row-cols-xl-4"
        data-home-featured
      ></div>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <aside class="cta-banner">
        <h2 class="section-title">Naroči torto po meri</h2>
        <p class="mb-4">
          Imate posebno priložnost? Naročite edinstveno torto, prilagojeno
          vašim željam in potrebam. Naša ekipa bo poskrbela, da bo vaš dan
          nepozaben.
        </p>
        <a class="btn btn-light" href="narocilo.php">Pošlji povpraševanje</a>
      </aside>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
