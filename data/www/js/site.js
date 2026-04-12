import {
  featuredProducts,
  navItems,
  productCategories,
  products,
  siteConfig,
} from "./data.js";

function renderHeader() {
  const rawPage = document.body.dataset.page;
  const page = rawPage === "izdelek" ? "ponudba" : rawPage;
  const mount = document.querySelector("[data-site-header]");
  if (!mount) return;

  const navMarkup = navItems
    .map((item) => {
      const isActive = page === item.id;

      return `
        <li class="nav-item">
          <a
            class="nav-link${isActive ? " active" : ""}"
            href="${item.href}"
            ${isActive ? 'aria-current="page"' : ""}
          >
            ${item.label}
          </a>
        </li>
      `;
    })
    .join("");

  mount.innerHTML = `
    <header class="site-header">
      <nav class="navbar navbar-expand-md" aria-label="Glavna navigacija">
        <div class="container">
          <a class="navbar-brand brand" href="domov.html" aria-label="SweetCraft domov">
            <span class="brand-mark">
              <img class="brand-mark-img" src="slike/ikona-logo.png" alt="" aria-hidden="true">
            </span>
            <span class="brand-copy">
              <strong>${siteConfig.name}</strong>
              <small>${siteConfig.tagline}</small>
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
              ${navMarkup}
            </ul>
          </div>
        </div>
      </nav>
    </header>
  `;
}

function renderFooter() {
  const mount = document.querySelector("[data-site-footer]");
  if (!mount) return;
  const phoneHref = `tel:${siteConfig.phone.replace(/\s+/g, "")}`;
  const emailHref = `mailto:${siteConfig.email}`;

  const navMarkup = navItems
    .map(
      (item) => `
        <li>
          <a class="footer-link" href="${item.href}">${item.label}</a>
        </li>
      `,
    )
    .join("");

  mount.innerHTML = `
    <footer class="site-footer mt-auto">
      <div class="container py-5">
        <div class="row g-4">
          <section class="col-lg-5">
            <div class="brand footer-brand">
              <span class="brand-mark">
                <img class="brand-mark-img" src="slike/ikona-logo.png" alt="" aria-hidden="true">
              </span>
              <span class="brand-copy">
                <strong>${siteConfig.name}</strong>
                <small>${siteConfig.tagline}</small>
              </span>
            </div>
            <p class="footer-copy mb-0">
              Ročno izdelane sladice po meri za vse priložnosti. Vsaka sladica je ustvarjena z ljubeznijo.
            </p>
          </section>

          <nav class="col-sm-6 col-lg-3" aria-label="Povezave v nogi">
            <h2 class="footer-title">Navigacija</h2>
            <ul class="footer-links list-unstyled mb-0">
              ${navMarkup}
            </ul>
          </nav>

          <section class="col-sm-6 col-lg-4">
            <h2 class="footer-title">Kontakt</h2>
            <address class="footer-contact mb-0">
              <p><a class="footer-link" href="${phoneHref}">${siteConfig.phone}</a></p>
              <p><a class="footer-link" href="${emailHref}">${siteConfig.email}</a></p>
              <p class="mb-0">${siteConfig.city}, ${siteConfig.country}</p>
            </address>
          </section>
        </div>
      </div>
      <div class="container footer-bottom">
        <p class="mb-0">&copy; 2026 SweetCraft. Vse pravice pridržane.</p>
      </div>
    </footer>
  `;
}

function createCategoryCard(category) {
  return `
    <div class="col">
      <a
        class="card-link d-block h-100"
        href="ponudba.html?filter=${category.id}#${category.id}"
        data-category-link
        data-category-id="${category.id}"
      >
        <article class="card category-card h-100 border-0 overflow-hidden">
          <div class="card-media category-card-media">
            <img src="${category.image}" alt="${category.title}">
          </div>
          <div class="card-body p-4">
            <h3 class="h4">${category.title}</h3>
            <p class="mb-0">${category.description}</p>
          </div>
        </article>
      </a>
    </div>
  `;
}

function createProductCard(product, showCategory = false) {
  return `
    <div class="col">
      <a class="card-link d-block h-100" href="izdelek.html?id=${product.id}">
        <article class="card product-card h-100 border-0 overflow-hidden">
          <div class="card-media product-card-media">
            <img src="${product.image}" alt="${product.name}">
          </div>
          <div class="card-body p-4">
            ${
              showCategory
                ? `<p class="eyebrow">${product.categoryLabel}</p>`
                : ""
            }
            <h3 class="h4">${product.name}</h3>
            <p class="mb-0">${product.shortDescription}</p>
          </div>
        </article>
      </a>
    </div>
  `;
}

