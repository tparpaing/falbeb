<?= $renderer->render(
    'header',
    [
        'params' => [
            'styles' => ['tree.css'],
            'scripts' => ['tree.js']
        ],
        'title' => 'Arbre de famille',
        'activeTrigger' => 'tree'
    ]
); ?>

<?= $renderer->render('footer'); ?>