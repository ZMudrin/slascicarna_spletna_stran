<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/studio-auth.php';
require_once __DIR__ . '/includes/studio-service.php';

function studio_status_class(string $status): string
{
    return match ($status) {
        'novo' => 'is-fresh',
        'potrjeno' => 'is-confirmed',
        'v izdelavi' => 'is-progress',
        'pripravljeno' => 'is-ready',
        'zakljuceno' => 'is-closed',
        default => 'is-fresh',
    };
}

start_studio_session();

$loginError = null;
$action = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (string) ($_POST['studio_action'] ?? '')
    : '';

if ($action === 'login') {
    if (studio_attempt_login((string) ($_POST['studio_password'] ?? ''))) {
        studio_set_flash('success', 'Dostop odobren. Dobrodošli v SweetCraft Studio.');
        header('Location: studio.php');
        exit;
    }

    $loginError = 'Geslo ni pravilno. Poskusite znova.';
}

if ($action === 'logout') {
    studio_logout();
    header('Location: studio.php');
    exit;
}

$isAuthenticated = studio_is_authenticated();
$databaseStatusMessage = '';
$pdo = $isAuthenticated ? connect_database($databaseStatusMessage) : null;

if ($isAuthenticated && $_SERVER['REQUEST_METHOD'] === 'POST' && $action !== '' && $action !== 'login' && $action !== 'logout') {
    if ($pdo === null) {
        studio_set_flash('danger', 'Upravljanje trenutno ni mogoče, ker baza ni dosegljiva.');
        header('Location: studio.php');
        exit;
    }

    try {
        if ($action === 'save_product') {
            $productId = studio_save_product($pdo, $_POST);
            studio_set_flash('success', 'Izdelek je bil uspešno shranjen v podatkovno bazo.');
            header('Location: studio.php?edit_product=' . $productId . '#product-form');
            exit;
        }

        if ($action === 'delete_product') {
            studio_delete_product($pdo, (int) ($_POST['product_id'] ?? 0));
            studio_set_flash('success', 'Izdelek je bil odstranjen iz podatkovne baze.');
            header('Location: studio.php#products');
            exit;
        }

        if ($action === 'save_order') {
            studio_save_order($pdo, $_POST);
            studio_set_flash('success', 'Naročilo je bilo uspešno posodobljeno.');
            header('Location: studio.php#orders');
            exit;
        }

        if ($action === 'delete_order') {
            studio_delete_order($pdo, (int) ($_POST['order_id'] ?? 0));
            studio_set_flash('success', 'Naročilo je bilo izbrisano iz podatkovne baze.');
            header('Location: studio.php#orders');
            exit;
        }
    } catch (Throwable $throwable) {
        studio_set_flash('danger', $throwable->getMessage());
        header('Location: studio.php' . ($action === 'save_product' ? '#product-form' : '#orders'));
        exit;
    }
}

$flash = studio_consume_flash();
$categories = $pdo ? fetch_categories($pdo) : [];
$products = $pdo ? fetch_products($pdo) : [];
$orders = $pdo ? fetch_order_requests($pdo) : [];
$metrics = $pdo ? fetch_dashboard_metrics($pdo) : [
    'products' => 0,
    'categories' => 0,
    'orders' => 0,
    'newOrders' => 0,
];

$editProductId = isset($_GET['edit_product']) ? (int) $_GET['edit_product'] : 0;
$editingProduct = $editProductId > 0 ? studio_find_product_for_edit($products, $editProductId) : null;
$productForm = studio_product_form_defaults();

if ($editingProduct !== null) {
    $productForm = [
        'id' => (string) $editingProduct['id'],
        'categoryId' => (string) $editingProduct['categoryId'],
        'name' => $editingProduct['name'],
        'shortDescription' => $editingProduct['shortDescription'],
        'description' => $editingProduct['description'],
        'image' => $editingProduct['image'],
        'flavor' => $editingProduct['flavor'],
        'size' => $editingProduct['size'],
        'occasion' => $editingProduct['occasion'],
    ];
}

