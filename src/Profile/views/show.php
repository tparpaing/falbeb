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
        <div class="profile_icon"><i class="fa-solid fa-info"></i></div>
        <h3 class="title">Infos</h3>
        <div class="content">
            <?php if (!empty($member->prenom) || !empty($member->nom)): ?>
            <div class="profile_info">
                <div class="title">
                    <div class="icon"><i class="fa-regular fa-address-book"></i></div>
                    <div class="text">Identite</div>
                </div>
                <div class="content"><?php if (!empty($member->prenom)): ?><span class="capitalize"><?= $member->prenom ?></span><?php endif; ?>&nbsp;<?php if (!empty($member->nom)): ?><span class="uppercase"><?= $member->nom ?></span><?php endif; ?></div>
            </div>
            <?php endif; ?>
            <?php if (!is_null($bapt)): ?>
            <div class="profile_info">
                <div class="title">
                    <div class="icon"><i class="fa-solid fa-user-pen"></i></div>
                    <div class="text">Surnom</div>
                </div>
                <div class="content"><?= $member->surnom ?></div>
            </div>
            <?php endif; ?>
            <div class="profile_info">
                <div class="title">
                    <div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div class="text">Grade</div>
                </div>
                <div class="content"><i class="<?= $member->symbole ?>"></i><?= $member->g_nom ?></div>
            </div>
            <?php if (!is_null($status)): ?>
            <div class="profile_info">
                <div class="title">
                    <div class="icon"><i class="fa-solid fa-certificate"></i></div>
                    <div class="text">Statut Faluchard</div>
                </div>
                <div class="content"><?= $status ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!is_null($bapt)): ?>
    <div class="profile_container">
        <div class="profile_icon"><i class="fa-solid fa-hat-wizard"></i></div>
        <h3 class="title">Bapteme</h3>
        <div class="content">
            <div class="profile_info">
                <div class="title">
                    <div class="icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <div class="text">Date</div>
                </div>
                <div class="content"><?= $bapt['date'] ?></div>
            </div>
            <?php if (!empty($bapt['lieu']) && !is_null($bapt['lieu'])): ?>
            <div class="profile_info">
                <div class="title">
                    <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="text">Lieu</div>
                </div>
                <div class="content"><?= $bapt['lieu'] ?></div>
            </div>
            <?php endif; ?>
            <?php if (!is_null($paillarde)): ?>
            <div class="profile_info">
                <div class="title">
                    <div class="icon"><i class="fa-solid fa-microphone-lines"></i></div>
                    <div class="text">Paillarde</div>
                </div>
                <div class="content"><a href="<?= $router->generateUri('paillardes.show', ['id' => $paillarde['id']]) ?>"><?= $paillarde['title'] ?></a></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!is_null($pms)): ?>
    <div class="profile_container">
        <div class="profile_icon"><i class="fa-solid fa-person-chalkboard"></i></div>
        <h3 class="title">Parrains / Marraines</h3>
        <div class="content">
            <?php foreach($pms as $pm): ?>
            <div class="chip">
                <a href="<?= $router->generateUri('profile.show', ['id' => $pm->fk_pere]) ?>">
                    <div class="icon"><i class="<?= $pm->symbole ?>"></i></div>
                    <p class="text"><?= $pm->surnom ?></p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="profile_legend_icon"><i class="fa-regular fa-circle-question"></i></div>
        <div class="profile_legend">
            <div class="title">Légende</div>
            <?php foreach($legend['pms'] as $l): ?>
            <div class="content">
                <div class="content-icon"><i class="<?= $l['symbole'] ?>"></i></div>
                <p class="content-text"><?= $l['nom'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!is_null($fillots)): ?>
    <div class="profile_container">
        <div class="profile_icon"><i class="fa-solid fa-graduation-cap"></i></div>
        <h3 class="title">Fillot-te-s</h3>
        <div class="chip_container">
            <?php foreach($fillots as $f): ?>
            <div class="chip">
                <a href="<?= $router->generateUri('profile.show', ['id' => $f->fk_fils]) ?>">
                    <div class="icon"><i class="<?= $f->symbole ?>"></i></div>
                    <p class="text"><?= $f->surnom ?></p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="profile_legend_icon"><i class="fa-regular fa-circle-question"></i></div>
        <div class="profile_legend">
            <div class="title">Légende</div>
            <?php foreach($legend['fillots'] as $l): ?>
            <div class="content">
                <div class="content-icon"><i class="<?= $l['symbole'] ?>"></i></div>
                <p class="content-text"><?= $l['nom'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!is_null($adelphes)): ?>
    <div class="profile_container">
        <div class="profile_icon"><i class="fa-solid fa-people-arrows"></i></div>
        <h3 class="title">Adelphes</h3>
        <div class="chip_container">
            <?php foreach($adelphes as $a): ?>
            <div class="chip">
                <a href="<?= $router->generateUri('profile.show', ['id' => $a->pk_id]) ?>">
                    <div class="icon"><i class="fa-solid fa-user-group"></i></div>
                    <p class="text"><?= $a->surnom ?></p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<?= $renderer->render('footer'); ?>