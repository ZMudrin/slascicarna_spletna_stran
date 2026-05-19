<?php

declare(strict_types=1);

$siteConfig = [
    'name' => 'SweetCraft',
    'tagline' => 'Ročno izdelane sladice',
    'city' => 'Maribor',
    'country' => 'Slovenija',
    'phone' => '031 234 567',
    'email' => 'info@sweetcraft.si',
    'address' => [
        'line1' => 'Gosposka ulica 12',
        'line2' => '2000 Maribor',
        'line3' => 'Slovenija',
    ],
];

$navItems = [
    ['id' => 'domov', 'label' => 'Domov', 'href' => 'domov.php'],
    ['id' => 'ponudba', 'label' => 'Ponudba', 'href' => 'ponudba.php'],
    ['id' => 'o-nas', 'label' => 'O nas', 'href' => 'o-nas.php'],
    ['id' => 'kontakt', 'label' => 'Kontakt', 'href' => 'kontakt.php'],
    ['id' => 'narocilo', 'label' => 'Naročilo', 'href' => 'narocilo.php'],
];

$assetVersion = '20260519';

if (!function_exists('h')) {
    function h(null|string|int|float $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('asset_path')) {
    function asset_path(string $path): string
    {
        global $assetVersion;

        $separator = str_contains($path, '?') ? '&' : '?';

        return $path . $separator . 'v=' . rawurlencode((string) $assetVersion);
    }
}
