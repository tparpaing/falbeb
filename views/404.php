<?php

$nearestPage = $params['nearestPage'];

echo $renderer->render(
    'header',
    [
        'params' => [
            'no_nav' => true,
            'no_container' => true,
            'styles' => ['404.css'],
            'scripts' => ['404.js'],
            'style_strict' => true
        ],
        'title' => 'Erreur 404 - Page non trouvée'
    ]
); ?>

<div class="moon"></div>
<div class="moon__crater moon__crater1"></div>
<div class="moon__crater moon__crater2"></div>
<div class="moon__crater moon__crater3"></div>

<div class="star star1"></div>
<div class="star star2"></div>
<div class="star star3"></div>
<div class="star star4"></div>
<div class="star star5"></div>

<div class="error">
    <div class="error__title">404</div>
    <div class="error__subtitle">Hmmm...</div>
    <div class="error__description">
        On dirait qu'un développeur s'est endormi en codant cette page car elle n'a pas trouvée !
        <?php if ($nearestPage !== null) : ?>
            <p>Vouliez vous aller à la page <a class="error__description__link" href="<?= $router->generateUri($nearestPage['path']) ?>"><?= htmlentities($nearestPage['title']) ?></a> ?</p>
        <?php endif; ?>
    </div>
    <a class="error__button error__button--active" href="<?= $router->generateUri('index') ?>">ACCUEIL</a>
    <?php if ($nearestPage !== null) : ?>
        <a class="error__button" href="<?= $router->generateUri($nearestPage['path']) ?>"><?= htmlentities($nearestPage['title']) ?></a>
    <?php endif; ?>
</div>

<div class="astronaut">
    <div class="astronaut__backpack"></div>
    <div class="astronaut__body"></div>
    <div class="astronaut__body__chest"></div>
    <div class="astronaut__arm-left1"></div>
    <div class="astronaut__arm-left2"></div>
    <div class="astronaut__arm-right1"></div>
    <div class="astronaut__arm-right2"></div>
    <div class="astronaut__arm-thumb-left"></div>
    <div class="astronaut__arm-thumb-right"></div>
    <div class="astronaut__leg-left"></div>
    <div class="astronaut__leg-right"></div>
    <div class="astronaut__foot-left"></div>
    <div class="astronaut__foot-right"></div>
    <div class="astronaut__wrist-left"></div>
    <div class="astronaut__wrist-right"></div>

    <div class="astronaut__cord">
        <canvas id="cord" height="500px" width="500px"></canvas>
    </div>

    <div class="astronaut__head">
        <canvas id="visor" width="60px" height="60px"></canvas>
        <div class="astronaut__head-visor-flare1"></div>
        <div class="astronaut__head-visor-flare2"></div>
    </div>
</div>

<?= $renderer->render(
    'footer',
    [
        'params' => [
            'no_footer' => true,
            'no_container' => true
        ]
    ]
); ?>