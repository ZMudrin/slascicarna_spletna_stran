<?php

declare(strict_types=1);

function start_studio_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name('sweetcraft_studio');
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function studio_password_value(): string
{
    return 'sweetcraft';
}

function studio_is_authenticated(): bool
{
    start_studio_session();

    return ($_SESSION['studio_authenticated'] ?? false) === true;
}

function studio_attempt_login(string $password): bool
{
    start_studio_session();

    $isValid = hash_equals(studio_password_value(), trim($password));

    if ($isValid) {
        $_SESSION['studio_authenticated'] = true;
    }

    return $isValid;
}

function studio_logout(): void
{
    start_studio_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function studio_set_flash(string $type, string $message): void
{
    start_studio_session();
    $_SESSION['studio_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function studio_consume_flash(): ?array
{
    start_studio_session();

    $flash = $_SESSION['studio_flash'] ?? null;
    unset($_SESSION['studio_flash']);

    return is_array($flash) ? $flash : null;
}
