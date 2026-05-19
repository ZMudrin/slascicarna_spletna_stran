<?php

declare(strict_types=1);

$pageId = 'kontakt';
$pageTitle = 'SweetCraft | Kontakt';
$pageDescription = 'Kontaktirajte SweetCraft za naročila po meri, vprašanja in dogovor o sladkih pogostitvah.';
require __DIR__ . '/includes/head.php';
?>
<main class="page-main flex-grow-1">
  <section class="page-hero">
    <div class="container">
      <header class="center-copy text-center">
        <h1>Kontakt</h1>
        <p class="lead mb-0">Stopite v stik z nami, radi vam bomo pomagali.</p>
      </header>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <div class="row g-4 g-xl-5 align-items-start">
        <section class="col-lg-5" aria-labelledby="kontaktni-podatki">
          <h2 class="mb-4" id="kontaktni-podatki">Kontaktni podatki</h2>

          <div class="vstack gap-3">
            <article class="contact-card">
              <div class="contact-icon">☎</div>
              <div class="contact-copy">
                <h3 class="h5">Telefon</h3>
                <p><a class="contact-link" href="tel:031234567">031 234 567</a></p>
                <p>Pon-Pet: 9:00 - 17:00</p>
              </div>
            </article>

            <article class="contact-card">
              <div class="contact-icon is-accent">✉</div>
              <div class="contact-copy">
                <h3 class="h5">E-pošta</h3>
                <p><a class="contact-link" href="mailto:info@sweetcraft.si">info@sweetcraft.si</a></p>
                <p>Odgovorimo v 24 urah</p>
              </div>
            </article>

            <article class="contact-card">
              <div class="contact-icon">⌂</div>
              <div class="contact-copy">
                <h3 class="h5">Naslov</h3>
                <p>Gosposka ulica 12</p>
                <p>2000 Maribor</p>
                <p>Slovenija</p>
              </div>
            </article>

            <article class="contact-card">
              <div class="contact-icon is-accent">⏰</div>
              <div class="contact-copy">
                <h3 class="h5">Delovni čas</h3>
                <p>Ponedeljek - Petek: 9:00 - 18:00</p>
                <p>Sobota: 9:00 - 14:00</p>
                <p>Nedelja: Zaprto</p>
              </div>
            </article>
          </div>

          <aside class="map-card map-card-embed mt-4">
            <div class="map-frame">
              <iframe
                class="map-embed"
                title="Lokacija SweetCraft v Mariboru"
                src="https://www.google.com/maps?q=Gosposka+ulica+12,+2000+Maribor,+Slovenia&z=16&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
            <div class="map-meta">
              <p class="mb-1"><strong>Gosposka ulica 12</strong></p>
              <p class="mb-2">2000 Maribor, Slovenija</p>
              <a
                class="map-link"
                href="https://www.google.com/maps?q=Gosposka+ulica+12,+2000+Maribor,+Slovenia"
                target="_blank"
                rel="noopener noreferrer"
              >
                Odpri zemljevid
              </a>
            </div>
          </aside>
        </section>

        <section class="col-lg-7" aria-labelledby="kontaktni-obrazec">
          <h2 class="mb-4" id="kontaktni-obrazec">Pošlji sporočilo</h2>

          <section class="success-panel mb-4" data-contact-success aria-live="polite" hidden>
            <div class="success-icon">✓</div>
            <h3 class="h4">Sporočilo poslano!</h3>
            <p class="mb-0">Kmalu se vam javimo.</p>
          </section>

          <form data-contact-form>
            <div class="form-card">
              <div class="mb-3">
                <label class="form-label" for="ime">Ime in priimek *</label>
                <input class="form-control" id="ime" name="ime" type="text" placeholder="Janez Novak" required />
              </div>

              <div class="mb-3">
                <label class="form-label" for="email">E-pošta *</label>
                <input class="form-control" id="email" name="email" type="email" placeholder="janez.novak@email.si" required />
              </div>

              <div>
                <label class="form-label" for="sporocilo">Sporočilo *</label>
                <textarea class="form-control" id="sporocilo" name="sporocilo" placeholder="Vaše sporočilo..." required></textarea>
              </div>
            </div>

            <div class="d-grid gap-2 mt-4">
              <button class="btn btn-primary w-100" type="submit">Pošlji sporočilo</button>
              <p class="form-note mb-0">* označuje obvezna polja</p>
            </div>
          </form>
        </section>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
