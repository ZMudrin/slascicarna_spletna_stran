<?php

declare(strict_types=1);

function database_settings(): array
{
    return [
        'host' => 'podatkovna-baza',
        'port' => 3306,
        'dbname' => 'sweetcraft',
        'username' => 'root',
        'password' => 'superVarnoGeslo',
        'charset' => 'utf8mb4',
    ];
}

function connect_database(?string &$statusMessage = null): ?PDO
{
    $settings = database_settings();
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $settings['host'],
        $settings['port'],
        $settings['dbname'],
        $settings['charset'],
    );

    try {
        $pdo = new PDO(
            $dsn,
            $settings['username'],
            $settings['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        );

        $statusMessage = 'Povezava na bazo uspešna.';

        return $pdo;
    } catch (PDOException $exception) {
        $statusMessage = 'Povezava na bazo ni uspela. Preverite, ali sta MySQL in podatkovna baza sweetcraft pravilno vzpostavljena.';

        return null;
    }
}
