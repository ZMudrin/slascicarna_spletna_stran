<?php

declare(strict_types=1);

function default_order_form_data(): array
{
    return [
        'tipNarocila' => '',
        'izdelek' => '',
        'priloznostNarocila' => '',
        'velikost' => '',
        'datumPrevzema' => '',
        'kolicina' => '',
        'okus' => '',
        'opombe' => '',
        'ime' => '',
        'telefon' => '',
        'email' => '',
    ];
}

function process_order_request(?PDO $pdo, string $statusMessage): array
{
    $result = [
        'errors' => [],
        'success' => isset($_GET['success']),
        'statusClass' => $pdo ? 'success' : 'warning',
        'statusMessage' => $statusMessage,
        'data' => default_order_form_data(),
        'successPhone' => $_GET['phone'] ?? '',
        'successEmail' => $_GET['email'] ?? '',
    ];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $result;
    }

    $fields = array_keys($result['data']);
    foreach ($fields as $field) {
        $result['data'][$field] = trim((string) ($_POST[$field] ?? ''));
    }

    $requiredFields = [
        'tipNarocila' => 'Izberite način naročila.',
        'izdelek' => 'Izberite izdelek ali referenco.',
        'priloznostNarocila' => 'Izberite priložnost.',
        'velikost' => 'Izberite velikost ali količino.',
        'datumPrevzema' => 'Izberite datum prevzema.',
        'okus' => 'Vnesite okus ali prilagoditve.',
        'ime' => 'Vnesite ime in priimek.',
        'telefon' => 'Vnesite telefonsko številko.',
        'email' => 'Vnesite e-poštni naslov.',
    ];

    foreach ($requiredFields as $field => $message) {
        if ($result['data'][$field] === '') {
            $result['errors'][] = $message;
        }
    }

    if (
        $result['data']['email'] !== ''
        && !filter_var($result['data']['email'], FILTER_VALIDATE_EMAIL)
    ) {
        $result['errors'][] = 'E-poštni naslov ni v pravilni obliki.';
    }

    if ($pdo === null) {
        $result['errors'][] = 'Shranjevanje trenutno ni mogoče, ker povezava z bazo ni na voljo.';

        return $result;
    }

    if ($result['errors'] !== []) {
        return $result;
    }

    $statement = $pdo->prepare(
        'INSERT INTO order_requests (
            product_id,
            order_mode,
            occasion,
            size_option,
            pickup_date,
            quantity_note,
            flavor_request,
            special_notes,
            customer_name,
            customer_phone,
            customer_email
        ) VALUES (
            :product_id,
            :order_mode,
            :occasion,
            :size_option,
            :pickup_date,
            :quantity_note,
            :flavor_request,
            :special_notes,
            :customer_name,
            :customer_phone,
            :customer_email
        )'
    );

    $statement->execute([
        'product_id' => (int) $result['data']['izdelek'],
        'order_mode' => $result['data']['tipNarocila'],
        'occasion' => $result['data']['priloznostNarocila'],
        'size_option' => $result['data']['velikost'],
        'pickup_date' => $result['data']['datumPrevzema'],
        'quantity_note' => $result['data']['kolicina'] !== '' ? $result['data']['kolicina'] : null,
        'flavor_request' => $result['data']['okus'],
        'special_notes' => $result['data']['opombe'] !== '' ? $result['data']['opombe'] : null,
        'customer_name' => $result['data']['ime'],
        'customer_phone' => $result['data']['telefon'],
        'customer_email' => $result['data']['email'],
    ]);

    $query = http_build_query([
        'success' => 1,
        'phone' => $result['data']['telefon'],
        'email' => $result['data']['email'],
    ]);

    header('Location: narocilo.php?' . $query);
    exit;
}