function renderDomovCollections() {
  const categoriesMount = document.querySelector("[data-home-categories]");
  const featuredMount = document.querySelector("[data-home-featured]");

  if (categoriesMount) {
    categoriesMount.innerHTML = productCategories.map(createCategoryCard).join("");

    categoriesMount.querySelectorAll("[data-category-link]").forEach((link) => {
      link.addEventListener("click", () => {
        const selectedCategory = link.dataset.categoryId;
        if (selectedCategory) {
          window.sessionStorage.setItem("sweetcraft-offer-filter", selectedCategory);
        }
      });
    });
  }

  if (featuredMount) {
    featuredMount.innerHTML = featuredProducts
      .map((product) => createProductCard(product, true))
      .join("");
  }
}

function renderPonudbaPage() {
  const page = document.body.dataset.page;
  if (page !== "ponudba") return;

  const buttons = Array.from(document.querySelectorAll("[data-filter]"));
  const grid = document.querySelector("[data-product-grid]");
  const empty = document.querySelector("[data-empty-state]");
  if (!grid || !empty) return;

  const render = (filter) => {
    const filtered =
      filter === "vse"
        ? products
        : products.filter((product) => product.category === filter);

    grid.innerHTML = filtered.map((product) => createProductCard(product)).join("");
    empty.hidden = filtered.length > 0;

    buttons.forEach((button) => {
      const isActive = button.dataset.filter === filter;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", String(isActive));
    });

    const nextUrl =
      filter === "vse"
        ? "ponudba.html"
        : `ponudba.html?filter=${encodeURIComponent(filter)}`;
    window.history.replaceState({}, "", nextUrl);
  };

  buttons.forEach((button) => {
    button.addEventListener("click", () => {
      const nextFilter = button.dataset.filter;
      window.sessionStorage.setItem("sweetcraft-offer-filter", nextFilter);
      render(nextFilter);
    });
  });

  const params = new URLSearchParams(window.location.search);
  const requestedFilter = params.get("filter");
  const hashFilter = window.location.hash.replace(/^#/, "");
  const storedFilter = window.sessionStorage.getItem("sweetcraft-offer-filter");
  const initialFilterCandidate = requestedFilter || hashFilter || storedFilter || "vse";
  const initialFilter = buttons.some((button) => button.dataset.filter === initialFilterCandidate)
    ? initialFilterCandidate
    : "vse";

  window.sessionStorage.removeItem("sweetcraft-offer-filter");

  render(initialFilter);
}

function renderProductDetailPage() {
  if (document.body.dataset.page !== "izdelek") return;

  const root = document.querySelector("[data-product-detail]");
  if (!root) return;

  const params = new URLSearchParams(window.location.search);
  const id = params.get("id") || "1";
  const product = products.find((item) => item.id === id);

  if (!product) {
    root.innerHTML = `
      <section class="page-section">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
              <div class="empty-panel">
                <h1>Izdelek ni bil najden</h1>
                <p>Izbrani izdelek ne obstaja več ali pa je povezava napačna.</p>
                <a class="btn btn-primary" href="ponudba.html">Nazaj na ponudbo</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    `;
    return;
  }

  root.innerHTML = `
    <section class="page-section">
      <div class="container">
        <nav aria-label="Drobtinice">
          <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="domov.html">Domov</a></li>
            <li class="breadcrumb-item"><a href="ponudba.html">Ponudba</a></li>
            <li class="breadcrumb-item active" aria-current="page">${product.name}</li>
          </ol>
        </nav>

        <article class="row g-4 g-xl-5 align-items-start">
          <div class="col-lg-6">
            <figure class="detail-media mb-0">
              <img src="${product.image}" alt="${product.name}">
            </figure>
          </div>

          <div class="col-lg-6">
            <div class="detail-copy">
              <div class="rating-row mb-3">
                <span class="stars">★★★★★</span>
                <span>(127 ocen)</span>
              </div>
              <h1>${product.name}</h1>
              <p class="lead mb-4">${product.description}</p>

              <section class="info-card mb-4">
                <h2 class="h3 mb-3">Informacije</h2>
                <dl class="row g-3 mb-0 info-list">
                  <div class="col-sm-4">
                    <dt>Okus</dt>
                    <dd>${product.okus}</dd>
                  </div>
                  <div class="col-sm-4">
                    <dt>Velikost</dt>
                    <dd>${product.velikost}</dd>
                  </div>
                  <div class="col-sm-4">
                    <dt>Priložnost</dt>
                    <dd>${product.priloznost}</dd>
                  </div>
                </dl>
              </section>

              <a class="btn btn-primary w-100 mb-3" href="narocilo.html?product=${product.id}">Naroči ta izdelek</a>

              <aside class="notice">
                <p class="mb-0"><strong>Pomembno:</strong> Vse sladice so narejene po naročilu iz svežih sestavin. Prosimo, naročite vsaj <strong>3 dni vnaprej</strong> za najboljšo kakovost.</p>
              </aside>
            </div>
          </div>
        </article>
      </div>
    </section>
  `;
}

function setupKontaktForm() {
  if (document.body.dataset.page !== "kontakt") return;

  const form = document.querySelector("[data-contact-form]");
  const success = document.querySelector("[data-contact-success]");
  if (!form || !success) return;

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    form.hidden = true;
    success.hidden = false;

    window.setTimeout(() => {
      form.reset();
      form.hidden = false;
      success.hidden = true;
    }, 3000);
  });
}

