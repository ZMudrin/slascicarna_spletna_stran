export const siteConfig = {
  name: "SweetCraft",
  tagline: "Ročno izdelane sladice",
  city: "Maribor",
  country: "Slovenija",
  phone: "031 234 567",
  email: "info@sweetcraft.si",
};

export const navItems = [
  { id: "domov", label: "Domov", href: "domov.html" },
  { id: "ponudba", label: "Ponudba", href: "ponudba.html" },
  { id: "o-nas", label: "O nas", href: "o-nas.html" },
  { id: "kontakt", label: "Kontakt", href: "kontakt.html" },
  { id: "narocilo", label: "Naročilo", href: "narocilo.html" },
];

export const products = [
  {
    id: "1",
    name: "Čokoladna torta",
    shortDescription: "Bogata čokoladna torta z ganache prevleko",
    description:
      "Bogata čokoladna torta z ganache prevleko, idealna za vse ljubitelje čokolade. Naša značilna torta je izdelana iz kakovostnih belgijskih čokolad in sveže belgijske smetane. Vsaka plast je natančno izdelana in napolnjena s kremno čokoladno kremo.",
    category: "torte",
    categoryLabel: "Torte",
    image: "slike/plates-with-sweets.jpg",
    okus: "Čokolada",
    velikost: "Srednja (8-10 oseb)",
    priloznost: "Rojstni dan, poroka, obletnica",
  },
  {
    id: "2",
    name: "Jagodna torta",
    shortDescription: "Sveža torta s sezonskimi jagodami",
    description:
      "Sveža torta s sezonskimi jagodami in kremno vaniljevo kremo. Lahka in osvežilna, popolna za poletna praznovanja. Jagode izbiramo iz lokalnih kmetij za najboljši okus in svežino.",
    category: "torte",
    categoryLabel: "Torte",
    image: "slike/cafe-sweets.jpg",
    okus: "Jagoda, vanilija",
    velikost: "Velika (12-15 oseb)",
    priloznost: "Poletna zabava, rojstni dan",
  },
  {
    id: "3",
    name: "Poročna torta",
    shortDescription: "Elegantna večnadstropna torta",
    description:
      "Elegantna večnadstropna torta, prilagojena vašim željam. Okrašena je z ročno izdelanimi detajli in cvetličnimi elementi. Vsaka poročna torta je edinstvena in ustvarjena posebej za vaš pomemben dan.",
    category: "torte",
    categoryLabel: "Torte",
    image: "slike/big-cake.jpg",
    okus: "Po meri",
    velikost: "Po meri",
    priloznost: "Poroka",
  },
  {
    id: "4",
    name: "Vaniljevi kolački",
    shortDescription: "Klasični masleni kolački z vanilijo",
    description:
      "Klasični masleni kolački z vanilijo. Hrustljavi in zlato rjavi, popolni za različne priložnosti. Pečeni so po tradicionalnem družinskem receptu, ki ga hranimo že generacije.",
    category: "kolacki",
    categoryLabel: "Kolački",
    image: "slike/stacked-cookies.jpg",
    okus: "Vanilija",
    velikost: "20 kosov",
    priloznost: "Družinsko srečanje, darilo",
  },
  {
    id: "5",
    name: "Čokoladni piškoti",
    shortDescription: "Hrustljavi piškoti z čokoladnimi koščki",
    description:
      "Hrustljavi piškoti z bogatimi čokoladnimi koščki. Popolna kombinacija hrustljavosti in mehkobe z velikodušno mero čokolade v vsakem piškotu.",
    category: "kolacki",
    categoryLabel: "Kolački",
    image: "slike/baked-cookies.jpg",
    okus: "Čokolada",
    velikost: "15 kosov",
    priloznost: "Popoldanski prigrizek, darilo",
  },
  {
    id: "6",
    name: "Mandljevi kolački",
    shortDescription: "Božanski kolački z mandlji",
    description:
      "Nežni kolački z mandlji in prijetno aromo domače peke. Odlično se podajo ob kavi, čaju ali kot elegantno sladko darilo.",
    category: "kolacki",
    categoryLabel: "Kolački",
    image: "slike/seasonal-sweet.jpg",
    okus: "Mandelj",
    velikost: "18 kosov",
    priloznost: "Pogostitev, praznovanje",
  },
  {
    id: "7",
    name: "Božični kolački",
    shortDescription: "Tradicionalni praznični kolački",
    description:
      "Tradicionalni praznični kolački, pečeni po starem receptu. Začinjeni so s cimetom, klinčki in ingverjem, zato v dom takoj prinesejo pravo praznično vzdušje.",
    category: "sezonsko",
    categoryLabel: "Sezonsko",
    image: "slike/gingerbread-cookies.jpg",
    okus: "Začimbe, med",
    velikost: "25 kosov",
    priloznost: "Božič, novoletno praznovanje",
  },
  {
    id: "8",
    name: "Sezonske sladice",
    shortDescription: "Sladice za pomladno praznovanje",
    description:
      "Izbor sezonskih sladic, prilagojenih letnemu času in praznikom. Idealna izbira za dogodke, kjer želite nekaj svežega, igrivega in drugačnega.",
    category: "sezonsko",
    categoryLabel: "Sezonsko",
    image: "slike/muffins.jpg",
    okus: "Po izboru",
    velikost: "Po dogovoru",
    priloznost: "Tematski dogodki, prazniki",
  },
];

export const productCategories = [
  {
    id: "torte",
    title: "Torte po meri",
    description: "Edinstvene torte, prilagojene vašim željam",
    image: products[0].image,
  },
  {
    id: "kolacki",
    title: "Kolački",
    description: "Hrustljavi in sveži, vsak dan",
    image: products[4].image,
  },
  {
    id: "sezonsko",
    title: "Sezonske sladice",
    description: "Posebne sladice za vsako priložnost",
    image: products[6].image,
  },
];

export const featuredProducts = products.filter((product) =>
  ["1", "2", "4", "7"].includes(product.id),
);
