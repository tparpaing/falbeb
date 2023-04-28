<?= $renderer->render('header', ['title' => 'Paillardes de famille', 'activeTrigger' => 'paillardes']); ?>

<section class="cards">
    <?php foreach ($paillardes as $p) : ?>
        <div class="courses-container">
            <div class="course">
                <div class="course-preview">
                    <h6 class="authors"><?= $authors[$p->pk_id] ?></a></h6>
                    <h3><?= $p->titre ?></h3>
                    <a href="<?= $router->generateUri('paillardes.show', ['id' => $p->pk_id]) ?>">Lire la paillarde
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

                <div class="course-info">
                    <h6>Reprise de</h6>
                    <p><?= $originals[$p->pk_id] ?></p>
                    <?php if (!empty($p->description)) : ?>
                        <h6>Mot des auteur-e-s</h6>
                        <p><?= nl2br($p->description) ?></p>
                    <?php endif; ?>
                    <a href="<?= $router->generateUri('paillardes.show', ['id' => $p->pk_id]) ?>" class="btn">Voir plus</a>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
</section>

<?= $renderer->render('footer'); ?>