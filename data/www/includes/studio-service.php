<?php

declare(strict_types=1);

function studio_product_form_defaults(): array
{
    return [
        'id' => '',
        'categoryId' => '',
        'name' => '',
        'shortDescription' => '',
        'description' => '',
        'image' => 'slike/',
        'flavor' => '',
        'size' => '',
        'occasion' => '',
    ];
}

function studio_allowed_order_statuses(): array
{
    return ['novo', 'potrjeno', 'v izdelavi', 'pripravljeno', 'zakljuceno'];
}

function studio_find_product_for_edit(array $products, int $productId): ?array
{
    foreach ($products as $product) {
        if ((int) $product['id'] === $productId) {
            return $product;
        }
    }

    return null;
}

function studio_save_product(PDO $pdo, array $input): int
{
    $productId = isset($input['id']) && $input['id'] !== '' ? (int) $input['id'] : null;
    $data = [
        'category_id' => (int) ($input['categoryId'] ?? 0),
        'name' => trim((string) ($input['name'] ?? '')),
        'short_description' => trim((string) ($input['shortDescription'] ?? '')),
        'description' => trim((string) ($input['description'] ?? '')),
        'image_path' => trim((string) ($input['image'] ?? '')),
        'flavor' => trim((string) ($input['flavor'] ?? '')),
        'size_label' => trim((string) ($input['size'] ?? '')),
        'occasion_label' => trim((string) ($input['occasion'] ?? '')),
    ];

    studio_validate_product_payload($pdo, $data);

    if ($productId === null) {
        $statement = $pdo->prepare(
            <<<SQL
            INSERT INTO products (
              category_id,
              name,
              short_description,
              description,
              image_path,
              flavor,
              size_label,
              occasion_label
            ) VALUES (
              :category_id,
              :name,
              :short_description,
              :description,
              :image_path,
              :flavor,
              :size_label,
              :occasion_label
            )
            SQL
        );
        $statement->execute($data);

        return (int) $pdo->lastInsertId();
    }

    $data['product_id'] = $productId;
    $statement = $pdo->prepare(
        <<<SQL
        UPDATE products
        SET
          category_id = :category_id,
          name = :name,
          short_description = :short_description,
          description = :description,
          image_path = :image_path,
          flavor = :flavor,
          size_label = :size_label,
          occasion_label = :occasion_label
        WHERE id = :product_id
        SQL
    );
    $statement->execute($data);

    return $productId;
}

function studio_delete_product(PDO $pdo, int $productId): void
{
    if ($productId <= 0) {
        throw new RuntimeException('Izbrani izdelek ne obstaja.');
    }

    $orderCheck = $pdo->prepare('SELECT COUNT(*) FROM order_requests WHERE product_id = :product_id');
    $orderCheck->execute(['product_id' => $productId]);

    if ((int) $orderCheck->fetchColumn() > 0) {
        throw new RuntimeException('Izdelka ni mogoče izbrisati, ker je že povezan z obstoječimi naročili.');
    }

    $statement = $pdo->prepare('DELETE FROM products WHERE id = :product_id');
    $statement->execute(['product_id' => $productId]);
}

function studio_save_order(PDO $pdo, array $input): void
{
    $orderId = (int) ($input['order_id'] ?? 0);
    $status = trim((string) ($input['status'] ?? ''));
    $pickupDate = trim((string) ($input['pickup_date'] ?? ''));
    $customerName = trim((string) ($input['customer_name'] ?? ''));
    $customerPhone = trim((string) ($input['customer_phone'] ?? ''));
    $customerEmail = trim((string) ($input['customer_email'] ?? ''));
    $specialNotes = trim((string) ($input['special_notes'] ?? ''));

    if ($orderId <= 0) {
        throw new RuntimeException('Izbrano naročilo ne obstaja.');
    }

    if (!in_array($status, studio_allowed_order_statuses(), true)) {
        throw new RuntimeException('Izbran status naročila ni veljaven.');
    }

    if ($pickupDate === '' || strtotime($pickupDate) === false) {
        throw new RuntimeException('Datum prevzema ni veljaven.');
    }

    if ($customerName === '' || $customerPhone === '' || $customerEmail === '') {
        throw new RuntimeException('Za urejanje naročila izpolnite ime, telefon in e-pošto.');
    }

    if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('E-poštni naslov naročila ni veljaven.');
    }

    $statement = $pdo->prepare(
        <<<SQL
        UPDATE order_requests
        SET
          pickup_date = :pickup_date,
          customer_name = :customer_name,
          customer_phone = :customer_phone,
          customer_email = :customer_email,
          special_notes = :special_notes,
          status = :status
        WHERE id = :order_id
        SQL
    );
    $statement->execute([
        'pickup_date' => $pickupDate,
        'customer_name' => $customerName,
        'customer_phone' => $customerPhone,
        'customer_email' => $customerEmail,
        'special_notes' => $specialNotes !== '' ? $specialNotes : null,
        'status' => $status,
        'order_id' => $orderId,
    ]);
}

function studio_delete_order(PDO $pdo, int $orderId): void
{
    $statement = $pdo->prepare('DELETE FROM order_requests WHERE id = :order_id');
    $statement->execute(['order_id' => $orderId]);
}

function studio_validate_product_payload(PDO $pdo, array $data): void
{
    foreach ($data as $key => $value) {
        if ($key !== 'category_id' && trim((string) $value) === '') {
            throw new RuntimeException('Za shranjevanje izdelka izpolnite vsa polja.');
        }
    }

    if ((int) $data['category_id'] <= 0) {
        throw new RuntimeException('Izberite veljavno kategorijo izdelka.');
    }

    $categoryCheck = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE id = :category_id');
    $categoryCheck->execute(['category_id' => $data['category_id']]);

    if ((int) $categoryCheck->fetchColumn() === 0) {
        throw new RuntimeException('Izbrana kategorija ne obstaja.');
    }
}
