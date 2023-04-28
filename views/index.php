<?php 

use Framework\App;

echo $renderer->render('header', ['activeTrigger' => 'index']); ?>

<?php if (App::loggedIn()): ?>
    <h1>Bonjour <?= $_SESSION['auth']->prenom ?> !</h1>
    <p><a href="<?= $router->generateUri('auth.logout') ?>">Me déconnecter</a></p>
    <p><a href="<?= $router->generateUri('profile.index') ?>">Voir mon profil</a></p>
<?php else: ?>
    <h1>Bonjour</h1>
    <p>Veuillez <a href="<?= $router->generateUri('auth.login') ?>">vous connecter</a> pour continuer</p>
<?php endif; ?>

<?= $renderer->render('footer'); ?>