function setupNarociloForm() {
  if (document.body.dataset.page !== "narocilo") return;

  const form = document.querySelector("[data-order-form]");
  const success = document.querySelector("[data-order-success]");
  const phoneTarget = document.querySelector("[data-order-phone]");
  const emailTarget = document.querySelector("[data-order-email]");
  const phoneLink = document.querySelector("[data-order-phone-link]");
  const emailLink = document.querySelector("[data-order-email-link]");
  const resetButton = document.querySelector("[data-order-reset]");
  const modeField = form?.querySelector("[data-order-mode]");
  const productField = form?.querySelector("[data-order-product]");
  const sizeField = form?.querySelector("[data-order-size]");
  const flavorField = form?.querySelector("[data-order-flavor]");
  const helperField = form?.querySelector("[data-order-helper]");
  const selectionCard = form?.querySelector("[data-order-selection]");
  const selectionName = form?.querySelector("[data-order-selection-name]");
  const selectionFlavor = form?.querySelector("[data-order-selection-flavor]");
  const selectionSize = form?.querySelector("[data-order-selection-size]");
  const selectionOccasion = form?.querySelector("[data-order-selection-occasion]");

  if (
    !form ||
    !success ||
    !phoneTarget ||
    !emailTarget ||
    !phoneLink ||
    !emailLink ||
    !resetButton ||
    !modeField ||
    !productField ||
    !sizeField ||
    !flavorField ||
    !helperField ||
    !selectionCard ||
    !selectionName ||
    !selectionFlavor ||
    !selectionSize ||
    !selectionOccasion
  ) {
    return;
  }

  const orderModeConfig = {
    izdelek: {
      helper:
        "Izberite izdelek iz ponudbe, ki je najbližje vaši želji. Dodatne prilagoditve zapišite spodaj.",
    },
    torta: {
      helper:
        "Izberite torto kot referenco, mi pa jo prilagodimo glede na okus, velikost in dekoracijo.",
    },
    kolacki: {
      helper:
        "Izberite vrsto kolačkov iz ponudbe in dopišite količino ali posebne kombinacije.",
    },
    sezonsko: {
      helper:
        "Izberite sezonsko referenco in dopišite želene prilagoditve ali tematiko dogodka.",
    },
  };

  const params = new URLSearchParams(window.location.search);
  const requestedProduct = products.find((product) => product.id === params.get("product"));
  const defaultMode = requestedProduct
    ? requestedProduct.category === "torte"
      ? "torta"
      : requestedProduct.category
    : "izdelek";

  let lastAutoFlavor = "";

  const getProductsForMode = (mode) => {
    if (mode === "izdelek") return products;

    const category = mode === "torta" ? "torte" : mode;
    return products.filter((product) => product.category === category);
  };

  const getSizeOptions = (mode, filteredProducts, selectedProduct) => {
    const productSizes = filteredProducts.map((product) => product.velikost);
    const modeDefaults = {
      izdelek: ["Po dogovoru"],
      torta: [
        "Majhna (4-6 oseb)",
        "Srednja (8-10 oseb)",
        "Velika (12-15 oseb)",
        "Večnadstropna ali po meri",
      ],
      kolacki: ["15 kosov", "20 kosov", "25 kosov", "Po dogovoru"],
      sezonsko: ["Manjši paket", "Večji paket", "Po dogovoru"],
    };

    return Array.from(
      new Set([
        selectedProduct?.velikost,
        ...productSizes,
        ...modeDefaults[mode],
      ].filter(Boolean)),
    );
  };

  const populateProductOptions = (mode, preferredProductId) => {
    const filteredProducts = getProductsForMode(mode);
    const optionsMarkup = filteredProducts
      .map((product) => {
        const suffix = mode === "izdelek" ? ` (${product.categoryLabel})` : "";
        const selected = preferredProductId === product.id ? " selected" : "";
        return `<option value="${product.id}"${selected}>${product.name}${suffix}</option>`;
      })
      .join("");

    productField.innerHTML = optionsMarkup;

    if (!productField.value && filteredProducts.length > 0) {
      productField.value = filteredProducts[0].id;
    }

    return filteredProducts;
  };

  const populateSizeOptions = (mode, filteredProducts, selectedProduct) => {
    const sizeOptions = getSizeOptions(mode, filteredProducts, selectedProduct);
    sizeField.innerHTML = sizeOptions
      .map((option) => `<option value="${option}">${option}</option>`)
      .join("");

    if (selectedProduct?.velikost) {
      sizeField.value = selectedProduct.velikost;
    }
  };

  const syncOrderState = ({ preferredProductId = null, preserveFlavor = true } = {}) => {
    const mode = modeField.value;
    const filteredProducts = populateProductOptions(mode, preferredProductId);
    const selectedProduct =
      filteredProducts.find((product) => product.id === productField.value) || null;

    helperField.textContent = orderModeConfig[mode].helper;
    populateSizeOptions(mode, filteredProducts, selectedProduct);

    if (selectedProduct) {
      selectionCard.hidden = false;
      selectionName.textContent = selectedProduct.name;
      selectionFlavor.textContent = selectedProduct.okus;
      selectionSize.textContent = selectedProduct.velikost;
      selectionOccasion.textContent = selectedProduct.priloznost;

      if (!preserveFlavor || !flavorField.value || flavorField.value === lastAutoFlavor) {
        flavorField.value = selectedProduct.okus;
        lastAutoFlavor = selectedProduct.okus;
      }
    } else {
      selectionCard.hidden = true;
    }
  };

  const minimumPickupDate = new Date();
  minimumPickupDate.setDate(minimumPickupDate.getDate() + 3);
  const minDate = [
    minimumPickupDate.getFullYear(),
    String(minimumPickupDate.getMonth() + 1).padStart(2, "0"),
    String(minimumPickupDate.getDate()).padStart(2, "0"),
  ].join("-");
  const pickupDateField = form.querySelector("#datumPrevzema");
  if (pickupDateField) {
    pickupDateField.min = minDate;
  }

  modeField.value = defaultMode;
  syncOrderState({
    preferredProductId: requestedProduct?.id || null,
    preserveFlavor: false,
  });

  modeField.addEventListener("change", () => {
    syncOrderState({ preserveFlavor: false });
  });

  productField.addEventListener("change", () => {
    syncOrderState({ preferredProductId: productField.value, preserveFlavor: false });
  });

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const formData = new FormData(form);
    const phone = formData.get("telefon") || siteConfig.phone;
    const email = formData.get("email") || siteConfig.email;
    phoneTarget.textContent = phone;
    emailTarget.textContent = email;
    phoneLink.href = `tel:${String(phone).replace(/\s+/g, "")}`;
    emailLink.href = `mailto:${email}`;
    form.hidden = true;
    success.hidden = false;
  });

  resetButton.addEventListener("click", () => {
    form.reset();
    modeField.value = defaultMode;
    syncOrderState({
      preferredProductId: requestedProduct?.id || null,
      preserveFlavor: false,
    });
    form.hidden = false;
    success.hidden = true;
  });
}

renderHeader();
renderFooter();
renderDomovCollections();
renderPonudbaPage();
renderProductDetailPage();
setupKontaktForm();
setupNarociloForm();
