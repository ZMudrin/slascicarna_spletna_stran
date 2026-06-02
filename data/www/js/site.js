function parseEmbeddedJson(id, fallback = []) {
  const node = document.getElementById(id);
  if (!node) return fallback;

  try {
    return JSON.parse(node.textContent || "[]");
  } catch (error) {
    return fallback;
  }
}

function setupPonudbaPage() {
  if (document.body.dataset.page !== "ponudba") return;

  const buttons = Array.from(document.querySelectorAll("[data-filter]"));
  const cards = Array.from(document.querySelectorAll("[data-product-card]"));
  const emptyState = document.querySelector("[data-empty-state]");

  if (buttons.length === 0 || cards.length === 0 || !emptyState) {
    return;
  }

  const availableFilters = new Set(buttons.map((button) => button.dataset.filter));

  const render = (filter) => {
    let visibleCount = 0;

    cards.forEach((card) => {
      const matches = filter === "vse" || card.dataset.category === filter;
      card.hidden = !matches;
      if (matches) visibleCount += 1;
    });

    emptyState.hidden = visibleCount > 0;

    buttons.forEach((button) => {
      const isActive = button.dataset.filter === filter;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", String(isActive));
    });

    const nextUrl =
      filter === "vse"
        ? "ponudba.php"
        : `ponudba.php?filter=${encodeURIComponent(filter)}`;
    window.history.replaceState({}, "", nextUrl);
  };

  buttons.forEach((button) => {
    button.addEventListener("click", () => {
      render(button.dataset.filter || "vse");
    });
  });

  const requestedFilter = new URLSearchParams(window.location.search).get("filter") || "vse";
  render(availableFilters.has(requestedFilter) ? requestedFilter : "vse");
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

  const products = parseEmbeddedJson("order-products-data");
  const form = document.querySelector("[data-order-form]");
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
    !modeField ||
    !productField ||
    !sizeField ||
    !flavorField ||
    !helperField ||
    !selectionCard ||
    !selectionName ||
    !selectionFlavor ||
    !selectionSize ||
    !selectionOccasion ||
    products.length === 0
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

  const productById = new Map(products.map((product) => [String(product.id), product]));
  const params = new URLSearchParams(window.location.search);
  const requestedProduct = productById.get(params.get("product") || "");
  const initialModeValue = form.dataset.initialMode;
  const initialProductValue = form.dataset.initialProduct;
  const initialSizeValue = form.dataset.initialSize;
  const initialFlavorValue = form.dataset.initialFlavor;
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
    const productSizes = filteredProducts.map((product) => product.size);
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
      new Set([selectedProduct?.size, ...productSizes, ...modeDefaults[mode]].filter(Boolean)),
    );
  };

  const populateProductOptions = (mode, preferredProductId) => {
    const filteredProducts = getProductsForMode(mode);

    productField.innerHTML = filteredProducts
      .map((product) => {
        const suffix = mode === "izdelek" ? ` (${product.categoryLabel})` : "";
        const selected = preferredProductId === String(product.id) ? " selected" : "";
        return `<option value="${product.id}"${selected}>${product.name}${suffix}</option>`;
      })
      .join("");

    if (!productField.value && filteredProducts.length > 0) {
      productField.value = String(filteredProducts[0].id);
    }

    return filteredProducts;
  };

  const populateSizeOptions = (mode, filteredProducts, selectedProduct, preferredSize = "") => {
    const sizeOptions = getSizeOptions(mode, filteredProducts, selectedProduct);
    sizeField.innerHTML = sizeOptions
      .map((option) => `<option value="${option}">${option}</option>`)
      .join("");

    if (preferredSize && sizeOptions.includes(preferredSize)) {
      sizeField.value = preferredSize;
    } else if (selectedProduct?.size) {
      sizeField.value = selectedProduct.size;
    }
  };

  const syncOrderState = ({
    preferredProductId = null,
    preferredSize = "",
    preferredFlavor = "",
    preserveFlavor = true,
  } = {}) => {
    const mode = modeField.value;
    const filteredProducts = populateProductOptions(mode, preferredProductId);
    const selectedProduct =
      filteredProducts.find((product) => String(product.id) === productField.value) || null;

    helperField.textContent = orderModeConfig[mode]?.helper || orderModeConfig.izdelek.helper;
    populateSizeOptions(mode, filteredProducts, selectedProduct, preferredSize);

    if (selectedProduct) {
      selectionCard.hidden = false;
      selectionName.textContent = selectedProduct.name;
      selectionFlavor.textContent = selectedProduct.flavor;
      selectionSize.textContent = selectedProduct.size;
      selectionOccasion.textContent = selectedProduct.occasion;

      if (preferredFlavor) {
        flavorField.value = preferredFlavor;
        lastAutoFlavor = preferredFlavor;
      } else if (!preserveFlavor || !flavorField.value || flavorField.value === lastAutoFlavor) {
        flavorField.value = selectedProduct.flavor;
        lastAutoFlavor = selectedProduct.flavor;
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

  modeField.value = initialModeValue || defaultMode;
  syncOrderState({
    preferredProductId: initialProductValue || String(requestedProduct?.id || ""),
    preferredSize: initialSizeValue || "",
    preferredFlavor: initialFlavorValue || "",
    preserveFlavor: false,
  });

  modeField.addEventListener("change", () => {
    syncOrderState({ preserveFlavor: false });
  });

  productField.addEventListener("change", () => {
    syncOrderState({
      preferredProductId: productField.value,
      preserveFlavor: false,
    });
  });
}

setupPonudbaPage();
setupKontaktForm();
setupNarociloForm();
