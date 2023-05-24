<?= $renderer->render('header', ['title' => 'Bonjour ' . $auth->prenom, 'activeTrigger' => 'profile']); ?>

<section>
    Nom : <?= $auth->nom ?><br>
    Prénom : <?= $auth->prenom ?>
</section>

<?= $renderer->render('footer'); ?>