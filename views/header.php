<?php

use Framework\App;
use Framework\ViewParams;

if (!isset($activeTrigger) || empty($activeTrigger)) {
    $activeTrigger = "/";
}

$viewParams = new ViewParams($params, $router);
$viewParams->setParamsDefaultVal();

$styles = $viewParams->getStylesHTML($router);
$scripts = $viewParams->getScriptsHTML($router);

$noNav = $viewParams->getParam('no_nav');
$noContainer = $viewParams->getParam('no_container');
$noFooter = $viewParams->getParam('no_footer') ?? 0;
$styleStrict = $viewParams->getParam('style_strict');

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!$styleStrict) : ?>
        <link rel="stylesheet" href="<?= $router->generateUri('styles') ?>/app.css" />
    <?php endif; ?>
    <?= $styles ?>

    <?= $scripts ?>

    <title><?= $title ?>Faluchologie de la Bite en Bois</title>
</head>

<body>

    <?php if (!$noNav) : ?>
        <nav>
            <div class="menu">
                <p class="website_name"><?= $displayTitle ?></p>
                <div class="menu_links">
                    <a class="link <?php if ($activeTrigger === 'index') : ?>link-active<?php endif; ?>" href="<?= $router->generateUri('index') ?>">Accueil</a>
                    <?php foreach ($navLinks as $link) : ?>
                        <a class="link <?php if ($activeTrigger === $link['name']) : ?>link-active<?php endif; ?>" href="<?= $router->generateUri($link['name'] . '.' . $link['baseRoute']) ?>"><?= $link['displayName'] ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="menu_icon">
                    <span class="icon"></span>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <?php if (!$noContainer) : ?>
        <div class="container" data-no-footer="<?= $noFooter ?>">
        <?php endif; ?>