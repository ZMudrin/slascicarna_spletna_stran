<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/order-handler.php';

$pageId = 'narocilo';
$pageTitle = 'SweetCraft | Naročilo';
$pageDescription = 'Oddajte naročilo za torto, kolačke ali sezonske sladice SweetCraft prek preglednega obrazca.';

$databaseStatusMessage = '';
$pdo = connect_database($databaseStatusMessage);
$orderState = process_order_request($pdo, $databaseStatusMessage);

require __DIR__ . '/includes/head.php';
?>
<main class="page-main flex-grow-1">
  <section class="page-hero">
    <div class="container">
      <header class="center-copy text-center">
        <h1>Naročilo</h1>
        <p class="lead mb-0">
          Izpolnite obrazec in oddajte naročilo za vašo sladico po meri.
        </p>
      </header>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
          <div class="alert alert-<?= h($orderState['statusClass']) ?> mb-4" role="status">
            <?= h($orderState['statusMessage']) ?>
          </div>

          <?php if ($orderState['errors'] !== []): ?>
            <div class="alert alert-danger mb-4" role="alert">
              <p class="fw-semibold mb-2">Obrazca ni bilo mogoče shraniti:</p>
              <ul class="mb-0 ps-3">
                <?php foreach ($orderState['errors'] as $error): ?>
                  <li><?= h($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <section
            class="success-panel mb-4"
            aria-live="polite"
            <?= $orderState['success'] ? '' : 'hidden' ?>
          >
            <div class="success-icon">✓</div>
            <h2>Naročilo oddano!</h2>
            <p>
              Hvala za vaše naročilo. Kmalu vas bomo kontaktirali na
              <a class="contact-link" href="tel:<?= h(str_replace(' ', '', $orderState['successPhone'] !== '' ? $orderState['successPhone'] : '031234567')) ?>">
                <strong><?= h($orderState['successPhone'] !== '' ? $orderState['successPhone'] : '031 234 567') ?></strong>
              </a>
              za potrditev in dodatne podrobnosti.
            </p>
            <div class="success-summary">
              <p>Prejeli boste e-poštno sporočilo s potrdilom na:</p>
              <p class="mb-0">
                <a class="contact-link" href="mailto:<?= h($orderState['successEmail'] !== '' ? $orderState['successEmail'] : 'info@sweetcraft.si') ?>">
                  <strong><?= h($orderState['successEmail'] !== '' ? $orderState['successEmail'] : 'info@sweetcraft.si') ?></strong>
                </a>
              </p>
            </div>
            <a class="btn btn-primary" href="narocilo.php">Novo naročilo</a>
          </section>

          <form
            action="narocilo.php"
            method="post"
            data-order-form
            data-initial-mode="<?= h($orderState['data']['tipNarocila']) ?>"
            data-initial-product="<?= h($orderState['data']['izdelek']) ?>"
            data-initial-size="<?= h($orderState['data']['velikost']) ?>"
            data-initial-flavor="<?= h($orderState['data']['okus']) ?>"
            <?= $orderState['success'] ? 'hidden' : '' ?>
          >
            <fieldset class="form-card mb-4">
              <legend class="h2 form-section-title">Podrobnosti naročila</legend>

              <div class="mb-3">
                <label class="form-label" for="tipNarocila">Način naročila *</label>
                <select class="form-select" id="tipNarocila" name="tipNarocila" data-order-mode required>
                  <option value="izdelek"<?= $orderState['data']['tipNarocila'] === 'izdelek' ? ' selected' : '' ?>>Izdelek iz ponudbe</option>
                  <option value="torta"<?= $orderState['data']['tipNarocila'] === 'torta' ? ' selected' : '' ?>>Torta po meri</option>
                  <option value="kolacki"<?= $orderState['data']['tipNarocila'] === 'kolacki' ? ' selected' : '' ?>>Paket kolačkov</option>
                  <option value="sezonsko"<?= $orderState['data']['tipNarocila'] === 'sezonsko' ? ' selected' : '' ?>>Sezonska ponudba</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label" for="izdelek">Izbran izdelek ali referenca *</label>
                <select class="form-select" id="izdelek" name="izdelek" data-order-product required></select>
                <p class="order-field-note mb-0 mt-2" data-order-helper>
                  Izberite izdelek iz ponudbe, ki je najbližje vaši želji.
                </p>
              </div>

              <section class="order-selection-card mb-3" data-order-selection hidden>
                <p class="eyebrow mb-2">Izbrana referenca</p>
                <h3 class="h4 mb-3" data-order-selection-name></h3>
                <dl class="row g-3 mb-0 order-selection-list">
                  <div class="col-sm-4">
                    <dt>Okus</dt>
                    <dd data-order-selection-flavor></dd>
                  </div>
                  <div class="col-sm-4">
                    <dt>Velikost</dt>
                    <dd data-order-selection-size></dd>
                  </div>
                  <div class="col-sm-4">
                    <dt>Priložnost</dt>
                    <dd data-order-selection-occasion></dd>
                  </div>
                </dl>
              </section>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="priloznostNarocila">Priložnost *</label>
                  <select class="form-select" id="priloznostNarocila" name="priloznostNarocila" required>
                    <option value="">Izberite...</option>
                    <option value="rojstni-dan"<?= $orderState['data']['priloznostNarocila'] === 'rojstni-dan' ? ' selected' : '' ?>>Rojstni dan</option>
                    <option value="poroka"<?= $orderState['data']['priloznostNarocila'] === 'poroka' ? ' selected' : '' ?>>Poroka</option>
                    <option value="obletnica"<?= $orderState['data']['priloznostNarocila'] === 'obletnica' ? ' selected' : '' ?>>Obletnica</option>
                    <option value="pogostitev"<?= $orderState['data']['priloznostNarocila'] === 'pogostitev' ? ' selected' : '' ?>>Pogostitev</option>
                    <option value="darilo"<?= $orderState['data']['priloznostNarocila'] === 'darilo' ? ' selected' : '' ?>>Darilo</option>
                    <option value="prazniki"<?= $orderState['data']['priloznostNarocila'] === 'prazniki' ? ' selected' : '' ?>>Prazniki</option>
                    <option value="drugo"<?= $orderState['data']['priloznostNarocila'] === 'drugo' ? ' selected' : '' ?>>Drugo</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="velikost">Velikost ali količina *</label>
                  <select class="form-select" id="velikost" name="velikost" data-order-size required></select>
                </div>
              </div>

              <div class="row g-3 mt-0">
                <div class="col-md-6">
                  <label class="form-label" for="datumPrevzema">Datum prevzema *</label>
                  <input
                    class="form-control"
                    id="datumPrevzema"
                    name="datumPrevzema"
                    type="date"
                    value="<?= h($orderState['data']['datumPrevzema']) ?>"
                    required
                  />
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="kolicina">Število oseb ali kosov</label>
                  <input
                    class="form-control"
                    id="kolicina"
                    name="kolicina"
                    type="text"
                    placeholder="Npr. 10 oseb ali 20 kosov"
                    value="<?= h($orderState['data']['kolicina']) ?>"
                  />
                </div>
              </div>

              <div class="mb-3 mt-3">
                <label class="form-label" for="okus">Okus ali prilagoditve *</label>
                <input
                  class="form-control"
                  id="okus"
                  name="okus"
                  data-order-flavor
                  type="text"
                  placeholder="Npr. čokolada z malinami, brez oreščkov ..."
                  value="<?= h($orderState['data']['okus']) ?>"
                  required
                />
              </div>

              <div>
                <label class="form-label" for="opombe">Opombe in posebne želje</label>
                <textarea
                  class="form-control"
                  id="opombe"
                  name="opombe"
                  placeholder="Napis na torti, dekoracija, alergije, posebne zahteve ..."
                ><?= h($orderState['data']['opombe']) ?></textarea>
              </div>
            </fieldset>

            <fieldset class="form-card mb-4">
              <legend class="h2 form-section-title">Kontaktni podatki</legend>

              <div class="mb-3">
                <label class="form-label" for="ime">Ime in priimek *</label>
                <input
                  class="form-control"
                  id="ime"
                  name="ime"
                  type="text"
                  placeholder="Janez Novak"
                  value="<?= h($orderState['data']['ime']) ?>"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label" for="telefon">Telefon *</label>
                <input
                  class="form-control"
                  id="telefon"
                  name="telefon"
                  type="tel"
                  placeholder="031 234 567"
                  value="<?= h($orderState['data']['telefon']) ?>"
                  required
                />
              </div>

              <div>
                <label class="form-label" for="email">E-pošta *</label>
                <input
                  class="form-control"
                  id="email"
                  name="email"
                  type="email"
                  placeholder="janez.novak@email.si"
                  value="<?= h($orderState['data']['email']) ?>"
                  required
                />
              </div>
            </fieldset>

            <div class="d-grid gap-2">
              <button class="btn btn-primary w-100" type="submit">Oddaj naročilo</button>
              <p class="form-note mb-0">* označuje obvezna polja</p>
            </div>

            <aside class="notice mt-4">
              <p class="mb-0">
                <strong>Pomembno:</strong> Naročila sprejemamo vsaj 3 dni pred
                prevzemom. Izdelek iz ponudbe lahko prilagodimo po dogovoru.
                Za nujna naročila nas prosimo kontaktirajte na telefon
                <a class="contact-link" href="tel:031234567"><strong>031 234 567</strong></a>.
              </p>
            </aside>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
