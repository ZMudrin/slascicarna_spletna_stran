<?php

declare(strict_types=1);

$phoneHref = 'tel:' . preg_replace('/\s+/', '', $siteConfig['phone']);
$emailHref = 'mailto:' . $siteConfig['email'];
?>
<footer class="site-footer mt-auto">
  <div class="container py-5">
    <div class="row g-4">
      <section class="col-lg-5">
        <div class="brand footer-brand">
          <span class="brand-mark">
            <img class="brand-mark-img" src="slike/ikona-logo.png" alt="" aria-hidden="true" />
          </span>
          <span class="brand-copy">
            <strong><?= h($siteConfig['name']) ?></strong>
            <small><?= h($siteConfig['tagline']) ?></small>
          </span>
        </div>
        <p class="footer-copy mb-0">
          Ročno izdelane sladice po meri za vse priložnosti. Vsaka sladica je ustvarjena z ljubeznijo.
        </p>
      </section>

      <nav class="col-sm-6 col-lg-3" aria-label="Povezave v nogi">
        <h2 class="footer-title">Navigacija</h2>
        <ul class="footer-links list-unstyled mb-0">
          <?php foreach ($navItems as $item): ?>
            <li><a class="footer-link" href="<?= h($item['href']) ?>"><?= h($item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <section class="col-sm-6 col-lg-4">
        <h2 class="footer-title">Kontakt</h2>
        <address class="footer-contact mb-0">
          <p><a class="footer-link" href="<?= h($phoneHref) ?>"><?= h($siteConfig['phone']) ?></a></p>
          <p><a class="footer-link" href="<?= h($emailHref) ?>"><?= h($siteConfig['email']) ?></a></p>
          <p class="mb-0"><?= h($siteConfig['city']) ?>, <?= h($siteConfig['country']) ?></p>
        </address>
      </section>
    </div>
  </div>
  <div class="container footer-bottom">
    <p class="mb-0">&copy; 2026 SweetCraft. Vse pravice pridržane.</p>
  </div>
</footer>
<script src="js/bootstrap.bundle.min.js"></script>
<script type="module" src="<?= h(asset_path('js/site.js')) ?>"></script>
  </body>
</html>
