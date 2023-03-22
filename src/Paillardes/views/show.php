<?= $renderer->render('header', ['title' => 'Paillardes de famille', 'activeTrigger' => 'paillardes']); ?>

<h1><?= $paillarde->titre ?></h1>
<p class="returnToMain">
    <a href="<?= $router->generateUri('paillardes.index') ?>">
        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
        <span>Retour à la liste des paillardes</span>
    </a>
</p>

<section class="cards">
    <div class="courses-container">
        <div class="course">
            <div class="course-preview">
                <h6 class="authors"><?= $authors ?></h6>
                <h3><?= $paillarde->titre ?></h3>
                <h6>Reprise de</h6>
                <p><?= $original ?></p>
                <?php if (!empty($paillarde->description)) : ?>
                    <h6>Mot des auteur-e-s</h6>
                    <p><?= nl2br($paillarde->description) ?></p>
                <?php endif; ?>
            </div>

            <div class="course-info">
                <p><?= nl2br($paroles) ?></p>
            </div>

        </div>
    </div>
</section>

<?= $renderer->render('footer'); ?>