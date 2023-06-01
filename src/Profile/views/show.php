<?php

use Framework\App;

if (App::getLoggedUserId(1) == $member->pk_id) {
    $title = 'Bonjour ' . $member->prenom;
} else {
    if (in_array(substr($member->surnom,0,1),$VOWELS,true)) {
        $title = 'Profil d\'{' . $member->surnom . '}';
    } else {
        $title = 'Profil de {' . $member->surnom . '}';
    }
}



echo $renderer->render('header', ['title' => $title, 'activeTrigger' => 'profile']); ?>

<section>
    <div class="profile_container">
    <div class="profile_info">
            <div class="icon"><i class="<?= $member->symbole ?>"></i></div>
            <p class="text"><?= $member->g_nom ?></p>
        </div>
        <?php if ($member->tva === 1): ?>
        <div class="profile_info">
            <div class="icon"><i class="fa-solid fa-user-graduate"></i></div>
            <p class="text">Très Vénérable Ancien-ne</p>
        </div>
        <?php endif; ?>
        <?php if (!empty($member->prenom)): ?>
        <div class="profile_info">
            <div class="icon">Prénom :</div>
            <p class="text"><?= $member->prenom ?><br></p>
        </div>
        <?php endif; ?>
        <?php if (!empty($member->nom)): ?>
        <div class="profile_info">
            <div class="icon">Nom :</div>
            <p class="text"><?= $member->nom ?><br></p>
        </div>
        <?php endif; ?>
    </div>

    <?php if(!is_null($pms)): ?>
        <br><br>P/M :
        <div class="chip_container">
        <?php foreach($pms as $pm): ?>
            <a href="<?= $router->generateUri('profile.show', ['id' => $pm->fk_pere]) ?>">
                <div class="chip">
                    <div class="icon"><i class="<?= $pm->symbole ?>"></i></div>
                    <p class="text"><?= $pm->surnom ?></p>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if(!is_null($fillots)): ?>
        <br><br>Fillot-te-s :
        <div class="chip_container">
        <?php foreach($fillots as $f): ?>
            <a href="<?= $router->generateUri('profile.show', ['id' => $f->fk_fils]) ?>">
                <div class="chip">
                    <div class="icon"><i class="<?= $f->symbole ?>"></i></div>
                    <p class="text"><?= $f->surnom ?></p>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if(!is_null($adelphes)): ?>
        <br><br>Adelphes (frères et soeurs) :
        <div class="chip_container">
        <?php foreach($adelphes as $a): ?>
            <a href="<?= $router->generateUri('profile.show', ['id' => $a->pk_id]) ?>">
                <div class="chip">
                    <div class="icon"><i class="fa-solid fa-user-group"></i></div>
                    <p class="text"><?= $a->surnom ?></p>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?= $renderer->render('footer'); ?>