<?php

declare(strict_types=1);

function fetch_categories(?PDO $pdo): array
{
    if ($pdo === null) {
        return [];
    }

    $statement = $pdo->query(
        'SELECT id, slug, name, description FROM categories ORDER BY name'
    );

    return $statement->fetchAll();
}

function fetch_products(?PDO $pdo): array
{
    if ($pdo === null) {
        return [];
    }

    $statement = $pdo->query(
        <<<SQL
        SELECT
          p.id,
          p.category_id,
          p.name,
          p.short_description,
          p.description,
          p.image_path,
          p.flavor,
          p.size_label,
          p.occasion_label,
          c.slug AS category_slug,
          c.name AS category_name,
          c.description AS category_description
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        ORDER BY c.name, p.name
        SQL
    );

    return array_map('normalize_product_record', $statement->fetchAll());
}

function fetch_featured_products(?PDO $pdo, array $featuredIds = [1, 2, 4, 7]): array
{
    $products = fetch_products($pdo);
    if ($products === []) {
        return [];
    }

    $byId = [];
    foreach ($products as $product) {
        $byId[(int) $product['id']] = $product;
    }

    $featured = [];
    foreach ($featuredIds as $productId) {
        if (isset($byId[$productId])) {
            $featured[] = $byId[$productId];
        }
    }

    if ($featured !== []) {
        return $featured;
    }

    return array_slice($products, 0, 4);
}

function fetch_product_by_id(?PDO $pdo, int $productId): ?array
{
    if ($pdo === null) {
        return null;
    }

    $statement = $pdo->prepare(
        <<<SQL
        SELECT
          p.id,
          p.category_id,
          p.name,
          p.short_description,
          p.description,
          p.image_path,
          p.flavor,
          p.size_label,
          p.occasion_label,
          c.slug AS category_slug,
          c.name AS category_name,
          c.description AS category_description
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.id = :product_id
        LIMIT 1
        SQL
    );
    $statement->execute(['product_id' => $productId]);

    $product = $statement->fetch();

    return $product ? normalize_product_record($product) : null;
}

function fetch_category_cards(?PDO $pdo): array
{
    $categories = fetch_categories($pdo);
    $products = fetch_products($pdo);
    $previewByCategory = [];

    foreach ($products as $product) {
        $slug = $product['category'];
        if (!isset($previewByCategory[$slug])) {
            $previewByCategory[$slug] = $product['image'];
        }
    }

    return array_map(
        static function (array $category) use ($previewByCategory): array {
            return [
                'id' => $category['slug'],
                'title' => $category['name'],
                'description' => $category['description'],
                'image' => $previewByCategory[$category['slug']] ?? 'slike/plates-with-sweets.jpg',
            ];
        },
        $categories
    );
}

function fetch_related_products(?PDO $pdo, int $categoryId, int $excludeProductId, int $limit = 3): array
{
    if ($pdo === null) {
        return [];
    }

    $statement = $pdo->prepare(
        <<<SQL
        SELECT
          p.id,
          p.category_id,
          p.name,
          p.short_description,
          p.description,
          p.image_path,
          p.flavor,
          p.size_label,
          p.occasion_label,
          c.slug AS category_slug,
          c.name AS category_name,
          c.description AS category_description
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.category_id = :category_id AND p.id <> :exclude_id
        ORDER BY p.name
        LIMIT :result_limit
        SQL
    );
    $statement->bindValue('category_id', $categoryId, PDO::PARAM_INT);
    $statement->bindValue('exclude_id', $excludeProductId, PDO::PARAM_INT);
    $statement->bindValue('result_limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    return array_map('normalize_product_record', $statement->fetchAll());
}

function fetch_order_requests(?PDO $pdo): array
{
    if ($pdo === null) {
        return [];
    }

    $statement = $pdo->query(
        <<<SQL
        SELECT
          o.id,
          o.product_id,
          o.order_mode,
          o.occasion,
          o.size_option,
          o.pickup_date,
          o.quantity_note,
          o.flavor_request,
          o.special_notes,
          o.customer_name,
          o.customer_phone,
          o.customer_email,
          o.status,
          o.created_at,
          o.updated_at,
          p.name AS product_name,
          c.slug AS category_slug,
          c.name AS category_name
        FROM order_requests o
        INNER JOIN products p ON p.id = o.product_id
        INNER JOIN categories c ON c.id = p.category_id
        ORDER BY o.created_at DESC, o.id DESC
        SQL
    );

    return $statement->fetchAll();
}

function fetch_dashboard_metrics(?PDO $pdo): array
{
    $metrics = [
        'products' => 0,
        'categories' => 0,
        'orders' => 0,
        'newOrders' => 0,
    ];

    if ($pdo === null) {
        return $metrics;
    }

    $metrics['products'] = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $metrics['categories'] = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    $metrics['orders'] = (int) $pdo->query('SELECT COUNT(*) FROM order_requests')->fetchColumn();

    $newOrdersStatement = $pdo->prepare(
        "SELECT COUNT(*) FROM order_requests WHERE status = 'novo'"
    );
    $newOrdersStatement->execute();
    $metrics['newOrders'] = (int) $newOrdersStatement->fetchColumn();

    return $metrics;
}

function normalize_product_record(array $product): array
{
    return [
        'id' => (int) $product['id'],
        'categoryId' => (int) $product['category_id'],
        'name' => $product['name'],
        'shortDescription' => $product['short_description'],
        'description' => $product['description'],
        'image' => $product['image_path'],
        'flavor' => $product['flavor'],
        'size' => $product['size_label'],
        'occasion' => $product['occasion_label'],
        'category' => $product['category_slug'],
        'categoryLabel' => $product['category_name'],
        'categoryDescription' => $product['category_description'],
    ];
}

function product_exists(PDO $pdo, int $productId): bool
{
    $statement = $pdo->prepare('SELECT COUNT(*) FROM products WHERE id = :product_id');
    $statement->execute(['product_id' => $productId]);

    return (int) $statement->fetchColumn() > 0;
}