$pageId = 'studio';
$pageTitle = 'SweetCraft Studio';
$pageDescription = 'Zaščiten SweetCraft Studio za upravljanje izdelkov in naročil.';
require __DIR__ . '/includes/head.php';
?>
<main class="studio-page flex-grow-1">
  <section class="studio-hero">
    <div class="container">
      <div class="studio-hero-shell">
        <div class="studio-hero-copy">
          <p class="studio-kicker">SweetCraft Studio</p>
          <h1>Sladki nadzorni center za podatke, naročila in ponudbo</h1>
          <p class="lead mb-0">
            Zaklenjena delovna podstran za nalogo z bazo podatkov. Tu lahko
            bereš podatke iz baze, dodajaš nove izdelke, posodabljaš naročila
            in brišeš zapise, ko jih ne potrebuješ več.
          </p>
        </div>

        <?php if (!$isAuthenticated): ?>
          <section class="studio-login-card" aria-labelledby="studio-login-title">
            <p class="eyebrow mb-2">Vstop samo z geslom</p>
            <h2 id="studio-login-title">Odkleni Studio</h2>
            <p>
              Za dostop ni uporabniškega imena. Vpiši samo geslo in nadaljuj v
              upravljalni del aplikacije.
            </p>
            <div class="studio-auth-note">
              <span class="studio-auth-dot" aria-hidden="true"></span>
              Geslo za dostop: <strong>sweetcraft</strong>
            </div>

            <?php if ($loginError !== null): ?>
              <div class="alert alert-danger mb-3" role="alert">
                <?= h($loginError) ?>
              </div>
            <?php endif; ?>

            <form method="post" class="studio-login-form">
              <input type="hidden" name="studio_action" value="login" />
              <label class="form-label" for="studio_password">Geslo</label>
              <input
                class="form-control"
                id="studio_password"
                name="studio_password"
                type="password"
                placeholder="Vnesi geslo"
                required
              />
              <button class="btn btn-primary studio-cta-btn w-100 mt-3" type="submit">Vstopi v Studio</button>
            </form>
          </section>
        <?php else: ?>
          <aside class="studio-hero-actions">
            <div class="studio-metric-grid">
              <article class="studio-metric-card">
                <span>Izdelki</span>
                <strong><?= h((string) $metrics['products']) ?></strong>
              </article>
              <article class="studio-metric-card">
                <span>Kategorije</span>
                <strong><?= h((string) $metrics['categories']) ?></strong>
              </article>
              <article class="studio-metric-card">
                <span>Naročila</span>
                <strong><?= h((string) $metrics['orders']) ?></strong>
              </article>
              <article class="studio-metric-card">
                <span>Novo</span>
                <strong><?= h((string) $metrics['newOrders']) ?></strong>
              </article>
            </div>

            <form method="post" class="d-grid">
              <input type="hidden" name="studio_action" value="logout" />
              <button class="btn btn-light studio-logout-btn" type="submit">Odjava iz Studia</button>
            </form>
          </aside>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php if ($isAuthenticated): ?>
    <section class="page-section pt-0">
      <div class="container">
        <?php if ($flash !== null): ?>
          <div class="alert alert-<?= h($flash['type']) ?> mb-4" role="status">
            <?= h($flash['message']) ?>
          </div>
        <?php endif; ?>

        <?php if ($pdo === null): ?>
          <div class="alert alert-warning mb-0" role="status">
            <?= h($databaseStatusMessage) ?>
          </div>
        <?php else: ?>
          <nav class="studio-toolbar" aria-label="Hitra navigacija Studia">
            <a class="btn studio-nav-btn" href="#product-form">
              <span class="studio-nav-btn-label">Urejanje ponudbe</span>
              <strong>Dodaj ali uredi izdelek</strong>
            </a>
            <a class="btn studio-nav-btn" href="#orders">
              <span class="studio-nav-btn-label">Obdelava naročil</span>
              <strong>Preglej naročila</strong>
            </a>
          </nav>

          <section class="studio-section" id="product-form">
            <div class="row g-4">
              <div class="col-xl-5">
                <article class="studio-panel studio-form-panel">
                  <p class="eyebrow mb-2"><?= $editingProduct !== null ? 'Urejanje izdelka' : 'Nov vnos izdelka' ?></p>
                  <h2><?= $editingProduct !== null ? 'Posodobi izdelek v ponudbi' : 'Dodaj nov izdelek v ponudbo' ?></h2>
                  <p>
                    Ta obrazec pokrije <strong>INSERT</strong> in <strong>UPDATE</strong>.
                    Po shranjevanju se podatki takoj prikažejo na javni strani ponudbe.
                  </p>

                  <form method="post" class="studio-admin-form">
                    <input type="hidden" name="studio_action" value="save_product" />
                    <?php if ($productForm['id'] !== ''): ?>
                      <input type="hidden" name="id" value="<?= h($productForm['id']) ?>" />
                    <?php endif; ?>

                    <div class="mb-3">
                      <label class="form-label" for="categoryId">Kategorija</label>
                      <select class="form-select" id="categoryId" name="categoryId" required>
                        <option value="">Izberi kategorijo...</option>
                        <?php foreach ($categories as $category): ?>
                          <option value="<?= h((string) $category['id']) ?>"<?= $productForm['categoryId'] === (string) $category['id'] ? ' selected' : '' ?>>
                            <?= h($category['name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label" for="productName">Ime izdelka</label>
                      <input class="form-control" id="productName" name="name" type="text" value="<?= h($productForm['name']) ?>" required />
                    </div>

                    <div class="mb-3">
                      <label class="form-label" for="shortDescription">Kratek opis</label>
                      <input class="form-control" id="shortDescription" name="shortDescription" type="text" value="<?= h($productForm['shortDescription']) ?>" required />
                    </div>

                    <div class="mb-3">
                      <label class="form-label" for="productDescription">Podroben opis</label>
                      <textarea class="form-control" id="productDescription" name="description" required><?= h($productForm['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                      <label class="form-label" for="imagePath">Pot do slike</label>
                      <input class="form-control" id="imagePath" name="image" type="text" value="<?= h($productForm['image']) ?>" required />
                    </div>

                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label" for="flavor">Okus</label>
                        <input class="form-control" id="flavor" name="flavor" type="text" value="<?= h($productForm['flavor']) ?>" required />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label" for="size">Velikost</label>
                        <input class="form-control" id="size" name="size" type="text" value="<?= h($productForm['size']) ?>" required />
                      </div>
                    </div>

                    <div class="mt-3">
                      <label class="form-label" for="occasion">Priložnost</label>
                      <input class="form-control" id="occasion" name="occasion" type="text" value="<?= h($productForm['occasion']) ?>" required />
                    </div>

                    <div class="studio-form-actions mt-4">
                      <button class="btn btn-primary studio-cta-btn" type="submit">
                        <?= $editingProduct !== null ? 'Shrani spremembe' : 'Dodaj izdelek' ?>
                      </button>
                      <?php if ($editingProduct !== null): ?>
                        <a class="btn studio-neutral-btn" href="studio.php#product-form">Nov vnos</a>
                      <?php endif; ?>
                    </div>
                  </form>
                </article>
              </div>

              <div class="col-xl-7" id="products">
                <article class="studio-panel">
                  <div class="studio-panel-head">
                    <div>
                      <p class="eyebrow mb-2">Bralni pogled</p>
                      <h2>Trenutni izdelki iz baze</h2>
                    </div>
                    <p class="studio-panel-copy mb-0">
                      Ta del pokaže <strong>SELECT</strong> na tabeli izdelkov. Vsaka kartica je živa
                      reprezentacija zapisa iz baze.
                    </p>
                  </div>

                  <div class="studio-product-grid">
                    <?php foreach ($products as $product): ?>
                      <article class="studio-product-card">
                        <div class="studio-product-image">
                          <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>" />
                        </div>
                        <div class="studio-product-body">
                          <p class="studio-chip"><?= h($product['categoryLabel']) ?></p>
                          <h3 class="studio-product-title"><?= h($product['name']) ?></h3>
                          <p class="studio-product-summary"><?= h($product['shortDescription']) ?></p>
                          <dl class="studio-mini-list">
                            <div>
                              <dt>Okus</dt>
                              <dd><?= h($product['flavor']) ?></dd>
                            </div>
                            <div>
                              <dt>Velikost</dt>
                              <dd><?= h($product['size']) ?></dd>
                            </div>
                          </dl>
                          <div class="studio-card-actions">
                            <a class="btn studio-neutral-btn" href="studio.php?edit_product=<?= h((string) $product['id']) ?>#product-form">Uredi</a>
                            <form class="studio-delete-form" method="post" onsubmit="return confirm('Ali res želiš izbrisati izdelek?');">
                              <input type="hidden" name="studio_action" value="delete_product" />
                              <input type="hidden" name="product_id" value="<?= h((string) $product['id']) ?>" />
                              <button class="btn btn-outline-danger" type="submit">Izbriši</button>
                            </form>
                          </div>
                        </div>
                      </article>
                    <?php endforeach; ?>
                  </div>
                </article>
              </div>
            </div>
          </section>

          <section class="studio-section" id="orders">
            <article class="studio-panel">
              <div class="studio-panel-head">
                <div>
                  <p class="eyebrow mb-2">Naročila iz baze</p>
                  <h2>Nadzor naročil in statusov</h2>
                </div>
                <p class="studio-panel-copy mb-0">
                  Javna stran <strong>Naročilo</strong> pošilja podatke z obrazcem v bazo,
                  tu pa jih lahko pregleduješ, urejaš in brišeš.
                </p>
              </div>

              <?php if ($orders === []): ?>
                <div class="empty-panel">
                  <h1>Še ni oddanih naročil</h1>
                  <p>Ko uporabnik odda obrazec na javni strani, se bo naročilo pojavilo tukaj.</p>
                </div>
              <?php else: ?>
                <div class="studio-orders-grid">
                  <?php foreach ($orders as $order): ?>
                    <article class="studio-order-card">
                      <div class="studio-order-head">
                        <div>
                          <p class="studio-chip mb-2">Naročilo #<?= h((string) $order['id']) ?></p>
                          <h3><?= h($order['customer_name']) ?></h3>
                          <p class="mb-0">
                            <?= h($order['product_name']) ?> · <?= h($order['category_name']) ?>
                          </p>
                        </div>
                        <span class="studio-status <?= h(studio_status_class($order['status'])) ?>">
                          <?= h($order['status']) ?>
                        </span>
                      </div>

                      <dl class="studio-order-meta">
                        <div>
                          <dt>Način</dt>
                          <dd><?= h($order['order_mode']) ?></dd>
                        </div>
                        <div>
                          <dt>Priložnost</dt>
                          <dd><?= h($order['occasion']) ?></dd>
                        </div>
                        <div>
                          <dt>Velikost</dt>
                          <dd><?= h($order['size_option']) ?></dd>
                        </div>
                        <div>
                          <dt>Vneseno</dt>
                          <dd><?= h((string) $order['created_at']) ?></dd>
                        </div>
                      </dl>

                      <form method="post" class="studio-admin-form studio-order-form">
                        <input type="hidden" name="studio_action" value="save_order" />
                        <input type="hidden" name="order_id" value="<?= h((string) $order['id']) ?>" />

                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label" for="status-<?= h((string) $order['id']) ?>">Status</label>
                            <select class="form-select" id="status-<?= h((string) $order['id']) ?>" name="status">
                              <?php foreach (studio_allowed_order_statuses() as $status): ?>
                                <option value="<?= h($status) ?>"<?= $order['status'] === $status ? ' selected' : '' ?>>
                                  <?= h($status) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label" for="pickup-<?= h((string) $order['id']) ?>">Datum prevzema</label>
                            <input class="form-control" id="pickup-<?= h((string) $order['id']) ?>" name="pickup_date" type="date" value="<?= h((string) $order['pickup_date']) ?>" />
                          </div>
                        </div>

                        <div class="row g-3 mt-0">
                          <div class="col-md-4">
                            <label class="form-label" for="customer-name-<?= h((string) $order['id']) ?>">Ime</label>
                            <input class="form-control" id="customer-name-<?= h((string) $order['id']) ?>" name="customer_name" type="text" value="<?= h($order['customer_name']) ?>" />
                          </div>
                          <div class="col-md-4">
                            <label class="form-label" for="customer-phone-<?= h((string) $order['id']) ?>">Telefon</label>
                            <input class="form-control" id="customer-phone-<?= h((string) $order['id']) ?>" name="customer_phone" type="text" value="<?= h($order['customer_phone']) ?>" />
                          </div>
                          <div class="col-md-4">
                            <label class="form-label" for="customer-email-<?= h((string) $order['id']) ?>">E-pošta</label>
                            <input class="form-control" id="customer-email-<?= h((string) $order['id']) ?>" name="customer_email" type="email" value="<?= h($order['customer_email']) ?>" />
                          </div>
                        </div>

                        <div class="mt-3">
                          <label class="form-label" for="special-notes-<?= h((string) $order['id']) ?>">Opombe</label>
                          <textarea class="form-control" id="special-notes-<?= h((string) $order['id']) ?>" name="special_notes"><?= h((string) ($order['special_notes'] ?? '')) ?></textarea>
                        </div>

                        <div class="studio-card-actions mt-4">
                          <button class="btn btn-primary studio-cta-btn" type="submit">Shrani naročilo</button>
                        </div>
                      </form>

                      <form method="post" class="studio-delete-form" onsubmit="return confirm('Ali res želiš izbrisati to naročilo?');">
                        <input type="hidden" name="studio_action" value="delete_order" />
                        <input type="hidden" name="order_id" value="<?= h((string) $order['id']) ?>" />
                        <button class="btn btn-outline-danger" type="submit">Izbriši naročilo</button>
                      </form>
                    </article>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </article>
          </section>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
