<?php

declare(strict_types=1);

header('Refresh: 0; url=domov.php');

$pageTitle = 'SweetCraft';
$pageDescription = 'Preusmerjanje na SweetCraft spletno stran.';
require __DIR__ . '/includes/head.php';
?>
<main class="redirect-shell flex-grow-1">
  <section class="container">
    <article class="row justify-content-center">
      <div class="col-lg-6">
        <div class="empty-panel">
          <h1>Preusmerjanje na SweetCraft</h1>
          <p>Če se stran ne odpre samodejno, klikni spodaj.</p>
          <p class="mb-0">
            <a class="btn btn-primary" href="domov.php">Odpri domov</a>
          </p>
        </div>
      </div>
    </article>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
