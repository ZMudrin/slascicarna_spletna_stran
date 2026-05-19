<?php

declare(strict_types=1);

$phoneHref = 'tel:' . preg_replace('/\s+/', '', $siteConfig['phone']);
$emailHref = 'mailto:' . $siteConfig['email'];
?>
<header class="site-header">
  <nav class="navbar navbar-expand-md" aria-label="Glavna navigacija">
    <div class="container">
      <a class="navbar-brand brand" href="domov.php" aria-label="SweetCraft domov">
        <span class="brand-mark">
          <img class="brand-mark-img" src="slike/ikona-logo.png" alt="" aria-hidden="true" />
        </span>
        <span class="brand-copy">
          <strong><?= h($siteConfig['name']) ?></strong>
          <small><?= h($siteConfig['tagline']) ?></small>
        </span>
      </a>

      <button
        class="navbar-toggler shadow-none border-0"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#siteNavbar"
        aria-controls="siteNavbar"
        aria-expanded="false"
        aria-label="Preklopi navigacijo"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-md-end" id="siteNavbar">
        <ul class="navbar-nav ms-auto align-items-md-center gap-md-2">
          <?php foreach ($navItems as $item): ?>
            <?php $isActive = $pageId === $item['id']; ?>
            <li class="nav-item">
              <a
                class="nav-link<?= $isActive ? ' active' : '' ?>"
                href="<?= h($item['href']) ?>"
                <?= $isActive ? 'aria-current="page"' : '' ?>
              >
                <?= h($item['label']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
