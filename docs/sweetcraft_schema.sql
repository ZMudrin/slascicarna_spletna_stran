CREATE DATABASE IF NOT EXISTS sweetcraft
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sweetcraft;

CREATE TABLE IF NOT EXISTS categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  short_description VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  flavor VARCHAR(100) NOT NULL,
  size_label VARCHAR(100) NOT NULL,
  occasion_label VARCHAR(150) NOT NULL,
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS order_requests (
  id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  order_mode ENUM('izdelek', 'torta', 'kolacki', 'sezonsko') NOT NULL,
  occasion VARCHAR(100) NOT NULL,
  size_option VARCHAR(100) NOT NULL,
  pickup_date DATE NOT NULL,
  quantity_note VARCHAR(120) NULL,
  flavor_request VARCHAR(150) NOT NULL,
  special_notes TEXT NULL,
  customer_name VARCHAR(150) NOT NULL,
  customer_phone VARCHAR(30) NOT NULL,
  customer_email VARCHAR(150) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

INSERT INTO categories (id, slug, name, description) VALUES
  (1, 'torte', 'Torte', 'Torte po meri za posebne priložnosti.'),
  (2, 'kolacki', 'Kolački', 'Sveži kolački in piškoti za različne dogodke.'),
  (3, 'sezonsko', 'Sezonsko', 'Izbor sezonskih sladic in prazničnih dobrot.')
ON DUPLICATE KEY UPDATE
  slug = VALUES(slug),
  name = VALUES(name),
  description = VALUES(description);

INSERT INTO products (
  id, category_id, name, short_description, description, image_path, flavor, size_label, occasion_label
) VALUES
  (1, 1, 'Čokoladna torta', 'Bogata čokoladna torta z ganache prevleko', 'Bogata čokoladna torta z ganache prevleko, idealna za vse ljubitelje čokolade. Naša značilna torta je izdelana iz kakovostnih belgijskih čokolad in sveže belgijske smetane. Vsaka plast je natančno izdelana in napolnjena s kremno čokoladno kremo.', 'slike/plates-with-sweets.jpg', 'Čokolada', 'Srednja (8-10 oseb)', 'Rojstni dan, poroka, obletnica'),
  (2, 1, 'Jagodna torta', 'Sveža torta s sezonskimi jagodami', 'Sveža torta s sezonskimi jagodami in kremno vaniljevo kremo. Lahka in osvežilna, popolna za poletna praznovanja. Jagode izbiramo iz lokalnih kmetij za najboljši okus in svežino.', 'slike/cafe-sweets.jpg', 'Jagoda, vanilija', 'Velika (12-15 oseb)', 'Poletna zabava, rojstni dan'),
  (3, 1, 'Poročna torta', 'Elegantna večnadstropna torta', 'Elegantna večnadstropna torta, prilagojena vašim željam. Okrašena je z ročno izdelanimi detajli in cvetličnimi elementi. Vsaka poročna torta je edinstvena in ustvarjena posebej za vaš pomemben dan.', 'slike/big-cake.jpg', 'Po meri', 'Po meri', 'Poroka'),
  (4, 2, 'Vaniljevi kolački', 'Klasični masleni kolački z vanilijo', 'Klasični masleni kolački z vanilijo. Hrustljavi in zlato rjavi, popolni za različne priložnosti. Pečeni so po tradicionalnem družinskem receptu, ki ga hranimo že generacije.', 'slike/stacked-cookies.jpg', 'Vanilija', '20 kosov', 'Družinsko srečanje, darilo'),
  (5, 2, 'Čokoladni piškoti', 'Hrustljavi piškoti z čokoladnimi koščki', 'Hrustljavi piškoti z bogatimi čokoladnimi koščki. Popolna kombinacija hrustljavosti in mehkobe z velikodušno mero čokolade v vsakem piškotu.', 'slike/baked-cookies.jpg', 'Čokolada', '15 kosov', 'Popoldanski prigrizek, darilo'),
  (6, 2, 'Mandljevi kolački', 'Božanski kolački z mandlji', 'Nežni kolački z mandlji in prijetno aromo domače peke. Odlično se podajo ob kavi, čaju ali kot elegantno sladko darilo.', 'slike/seasonal-sweet.jpg', 'Mandelj', '18 kosov', 'Pogostitev, praznovanje'),
  (7, 3, 'Božični kolački', 'Tradicionalni praznični kolački', 'Tradicionalni praznični kolački, pečeni po starem receptu. Začinjeni so s cimetom, klinčki in ingverjem, zato v dom takoj prinesejo pravo praznično vzdušje.', 'slike/gingerbread-cookies.jpg', 'Začimbe, med', '25 kosov', 'Božič, novoletno praznovanje'),
  (8, 3, 'Sezonske sladice', 'Sladice za pomladno praznovanje', 'Izbor sezonskih sladic, prilagojenih letnemu času in praznikom. Idealna izbira za dogodke, kjer želite nekaj svežega, igrivega in drugačnega.', 'slike/muffins.jpg', 'Po izboru', 'Po dogovoru', 'Tematski dogodki, prazniki')
ON DUPLICATE KEY UPDATE
  category_id = VALUES(category_id),
  name = VALUES(name),
  short_description = VALUES(short_description),
  description = VALUES(description),
  image_path = VALUES(image_path),
  flavor = VALUES(flavor),
  size_label = VALUES(size_label),
  occasion_label = VALUES(occasion_label);
