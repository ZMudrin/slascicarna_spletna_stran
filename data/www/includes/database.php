<?php

declare(strict_types=1);

function database_settings(): array
{
    return [
        'hosts' => [
            ['host' => getenv('DB_HOST') ?: 'podatkovna-baza', 'port' => (int) (getenv('DB_PORT') ?: 3306)],
            ['host' => 'mysql', 'port' => 3306],
            ['host' => '127.0.0.1', 'port' => 3307],
            ['host' => 'localhost', 'port' => 3307],
        ],
        'dbname' => 'sweetcraft',
        'username' => 'root',
        'password' => 'superVarnoGeslo',
        'charset' => 'utf8mb4',
    ];
}

function create_server_connection(array $settings, string $host, int $port): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=%d;charset=%s',
        $host,
        $port,
        $settings['charset'],
    );

    return new PDO(
        $dsn,
        $settings['username'],
        $settings['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    );
}

function connect_database(?string &$statusMessage = null): ?PDO
{
    $settings = database_settings();
    $lastException = null;

    for ($attempt = 1; $attempt <= 4; $attempt++) {
        foreach ($settings['hosts'] as $candidate) {
            try {
                $pdo = create_server_connection(
                    $settings,
                    (string) $candidate['host'],
                    (int) $candidate['port'],
                );
                $databaseName = '`' . str_replace('`', '``', $settings['dbname']) . '`';

                $pdo->exec(
                    'CREATE DATABASE IF NOT EXISTS ' . $databaseName
                    . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
                );
                $pdo->exec('USE ' . $databaseName);

                ensure_database_schema($pdo);

                $statusMessage = 'Povezava na bazo uspešna.';

                return $pdo;
            } catch (PDOException $exception) {
                $lastException = $exception;
            }
        }

        usleep(350000);
    }

    $statusMessage = 'Povezava na bazo ni uspela. Preverite, ali sta MySQL in podatkovna baza sweetcraft pravilno vzpostavljena.';

    return null;
}

function ensure_database_schema(PDO $pdo): void
{
    $pdo->exec(
        <<<SQL
        CREATE TABLE IF NOT EXISTS categories (
          id INT PRIMARY KEY AUTO_INCREMENT,
          slug VARCHAR(50) NOT NULL UNIQUE,
          name VARCHAR(100) NOT NULL,
          description VARCHAR(255) NOT NULL
        )
        SQL
    );

    $pdo->exec(
        <<<SQL
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
        )
        SQL
    );

    $pdo->exec(
        <<<SQL
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
          status ENUM('novo', 'potrjeno', 'v izdelavi', 'pripravljeno', 'zakljuceno') NOT NULL DEFAULT 'novo',
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          CONSTRAINT fk_orders_product
            FOREIGN KEY (product_id) REFERENCES products(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
        )
        SQL
    );

    ensure_table_column(
        $pdo,
        'order_requests',
        'status',
        "ALTER TABLE order_requests ADD COLUMN status ENUM('novo', 'potrjeno', 'v izdelavi', 'pripravljeno', 'zakljuceno') NOT NULL DEFAULT 'novo' AFTER customer_email"
    );
    ensure_table_column(
        $pdo,
        'order_requests',
        'updated_at',
        'ALTER TABLE order_requests ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at'
    );

    seed_categories($pdo);
    seed_products($pdo);
}

function ensure_table_column(PDO $pdo, string $table, string $column, string $alterSql): void
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name'
    );
    $statement->execute([
        'table_name' => $table,
        'column_name' => $column,
    ]);

    if ((int) $statement->fetchColumn() === 0) {
        $pdo->exec($alterSql);
    }
}

function seed_categories(PDO $pdo): void
{
    $pdo->exec(
        <<<SQL
        INSERT INTO categories (id, slug, name, description) VALUES
          (1, 'torte', 'Torte', 'Torte po meri za posebne priložnosti.'),
          (2, 'kolacki', 'Kolački', 'Sveži kolački in piškoti za različne dogodke.'),
          (3, 'sezonsko', 'Sezonsko', 'Izbor sezonskih sladic in prazničnih dobrot.')
        ON DUPLICATE KEY UPDATE
          slug = VALUES(slug),
          name = VALUES(name),
          description = VALUES(description)
        SQL
    );
}

function seed_products(PDO $pdo): void
{
    $pdo->exec(
        <<<SQL
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
          occasion_label = VALUES(occasion_label)
        SQL
    );
}
