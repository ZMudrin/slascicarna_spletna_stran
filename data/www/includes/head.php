<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$pageId = $pageId ?? '';
$pageTitle = $pageTitle ?? 'SweetCraft';
$pageDescription = $pageDescription ?? 'SweetCraft spletna stran';
?>
<!doctype html>
<html lang="sl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= h($pageDescription) ?>" />
    <title><?= h($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= h(asset_path('css/style.css')) ?>" />
  </head>
  <body class="d-flex flex-column min-vh-100"<?= $pageId !== '' ? ' data-page="' . h($pageId) . '"' : '' ?>>
    <?php require __DIR__ . '/header.php'; ?>
