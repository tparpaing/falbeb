<?php

if (in_array(substr($member->surnom,0,1),$VOWELS,true)) {
    $title = 'Profil d\'{' . $member->surnom . '}';
} else {
    $title = 'Profil de {' . $member->surnom . '}';
}

echo $renderer->render('header', ['title' => $title, 'activeTrigger' => 'profile']); ?>

<section>
    Nom : <?= $member->nom ?><br>
    Prénom : <?= $member->prenom ?>
</section>

<?= $renderer->render('footer'); ?>